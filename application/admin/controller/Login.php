<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Config;
use think\Log;
use app\admin\model\Admin as AdminModel;
use think\Db;

/**
 * 登陆控制器
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{
    public function index()
    {
        if ($this->request->isPost()) {
            $validate_arr['admin_name'] = $this->request->post('admin_name');
            $validate_arr['password'] = $this->request->post('password');
            $validate = $this->validate($validate_arr, 'Login');

            if ($validate !== true) {
                return $this->ajaxResponse($validate);
            }

            $admin_info = AdminModel::get(['admin_name' =>  $validate_arr['admin_name']]);

            if ($admin_info) {
                if ($admin_info->status != 1) {
                    return $this->ajaxResponse('账号已被屏蔽，请联系管理员！');
                }

                $password = md5(sha1(md5($validate_arr['password'])));

                if ($password == $admin_info->admin_password) {
                    $admin_info->last_login_time = time();
                    $admin_info->last_login_ip = $_SERVER['REMOTE_ADDR'];
                    $admin_info->checklogin = random_code();

                    if (!$admin_info->save()) {
                        Log::init([
                            'type'  =>  'File',
                            'path'  =>  getRootPath().Config::get('ADMIN_LOGIN_LOG'),
                        ]);
                        Log::write('更新管理员登陆信息失败，原因是：'.$admin_info->getError().'\r\n');
                    }

                    Session::set('_admin_name', $admin_info->admin_name);
                    Session::set('_admin_pass', $admin_info->admin_password);
                    Session::set('_check_login', $admin_info->checklogin);
                    return $this->ajaxResponse('登陆成功！', 1000);
                } else {
                    return $this->ajaxResponse('密码错误！');
                }
            } else {
                return $this->ajaxResponse('账号不存在！');
            }
        } else {
            if (Session::get('_admin_name')) {
                return $this->redirect('Index/index');
            } else {
                return $this->fetch();
            }
        }
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

    //退出登陆
    public function logout()
    {
        Session::set('_admin_name', NULL);
        Session::set('_admin_pass', NULL);
        Session::set('_check_login', NULL);
        return $this->ajaxResponse('ok', 1000);
    }
}