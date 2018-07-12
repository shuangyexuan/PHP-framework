<?php

return [
    'session' => false,
    'db' => [
        'type' => 'Mysqli',
        'host' => '',
        'dbname' => '',
        'user' => '',
        'pwd' => '',
        'prefix' => '',
        'charset' => 'utf8',
        'cache_dir' => 'Cache/Db/',
        'cache_time' => '12',
    ],
    'view' => [
        'view_dir' => 'App/Home/View/',
        'suffixes' => 'html',
        'cache' => false, //全局缓存开关，可在视图load方法中单独配置
        'cache_dir' => 'Cache/View/',
        'cache_time' => '12',
    ],
];
