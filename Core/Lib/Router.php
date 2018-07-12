<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

/**
 * 框架路由（后面再重写，以方便扩展）
 * @author TOP糯米
 */
class Router {
    private static $module = '';
    private static $class = '';
    private static $action = '';
    private static $classMethods = [];

    private function __construct() {
    }
    
    private static function handle() {
        $s = ltrim(((isset($_GET['s'])) ? $_GET['s'] : ''), '/');
        if (!$s) {
            $s = 'Home/Index/index';
        }
        $paramArr = explode('/', explode('.', $s)[0]);
        self::$module = $paramArr[0];
        self::$class = '\\' . $paramArr[0] . '\\Controller\\' . ((isset($paramArr[1]) && $paramArr[1] != '') ? $paramArr[1] : 'Index');
        self::$action = (isset($paramArr[2]) && $paramArr[2] != '') ? $paramArr[2] : 'index';
        define('__MODULE__', self::$module);
        define('__CLASSNAME__', ((isset($paramArr[1]) && $paramArr[1] != '') ? $paramArr[1] : 'Index')); //拿到不包含命名空间的控制器名
        define('__FUNCTIONNAME__', self::$action);
        return $paramArr;
    }

    /**
     * 解析URL
     * @throws \Exception
     * @return string[]|mixed[]|array[]
     */
    public static function pathinfo() {
        $paramArr = self::handle();
        $param = [];
        if (is_dir(BASEPATH . 'App/' . self::$module)) {
            if (class_exists(self::$class)) {
                self::$classMethods = get_class_methods(self::$class);
                if (!in_array(self::$action, self::$classMethods)) {
                    echo '<pre />';
                    throw new \Exception('Function ' . self::$action . ' not found');
                } elseif (isset($paramArr[3]) && $paramArr[3] != '') {
                    $param = self::getParams($paramArr, self::$class, self::$action);
                }
            } else {
                echo '<pre />';
                throw new \Exception('Class ' . self::$class . ' not found');
            }
        } else {
            echo '<pre />';
            throw new \Exception('Module ' . self::$module . ' not found!');
        }
        define('__VIEW__', 'App/' . self::$module . '/View/');
        return ['CLASS' => self::$class, 'FUNCTION' => self::$action, 'PARAM' => $param];
    }
    
    /**
     * 根據方法名獲取參數
     * @param array $paramArr
     * @param string $className
     * @param string $actionName
     * @return mixed[]
     */
    public static function getParams($paramArr, $className, $actionName) {
        unset($paramArr[0]);
        unset($paramArr[1]);
        unset($paramArr[2]);
        $paramName = (new \ReflectionMethod($className, $actionName))->getParameters();
        $paramNameArray = [];
        for ($i = 0; $i < count($paramName); $i++) {
            $paramNameArray[$paramName[$i]->name] = '';
        }
        $param = [];
        $paramArr = array_values($paramArr);
        for ($i = 0; $i < count($paramArr); $i = $i + 2) {
            $_GET[$paramArr[$i]] = $paramArr[$i + 1];
            if (isset($paramNameArray[$paramArr[$i]])) {
                $param[$paramArr[$i]] = $paramArr[$i + 1];
            }
        }
        return $param;
    }

    /**
     * 執行
     */
    public static function build() {
        $info = self::pathinfo();
        if (Config::get('session') === true) {
            session_start();
        }
        $viewName = $_SERVER['REQUEST_URI'];
        if (!\Lib\Cache\ViewCache::getInstance()->check(md5($viewName))) {
            $object = new $info['CLASS'];
            if (in_array('_init', self::$classMethods)) {
                $object->_init();
            }
            call_user_func_array([$object, $info['FUNCTION']], $info['PARAM']);
        } else {
            echo \Lib\Cache\ViewCache::getInstance()->get(md5($viewName));
        }
    }
}
