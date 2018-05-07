<?php
namespace app\admin\validate;

use app\common\validate\ValidateBase;;
use app\common;

/**
 * 权限规则验证器
 * Class Rule
 * @package app\admin\validate
 */
class Rule extends ValidateBase
{
    protected $rule = [
        'rule_name' => 'require|max:32',
        'rule_icon' => 'require|checkIcon|max:50',
        'rule_method' => 'require|max:50',
        'rule_sort' => 'integer|egt:0',
        'rule_status' => 'integer|in:1,2',
    ];

    protected $message = [
        'rule_name.require' => '规则名称不能为空！',
        'rule_name.max' => '规则名称不能超过32个字符！',
        'rule_icon.require' => '规则图标不能为空！',
        'rule_icon.checkIcon' => '规则图标格式错误！',
        'rule_icon.max' => '规则图标不能超过50个字符！',
        'rule_method.require' => '规则路径不能为空！',
        'rule_method.max' => '规则路径不能超过50个字符！',
        'rule_sort.integer' => '规则排序只能为整数！',
        'rule_sort.egt' => '规则排序只能为整数！',
        'rule_status.integer' => '规则状态参数错误！',
        'rule_status.in' => '规则状态参数错误！',
    ];

    protected $scene = [
        'notctrl' => ['rule_name', 'rule_method', 'rule_sort', 'rule_status'],
    ];

    //校验图标格式
    protected function checkIcon($value)
    {
        if (preg_match('/^fa-.+$/', $value)) {
            return true;
        } else {
            return false;
        }
    }
}