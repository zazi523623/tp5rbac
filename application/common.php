<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 获取项目根目录
 * @return string 返回文件路径
 * @author 商贸城的洋芋<bebubble@126.com>
 */
function getRootPath()
{
    $root_path = strchr(__DIR__, 'application');
    $root_path = str_replace($root_path, '', __DIR__);
    $root_path = str_replace('\\', '/', $root_path);
    return $root_path;
}

//生成随机编码
function random_code($length = 32)
{
    $hash = '';   //前缀
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);

    for($i = 0; $i < $length; $i++)
    {
        $hash .= $chars[mt_rand(0, $max)];
    }

    return $hash;
}

/**
 * 获取树形数组
 * @param array $data 原始数据数组(索引必须为数据的id)
 * @return array $tree 格式化的树状数组
 * @author 商贸城的洋芋<bebubble@126.com>
 */
function tree($data = [])
{
    $tree = [];

    foreach ($data as $k => $v) {
        if (isset($data[$v['rule_pid']])) {
            $data[$v['rule_pid']]['son'][] = &$data[$v['rule_id']];
        } else {
            $tree[] = &$data[$v['rule_id']];
        }
    }

    return $tree;
}

/**
 * 验证整数
 * @param string $number 要验证的数字
 * @return boolean true成功 false失败
 * @author 商贸城的洋芋<bebubble@126.com>
 */
function isInteger($number) {
    if (!$number || !is_numeric($number) || $number < 0) {
        return false;
    }

    if (strpos($number, '.')) {
        return false;
    }

    return true;
}

/**
 * 验证数字类型可以是小数
 * @param string $str 数字
 * @return boolean true 通过 false 不通过
 * @author 商贸城的洋芋<bebubble@126.com>
 */
function isNumber($str)
{
    if (is_numeric($str) && $str > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 无限级分类
 * @param array $data 数据集合
 * @param string $p_name 父类id字段名
 * @param string $id_name id字段名
 * @param integer $pid 父类id
 * @param integer $_sort 排序
 * @param string $_html 等级字符串
 * @return array $result
 * @author 商贸城的洋芋<bebubble@126.com>
 */
function unlimited($data = [], $p_name = '', $id_name = '', $pid = 0, $_sort = 0, $_html = '----')
{
    static $result = [];

    foreach ($data as $k => $v) {
       if ($v[$p_name] == $pid) {
           $v['_html'] = str_repeat($_html, $_sort);
           $result[] = $v;
           unlimited($data, $p_name, $id_name, $v[$id_name], $_sort+1, $_html);
       }
    }

    return $result;
}

/**
 * 获取客户端IP地址
 * <br />来源：ThinkPHP
 * <br />"X-FORWARDED-FOR" 是代理服务器通过 HTTP Headers 提供的客户端IP。代理服务器可以伪造任何IP。
 * <br />要防止伪造，不要读这个IP即可（同时告诉用户不要用HTTP 代理）。
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证, 防止通过IP注入攻击
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 *  通过淘宝IP地址库获取IP位置
 *1. 请求接口（GET）：http://ip.taobao.com/service/getIpInfo.php?ip=[ip地址字串]
 *2. 响应信息：（json格式的）国家 、省（自治区或直辖市）、市（县）、运营商
 *3. 返回数据格式Json：
 *其中code的值的含义为，0：成功，1：失败。
 */
function getTaobaoAddress(){
    $ip = get_client_ip();
    $ipContent   = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
    $jsonData = json_decode($ipContent, true);

    if ($jsonData['code']) {
        return '未获取到位置信息';
    } else {
        return $jsonData['data']['region'].$jsonData['data']['city'];
    }
    return $jsonAddress;
}

/*
 * 对象转数组
 */
function objectToArray($object){
    return json_decode( json_encode( $object),true);
}