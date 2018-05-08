<?php
namespace app\common\controller;

use think\Cache;
use think\Controller;
use think\Session;
use think\Db;

/**
 * 后台基础控制器
 * Class AdminBase
 * @package app\common\controller
 */
class AdminBase extends Controller
{
    //管理员信息
    protected $admin_info;
    //当前访问路径
    protected $path;
    //拥有权限用于动态按钮
    protected $my_rules;

    public function _initialize()
    {
        $admin_name = Session::get('_admin_name');
        $admin_pass = Session::get('_admin_pass');
        $check_login = Session::get('_check_login');
        //防止缺少变量报错
        $this->assign('parent_path', '');

        if (!$admin_name || !$admin_pass) {
            $this->Response('请登录！', '/admin');
        }

        $admin_info = Db::name('admin')->where('admin_name', $admin_name)->find();

        if (!$admin_info || $admin_info['status'] != 1) {
            $this->Response('账号异常，请重新登陆！', '/admin');
        }

        if ($admin_info['admin_password'] != $admin_pass) {
            $this->Response('密码已更改，请重新登陆！', '/admin');
        }

        if ($check_login != $admin_info['checklogin']) {
            $this->Response('该账号已在其他地方登陆，请重新登陆！', '/admin');
        }

        if ($admin_info['level'] < 1) {
            $this->Response('不属任何权限组，请联系管理员！', '/admin');
        }

        //当前访问路径
        $action = $this->request->param('action');

        if ($action) {
            $this->path = $this->request->controller().'/'.$this->request->action().'/action/'.$action;
        } else {
            $this->path = $this->request->controller().'/'.$this->request->action();
        }

        //获取上级路径
        $adminRuleModel = Db::name('admin_rule');
        $rule_pid_where = [
            'rule_method' => ['EQ', $this->path],
            'rule_status' => 1,
        ];
        $rule_pid = $adminRuleModel
                    ->where($rule_pid_where)
                    ->value('rule_pid');
        $rule_path_where = [
            'rule_level' => ['NEQ', 1],
            'rule_status' => 1,
            'rule_id' => $rule_pid,
        ];
        $parent_path = $adminRuleModel
                       ->where($rule_path_where)
                       ->value('rule_method');
        $this->admin_info = $admin_info;
        $menus = Cache::get('admin_menus');

        if (!$menus) {
            $menus = $this->menus();
            Cache::set('admin_menus', $menus);
        }

        $this->my_rules = serialize($menus);

        if ($this->admin_info['level'] !=1 ) {
            if (!stripos($this->my_rules, $this->path)) {
                return $this->Response('无权限访问！');
            }
        }

        $admin_info['group_name'] = Db::name('admin_role')->where('role_id', $admin_info['level'])->value('role_name');
        $this->assign([
            'admin_info' =>$admin_info,
            'parent_path' => $parent_path,
            'menus' => $menus,//菜单
            'navs' => $this->navs(),//导航
        ]);
    }

    /**
     * 返回数据格式
     * @param string $msg 提示信息
     * @param integer $code 状态码 1000 成功 2000 失败
     * @param array $data 数据集
     * @return json
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    protected function ajaxResponse($msg = '', $code = 2000, $data = [])
    {
        if ($data) {
            $rsult['msg'] = $msg;
            $rsult['code'] = $code;
            $rsult['data'] = $data;
        } else {
            $rsult['msg'] = $msg;
            $rsult['code'] = $code;
        }

        return $rsult;
    }

    /**
     * 响应函数
     * @param string $msg 提示信息
     * @param string $url 跳转url或状态码
     * @return json
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    protected function Response($msg = '', $url = 2000)
    {
        if ($this->request->isAjax()) {
            header('Content-Type: application/json');
            exit(json_encode($this->ajaxResponse($msg, $url)));
        } else {
            if ($url == 2000) {
                $this->error($msg);
            } else {
                $this->error($msg, $url);
            }
        }
    }

    /**
     * 获取菜单
     * @param boolean true 取出状态正常数据 false 取出所有数据
     * @param string $url 跳转url
     * @return json
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    protected function menus($type = true)
    {
        $rulesModel = Db::name('admin_rule');

        if ($type) {
            $where = ['rule_status' => 1];
        } else {
            $where = [];
        }

        $order = 'rule_sort desc';

        if ($this->admin_info['level'] != 1) {
            $rule_ids = Db::name('admin_and_role')
                        ->alias('a')
                        ->join('admin_role ar', 'a.role_id = ar.role_id')
                        ->where('a.admin_id', $this->admin_info['admin_id'])
                        ->value('rule_ids');
            $where['rule_id'] = ['IN', $rule_ids];
            $rules = $rulesModel->where($where)->order($order)->select();
        } else {
            $rules = $rulesModel->where($where)->order($order)->select();
        }

        $data = [];

        foreach ($rules as $k => $v) {
            $data[$v['rule_id']] = $v;
        }

        return tree($data);
    }

    //获取导航
    protected function navs($pid = 0)
    {
        static $navs = [];
        $filed = 'rule_name,rule_pid,rule_icon,rule_method,rule_level';

        if ($pid) {
            $where = ['rule_id' => $pid, 'rule_status' => 1];
        } else {
            $where = ['rule_method' => $this->path, 'rule_status' => 1];
        }

        $rule_info = Db::name('admin_rule')
                     ->field($filed)
                     ->where($where)
                     ->find();

        if ($rule_info['rule_pid']) {
            $navs[] = $rule_info;
            $this->navs($rule_info['rule_pid']);
        } else {
            $navs[] = $rule_info;
        }

        return array_reverse($navs);
    }
}