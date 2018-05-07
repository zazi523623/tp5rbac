<?php
namespace  app\admin\model;

use app\common\model\AdminBase;
use think\Db;

/**
 * 管理员模型
 * Class Manger
 * @package app\admin\model.
 */
class Manager extends AdminBase
{
    protected $name = 'admin';

    /**
     * 获取管理员列表
     * @param integer $admin_lnfo 当前管理员信息
     * @param integer $admin_id 管理员id
     * @param string $admin_nickname 管理员昵称
     * @param integer $status 管理员状态
     * @return array $data 数据集
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function getAdminList($admin_lnfo = 0, $admin_id = 0, $admin_nickname = '', $status = 0)
    {
        if ($admin_nickname) {
            $where['a.admin_nickname'] = ['LIKE', "%{$admin_nickname}%"];
        }

        if ($admin_lnfo['level'] != 1) {
            if ($admin_id) {
                $pid = $this->where('admin_id', $admin_id)->value('pid');

                if ($pid == $admin_lnfo['admin_id']) {
                    $where['a.admin_id'] = $admin_lnfo['admin_id'];
                } else {
                    $this->error = '只能查看自己的子管理员！';
                    return false;
                }
            } else {
                $where['a.pid'] = $admin_lnfo['admin_id'];
            }
        } else {
            if ($admin_id) {
                $where['a.admin_id'] = $admin_id;
            }
        }

        if ($status) {
            $where['a.status'] = $status;
        } else {
            $where['a.status'] = ['GT', 0];
        }

        $result = DB::name('admin')
                  ->alias('a')
                  ->field('a.*,ar.role_id')
                  ->join('admin_and_role ar', 'a.admin_id = ar.admin_id', 'left')
                  ->where($where)
                  ->order('a.register_time desc,a.admin_id desc')
                  ->select();
        $roleModel = Db::name('admin_role');

        foreach ($result as $k => $v) {
            $result[$k]['role_name'] = $roleModel->where('role_id', $v['role_id'])->value('role_name');
        }

        return $result?$result:false;
    }
}