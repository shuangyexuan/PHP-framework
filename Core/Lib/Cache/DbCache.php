<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib\Cache;

use Lib\Config;

/**
 * 数据库缓存
 * @author TOP糯米
 */
class DbCache {
    private static $instance;
    private static $config;
    private static $identifying;

    private function __construct() {
        self::$config = Config::get('db');
    }

    public static function getInstance($identifying = '') {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$identifying = ((!$identifying) ? '' : $identifying . '/');
        return self::$instance;
    }

    public function check($name) {
        $dir = self::$config['cache_dir'] . self::$identifying;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
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
        $dir = self::$config['cache_dir'] . self::$identifying;
        if (file_put_contents($dir . $name, serialize($value))) {
            return true;
        }
        return false;
    }

    public function get($name) {
        $dir = self::$config['cache_dir'] . self::$identifying;
        $fileName = $dir . $name;
        if (file_exists($fileName)) {
            return unserialize(file_get_contents($fileName));
        } else {
            throw new \Exception($fileName . ' not found!');
        }
    }

    public function clean($name = '', $cleanIdentifying = false) {
        $dir = self::$config['cache_dir'];
        if ($name) {
            $fileName = $dir . $name;
            if (@unlink($fileName)) {
                return true;
            }
            return false;
        } elseif ($cleanIdentifying) {
            removeDir($dir . self::$identifying);
            return true;
        } elseif (!$cleanIdentifying) {
            removeDir($dir);
            return true;
        }
    }
}