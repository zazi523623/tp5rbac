<?php
//配置文件
return [
    'ADMIN_LOGIN_LOG' => '/static/logs/admin_login',
    'dispatch_error_tmpl' => 'public/error',
    'dispatch_success_tmpl' => 'public/success',
    //分页配置
    'paginate'               => [
        'type'      => 'BootstrapDetailed',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'cache'                  => [
        // 驱动方式
        'type'   => 'redis',
        'host'   => '127.0.0.1',
    ],
];