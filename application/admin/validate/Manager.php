<?php
namespace app\admin\validate;

use app\common\validate\ValidateBase;

/**
 * 管理员验证器
 * Class Manager
 * @package app\admin\validate
 */
class Manager extends ValidateBase
{
    protected $rule = [
        'admin_name' => 'require|is_username',
        'admin_nickname' => 'require|max:20',
        'admin_password' => 'require|is_psassword',
        'repassword' => 'confirm:admin_password',
        'phone' => 'require|isMobile',
        'email' => 'email',
    ];

     protected $message = [
         'admin_name.require' => '管理员账号不能为空！',
         'admin_name.is_username' => '管理员账号格式错误，不能小于2或大于20个字符！',
         'admin_nickname.require' => '管理员昵称不能为空！',
         'admin_nickname.max' => '管理员昵称不能超过20个字符！',
         'admin_password.require' => '管理员密码不能为空！',
         'admin_password.is_psassword' => '管理员密码格式错误，请输入6-20位密码！',
         'repassword.confirm' => '两次输入的密码不一致！',
         'phone.require' => '手机号码不能为空！',
         'phone.isMobile' => '手机号码格式错误！',
         'email.email' => '邮箱格式错误！',
     ];

    protected $scene = [
        'edit' => ['admin_nickname', 'admin_password' => 'is_psassword', 'repassword', 'phone', 'email'],
    ];

    /**
     * 检查用户名是否符合规定
     * @param string $value 要检查的用户名
     * @return boolean true成功 false失败
     */
    protected  function is_username($value)
    {
        $strlen = strlen($value);

        if (!preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $value)) {
            return false;
        } else if (20 < $strlen || $strlen < 2) {
            return false;
        }

        return true;
    }

    /**
     * 验证密码
     * @param string $value 要检查的密码
     * @return boolean true成功 false失败
     */
    protected function is_psassword($value)
    {
        if (preg_match('/^.{6,20}$/',$value)) {
            return true;
        } else {
            return false;
        }
    }
}