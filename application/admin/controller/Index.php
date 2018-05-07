<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Cache;
use think\Config;
use think\Loader;
use think\session\driver\Redis;

class Index extends AdminBase
{
    //首页
    public function index()
    {
        //获取位置信息
        $address = Cache::get($this->admin_info['admin_name'].'address');

        if (!$address) {
            $address = getTaobaoAddress();
            Cache::set($this->admin_info['admin_name'].'address', $address);
        }

        $this->assign('address', $address);
        return $this->fetch();
    }

    //清除缓存
    public function clearCache()
    {
        if ($this->request->isAjax()) {
            Cache::clear();
            return $this->ajaxResponse('ok', 1000);
        }
    }
}