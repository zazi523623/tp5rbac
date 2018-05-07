<?php
namespace app\admin\model;

use app\common\model\AdminBase;

/**
 * 权限模型
 * Class Rule
 * @package app\amdin\model
 */
class Rule extends AdminBase
{
    protected $name = 'admin_rule';

    /**
     * 获取规则列表
     * @param string $ids 规则id
     * @param integer $pid 父id
     * @return array $data 数据集
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function getRuleList($ids = '', $pid = 0)
    {
        $where = [];

        if ($pid) {
            $where['rule_pid'] = $pid;
        }

        if ($ids) {
            $where['rule_id'] = ['IN', $ids];
            $where['rule_level'] = ['NEQ', 3];
        }

        $result = $this
                  ->field('rule_id,rule_name,rule_pid,rule_level')
                  ->where($where)
                  ->order('rule_sort desc,rule_id desc')
                  ->select();

        foreach ($result as $k => $v) {
            $v->rule_level = str_repeat('---', $v->rule_level);
            $data[$v->rule_id]['rule_id'] = $v->rule_id;
            $data[$v->rule_id]['rule_name'] = $v->rule_name;
            $data[$v->rule_id]['rule_pid'] = $v->rule_pid;
            $data[$v->rule_id]['rule_level'] = $v->rule_level;
        }

        $data = $this->getTree($data);
        return $data?$data:false;
    }

    /**
     * 获取树形数组
     * @param array $data 原始数据数组(索引必须为数据的id)
     * @return array $tree 格式化的树状数组
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function getTree($data)
    {
        $tree = array();

        foreach ($data as $v) {
            if (!empty($data[$v['rule_pid']])) {
                $data[$v['rule_pid']]['son'][] = &$data[$v['rule_id']];
            } else {
                $tree[] = &$data[$v['rule_id']];
            }
        }

        return $tree;
    }

    /**
     * 添加规则
     * @param array $data 数据集
     * @param string $type 规则类型用于区分等级
     * @return array $result code 1000 成功 2000失败 msg信息
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function addRule($data = array(), $type = '')
    {
        switch ($type) {
            case 'ctrl':
                $data['rule_level'] = 1;
                break;
            case 'method':
                $data['rule_level'] = 2;
                break;
            case 'action':
                $data['rule_level'] = 3;
                break;
        }

        if ($this->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 编辑规则
     * @param  integer $rule_id 规则id
     * @param array $data 数据集
     * @return array $result code 1000 成功 2000失败 msg信息
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    public function editRule($rule_id = 0, $data = array())
    {
        $this->startTrans();
        $flag = true;

        if ($data['rule_status']) {
            $sonRuleIds = $this->getSonRules($rule_id);

            if ($sonRuleIds) {
                $this->where(['rule_id' => ['IN', $sonRuleIds]])->update(['rule_status' => $data['rule_status']]);
            }
        }

        if (!$this->where('rule_id', $rule_id)->update($data)) {
            $flag = false;
        }

        if ($flag) {
            $this->commit();
            return true;
        } else {
            $this->rollback();
            return false;
        }
    }

    /**
     * 获取所有子权限规则
     * @param  integer $rule_id 规则id
     * @return string 子权限规则id字符串
     * @author 商贸城的洋芋<bebubble@126.com>
     */
    protected function getSonRules($rule_id)
    {
        static $ids = [];
        $rules = $this->field('rule_id')->where('rule_pid', $rule_id)->select();

        foreach ($rules as $k => $v) {
            $ids[] = $v->rule_id;
            $this->getSonRules($v->rule_id);
        }

        return implode(',', $ids);
    }
}