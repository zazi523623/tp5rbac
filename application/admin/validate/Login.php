<?php
namespace app\admin\validate;

use app\common\validate\ValidateBase;;

/**
 * 登陆验证器
 * Class Login
 * @package app\admin\validate
 */
class Login extends ValidateBase
{
    protected $rule = [
        'admin_name' => 'require|length:1,60',
        'password' => 'require|length:1,32',
    ];

    protected $message = [
        'admin_name.require' => '账号不能为空！',
        'admin_name.length' => '账号不能为空并且不得超过60个字符！',
        'password.require' => '密码不能为空！',
        'password.length' => '密码不能为空并且不得超过32个字符！',
    ];
}