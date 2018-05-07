<?php
namespace app\common\validate;

use think\Validate;

/**
 * 验证器基础类
 * Class ValidateBase
 * @package app\common\validate
 */
class ValidateBase extends Validate
{
    /**
     * 验证手机号码
     */
    protected function isMobile($mobilephone){
        if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9][0-9]{8}$/",$mobilephone)){
            //验证通过
            return true;
        }else{
            //手机号码格式不对
            return false;
        }
    }
}