<?php
namespace app\admin\model;

use app\common\model\AdminBase;
use think\Db;

/**
 * 权限组模型
 * Class Group
 * @package app\admin\model
 */
class Group extends AdminBase
{
    protected $name = 'admin_role';

    /**
     * 获取权限组列表
     * @param string $group_name 组名
     * @param integer $status 状态
     * @return array $data 数据集
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function getGroupList($group_name = '', $status = 0)
    {
        if ($group_name) {
            $where['role_name'] = ['LIKE', "%{$group_name}%"];
        }

        if ($status) {
            $where['role_status'] = $status;
        } else {
            $where['role_status'] = ['GT', 0];
        }

        $result = $this
                  ->where($where)
                  ->order('role_id desc')
                  ->paginate(10);
        return $result?$result:false;
    }

    /**
     * 获取权限组成员列表
     * @param integer $role_id 权限组id
     * @return array $data 数据集
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function getGroupUserList($role_id = 0)
    {
        $adminAndRoleModel = Db::name('admin_and_role');
        $field = 'ar.admin_id,a.admin_name,a.last_login_time,a.last_login_ip,a.status';
        $result = $adminAndRoleModel
                  ->alias('ar')
                  ->field($field)
                  ->join('admin a', 'ar.admin_id = a.admin_id')
                  ->where('ar.role_id', $role_id)
                  ->order('ar.admin_id desc')
                  ->paginate(10);
        return $result;
    }
}