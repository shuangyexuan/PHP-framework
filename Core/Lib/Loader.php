<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

/**
 * 自动加载类（一定程度上遵循PSR-0自动加载规范)
 * @author TOP糯米
 */
class Loader {
    private function __construct() {
    }

    public static function load($class) {
        $frameFile = FRAMEWORK . str_replace('\\', '/', $class) . '.php';
        $appFile = BASEPATH . 'App/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($appFile)) {
            require_once $appFile;
        } elseif (file_exists($frameFile)) {
            require_once $frameFile;
        } else {
            echo '<pre />';
            throw new \Exception($class . ' not found!');
        }
    }
}