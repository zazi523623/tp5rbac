<?php
namespace app\admin\validate;

use app\common\validate\ValidateBase;;

/**
 *权限组验证器
 * Class Group
 * @package app\admin\validate
 */
class Group extends ValidateBase
{
    protected $rule = [
        'role_name' => 'require|max:30',
        'role_status' => 'integer|in:1,2',
    ];

    protected $message = [
        'role_name.require' => '权限组名称不能为空！',
        'role_name.max' => '权限组名称不能超过30个字符！',
        'role_status.integer' => '权限组状态参数错误！',
        'role_status.in' => '权限组状态参数错误！',
    ];
}