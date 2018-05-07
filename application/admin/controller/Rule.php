<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\admin\model\Rule as RuleModel;
use app\admin\validate\Rule as Rulecheck;

/**
 * 权限管理控制器
 * Class Rule
 * @package app\admin\controller
 */
class Rule extends AdminBase
{
    //权限列表
    public function index() {
        $rules = $this->menus(false);
        $this->assign(['rules' => $rules]);
        return $this->fetch();
    }

    //添加权限
    public function add()
    {
        $ruleModel = new RuleModel;

        if ($this->request->isAjax()) {
            $data['rule_name']   = $this->request->post('name');
            $data['rule_method'] = $this->request->post('path');
            $data['rule_pid']    = $this->request->post('pid');
            $data['rule_pid']    = $data['rule_pid'] ? $data['rule_pid'] : 0;
            $data['rule_status'] = $this->request->post('status');
            $data['rule_sort']   = $this->request->post('order');
            $type                = $this->request->post('type');

            if ($ruleModel->where(['rule_name' => $data['rule_name'], 'rule_method' => $data['rule_method']])->find()) {
                return $this->ajaxResponse('规则已存在！');
            }

            $validate = new Rulecheck;

            if ($type != 'ctrl') {
                if (!$validate->scene('notctrl')->check($data)) {
                    return $this->ajaxResponse($validate->getError());
                }
            } else {
                $data['rule_icon'] = $this->request->post('icon');

                if (!$validate->check($data)) {
                    return $this->ajaxResponse($validate->getError());
                }
            }

            if ($ruleModel->addRule($data, $type)) {
                return $this->ajaxResponse('ok', 1000);
            } else {
                return $this->ajaxResponse('添加数据失败！');
            }
        } else {
            $type           = $this->request->param('type');
            $parent_rule_id = $this->request->param('parent_rule_id');
            $parent_rule_id = $parent_rule_id ? $parent_rule_id : 0;

            switch ($type) {
                case 'ctrl':
                    $typename = '控制器';
                    break;
                case 'method':
                    $typename = '方法';
                    break;
                case 'action':
                    $typename = '行为';
                    break;
            }

            if ($parent_rule_id) {
                $parent_rule_name = $ruleModel->where('rule_id', $parent_rule_id)->value('rule_name');
            }

            $this->assign(
                [
                    'type'             => $type,
                    'typename'         => $typename,
                    'parent_rule_id'   => $parent_rule_id,
                    'parent_rule_name' => $parent_rule_name
                ]
            );
           return $this->fetch();
        }
    }

    //编辑权限
    public function edit()
    {
        $ruleModel = new RuleModel;

        if ($this->request->isAjax()) {
            $data                = array();
            $data['rule_name']   = $this->request->post('name');
            $data['rule_method'] = $this->request->post('path');
            $data['rule_status'] = $this->request->post('status');
            $data['rule_sort']   = $this->request->post('order');
            $rule_id             = $this->request->post('rule_id');;
            $type   = $this->request->post('type');
            $where = [
                'rule_name' => $data['rule_name'],
                'rule_method' => $data['rule_method'],
                'rule_id' => ['NEQ', $rule_id],
            ];

            if ($ruleModel->where($where)->find()) {
                return $this->ajaxResponse('规则已存在！');
            }

            $validate = new Rulecheck;

            if ($type != 'ctrl') {
                if (!$validate->scene('notctrl')->check($data)) {
                    return $this->ajaxResponse($validate->getError());
                }
            } else {
                $data['rule_icon'] = $this->request->post('icon');

                if (!$validate->check($data)) {
                    return $this->ajaxResponse($validate->getError());
                }
            }

            if ($ruleModel->editRule($rule_id, $data)) {
                return $this->ajaxResponse('ok', 1000);
            } else {
                return $this->ajaxResponse('编辑规则失败！');
            }
        } else {
            $type    = $this->request->param('type');
            $rule_id = $this->request->param('rule_id', 0);

            switch ($type) {
                case 'ctrl':
                    $typename = '控制器';
                    break;
                case 'method':
                    $typename = '方法';
                    break;
                case 'action':
                    $typename = '行为';
                    break;
            }

            $where = [
                'rule_id' => $rule_id,
                'rule_status' => ['GT', 0],
            ];

            $rule_info = $ruleModel->where($where)->find();

            if (!$rule_info) {
                $this->error('规则不存在！');
            } else {
                $parent_rule_info = $ruleModel->field('rule_id,rule_name')->where('rule_id', $rule_info['rule_pid'])->find();
            }

            $this->assign([
                'rule_info'        => $rule_info,
                'type'             => $type,
                'typename'         => $typename,
                'parent_rule_id'   => $parent_rule_info['rule_id'],
                'parent_rule_name' => $parent_rule_info['rule_name']
            ]);

            return $this->fetch();
        }
    }

    //删除权限
    public function delete()
    {
        if ($this->request->isAjax()) {
            $rule_id   = $this->request->post('rule_id');
            $ruleModel = new RuleModel;
            $is_son = $ruleModel->where('rule_pid', $rule_id)->select();

            if (objectToArray($is_son)) {
               return $this->ajaxResponse('请先删除子权限！');
            } else {
                if ($ruleModel->where('rule_id', $rule_id)->limit(1)->delete()) {
                    return $this->ajaxResponse('ok', 1000);
                } else {
                    return $this->ajaxResponse('删除权限失败！');
                }
            }
        }
    }
}