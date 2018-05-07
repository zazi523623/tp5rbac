<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\admin\model\Group as GroupModel;
use app\admin\model\Rule as RuleModel;
use app\admin\validate\Group as GroupCheck;
use think\Db;

/**
 * 权限组控制器
 * Class Group
 * @package app\admin\controller
 */
class Group extends AdminBase
{
    /**
     * 权限组列表
     */
    public function index()
    {
        $group_name = $this->request->get('group_name');
        $status = $this->request->get('status');
        $groupModel = new GroupModel;
        $groups = $groupModel->getGroupList($group_name, $status);
        $this->assign([
            'groups' => $groups,
        ]);
        return $this->fetch();
    }

    /**
     * 添加或编辑权限组
     */
    public function operate()
    {
        $groupModel = new GroupModel;

        if ($this->request->isAjax()) {
            $action = $this->request->post('action');
            $data['role_name'] = $this->request->post('name');
            $data['role_status'] = $this->request->post('status');
            $validate = new GroupCheck;

            if (!$validate->check($data)) {
                return $this->ajaxResponse($validate->getError());
            }

            if ($action == 'add') {
                if ($groupModel->data($data)->save()) {
                    return $this->ajaxResponse('ok', 1000);
                } else {
                    return $this->ajaxResponse('添加数据失败！');
                }
            } else {
                $role_id = $this->request->post('val_id');

                if ($groupModel->save($data, ['role_id' => $role_id])) {
                    return $this->ajaxResponse('ok', 1000);
                } else {
                    return $this->ajaxResponse('更新数据失败！');
                }
            }
        } else {
            $action = $this->request->param('action');
            $info = [];

            if ($action == 'add') {
                $action_name = '添加权限组';
            } else {
                $role_id = $this->request->param('val_id');
                $info = $groupModel->where('role_id', $role_id)->find();
                $action_name = '编辑权限组';
            }

            $this->assign([
                'action' => $action,
                'action_name' => $action_name,
                'info' => $info,
            ]);
            return $this->fetch();
        }
    }

    /**
     * 删除权限组
     */
    public function delete()
    {
        if ($this->request->isAjax()) {
            $role_id = $this->request->post('val_id');
            $groupModel = new GroupModel;

            if ($groupModel->where('role_id', $role_id)->limit(1)->delete()) {
               return $this->ajaxResponse('ok', 1000);
            } else {
                return $this->ajaxResponse('删除失败');
            }
        }
    }

    //启用或禁用
    public function yesOrNo()
    {
        if ($this->request->isAjax()) {
            $role_id = $this->request->post('val_id');
            $groupModel = new GroupModel;
            $where = ['role_id' => $role_id];
            $status = $groupModel->where($where)->value('role_status');

            if ($status == 1) {
                //禁用
                if ($groupModel->save(['role_status' => 2], $where)) {
                    return $this->ajaxResponse('no', 1000);
                }
            } else {
                //启用
                if ($groupModel->save(['role_status' => 1], $where)) {
                    return $this->ajaxResponse('yes', 1000);
                }
            }
        }
    }

    //添加用户成员
    public function user()
    {
        $groupModel = new GroupModel;
        $adminAndRoleModel = Db::name('admin_and_role');

        if ($this->request->isAjax()) {
            $admin_ids = $this->request->post('admin_ids');
            $role_id = $this->request->post('type');

            if (!preg_match('/^\d+,?(\d+,?)*$/', $admin_ids)) {
                return $this->ajaxResponse('id格式错误！');
            }

            if (!isInteger($role_id)) {
                return $this->ajaxResponse('缺少参数或参数错误！');
            }

            $role_status = $groupModel->where('role_id', $role_id)->value('role_status');

            if (!$role_status || $role_status != 1) {
                return $this->ajaxResponse('权限组不存在或被禁用！');
            }

            $adminModel = Db::name('admin');
            $admin_ids_array = explode(',', $admin_ids);
            $groupModel->startTrans();
            $flag = true;

            for ($i = 0; $i < count($admin_ids_array); $i++) {
                if (!$adminModel->where('admin_id', $admin_ids_array[$i])->value('admin_id')) {
                    $msg = 'id'.$admin_ids_array[$i].'不存在！';
                    $flag = false;
                    break;
                }

                $old_role_id = $adminAndRoleModel->where('admin_id', $admin_ids_array[$i])->value('role_id');

                if ($old_role_id) {
                    $msg = 'id'.$admin_ids_array[$i].'已存在权限组，请先移除再添加！';
                    $flag = false;
                    break;
                } else {
                    $data = [
                        'admin_id' => $admin_ids_array[$i],
                        'role_id' => $role_id
                    ];

                    if (!$adminAndRoleModel->insert($data)) {
                        $msg = '添加数据失败！';
                        $flag = false;
                        break;
                    }

                    if (!$adminModel->where('admin_id', $admin_ids_array[$i])->update(['level' => $role_id])) {
                        $msg = '更新管理员数据失败！';
                        $flag = false;
                        break;
                    }
                }
            }

            if ($flag) {
                $groupModel->commit();
                return $this->ajaxResponse('ok', 1000);
            } else {
                $groupModel->rollback();
                return $this->ajaxResponse($msg);
            }
        } else {
            $role_id = $this->request->param('val_id');
            $grpup_name = $groupModel->where('role_id', $role_id)->value('role_name');
            $groupUserList = $groupModel->getGroupUserList($role_id);
            $this->assign([
                'group_name' => $grpup_name,
                'groupUserList' => $groupUserList
            ]);
            return $this->fetch();
        }
    }

    //移除用户成员
    public function removeUser()
    {
        if ($this->request->isAjax()) {
            $admin_id = $this->request->post('val_id');

            if (!isInteger($admin_id)) {
                return $this->ajaxResponse('缺少参数或参数错误！');
            }

            $adminAndRoleModel = Db::name('admin_and_role');
            $adminModel = Db::name('admin');

            if ($adminAndRoleModel->where('admin_id', $admin_id)->value('role_id') == 1) {
                $count = $adminAndRoleModel->where('role_id', 1)->count();

                if ($count <= 1 ) {
                    return $this->ajaxResponse('超级管理员必须存在一个！');
                }
            }

            $adminAndRoleModel->startTrans();
            $flag = true;

            if (!$adminAndRoleModel->where('admin_id', $admin_id)->limit(1)->delete()) {
                $msg = '移除权限组失败！';
                $flag = false;
            }

            if (!$adminModel->where('admin_id', $admin_id)->limit(1)->update(['level' => 0])) {
                $msg = '更新管理员数据失败！';
                $flag = false;
            }

            if ($flag) {
                $adminAndRoleModel->commit();
                return $this->ajaxResponse('ok', 1000);
            } else {
                $adminAndRoleModel->rollback();
                return $this->ajaxResponse($msg);
            }
        }
    }

    //访问授权
    public function authorize()
    {
        $groupModel = new GroupModel;
        $ruleModel = new RuleModel;

        if ($this->request->isAjax()) {
            $group_id = $this->request->post('group_id');
            $ids = $this->request->post('ids');

            if (!isInteger($group_id)) {
                return $this->ajaxResponse('缺少参数或参数错误！');
            }

            if (!preg_match('/^\d+,?(\d+,?)*$/', $ids)) {
                return $this->ajaxResponse('参数格式不正确！');
            }

            if ($groupModel->save(['rule_ids' => $ids], ['role_id' => $group_id])) {
                return $this->ajaxResponse('ok', 1000);
            } else {
                return $this->ajaxResponse('保存失败！');
            }
        } else {
            $role_id = $this->request->param('val_id');

            if (!isInteger($role_id)) {
                $this->error('缺少参数或参数错误！');
            }

            //获取权限组拥有的权限
            $roleRuleStr = $groupModel->where('role_id', $role_id)->value('rule_ids');
            $roleRuleArray = explode(',', $roleRuleStr);
            $rules = $ruleModel->getRuleList();
            $this->assign(array(
                'rules' => $rules,
                'roleRuleArray' => $roleRuleArray
            ));

            return $this->fetch();
        }
    }
}