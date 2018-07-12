<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

/**
 * 框架入口
 * @author TOP糯米
 */
class Core {
    
    public static function run() {
        // 设置时区
        date_default_timezone_set('PRC');
        // 注册自动加载类
        require_once FRAMEWORK . 'Lib/Loader.php';
        spl_autoload_register('Lib\Loader::load');
        require_once FRAMEWORK . 'Lib/Functions/functions.php';
        if(php_sapi_name() === 'cli'){
            \Lib\Command::Build();
        }else{
            \Lib\Router::build();
        }
    }
}