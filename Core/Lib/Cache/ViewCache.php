<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib\Cache;

use Lib\Config;

/**
 * 静态文件缓存
 * @author TOP糯米
 */
class ViewCache {
    private static $instance;
    private static $config;

    private function __construct() {
        self::$config = Config::get('view');
        self::$config['cache_dir'] = self::$config['cache_dir'] . __MODULE__ . '/' . __CLASSNAME__ . '/';
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function check($name) {
        $dir = self::$config['cache_dir'];
        $t = self::$config['cache_time'];
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            $time = filemtime($fileName);
            if (time() - $time > $t) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function set($name, $value) {
        $dir = self::$config['cache_dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (file_put_contents($dir . $name, $value)) {
            return true;
        }
        return false;
    }

    public function get($name) {
        $dir = self::$config['cache_dir'];
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        } else {
            throw new \Exception($fileName . ' not found!');
        }
    }

    public function clean($name = '') {
        $dir = Config::get('view')['cache_dir'] . $name . '/';
        removeDir($dir);
        return true;
    }
}