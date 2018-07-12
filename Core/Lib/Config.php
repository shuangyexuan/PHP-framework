<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

/**
 * 项目配置获取
 * @author TOP糯米
 */
class Config {

    private static $config = [];

    private function __construct() {
    }

    public static function get($name) {
        $file = BASEPATH . 'App/' . __MODULE__ . '/Config/config.php';
        if (file_exists($file)) {
            if (!isset(self::$config[md5($name)])) {
                self::$config[md5($name)] = require $file;
            }
            return isset(self::$config[md5($name)][$name]) ? self::$config[md5($name)][$name] : false;
        } else {
            throw new \Exception($file . ' not found!');
        }
    }
}