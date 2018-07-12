<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

/**
 * 视图基类
 * @author TOP糯米
 */
class View {
    private static $instance;
    private $params = [];
    private $config = [];

    private function __construct() {
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setParams($name, $value) {
        $this->params[$name] = $value;
    }

    public function load($file, $cache) {
        extract($this->params);
        $this->config = Config::get('view');
        $fileName = $this->config['view_dir'] . $file . '.' . $this->config['suffixes'];
        $viewName = $_SERVER['REQUEST_URI'];
        //如果当前视图文件不存在
        if (!file_exists($fileName)) {
            //取出目录
            $dirArr = explode('/', $fileName);
            $beforeFile = $dirArr[count($dirArr) - 1]; //当前准备渲染的文件名
            unset($dirArr[count($dirArr) - 1]);
            $dir = implode('/', $dirArr) . '/';
            if (!is_dir($dir)) {
                //创建目录
                mkdir($dir, 0777, true);
            }
            //创建文件
            file_put_contents($dir . $beforeFile, '');
        }
        if ($this->config['cache'] || $cache) {
            $viewCache = \Lib\Cache\ViewCache::getInstance();
            if (!$viewCache->check(md5($fileName))) { //检查缓存文件状态
                ob_start();
                require_once $fileName;
                $content = ob_get_contents();
                ob_clean();
                $viewCache->set(md5($viewName), $content); //利用缓冲区拿到静态内容写入文件缓存
            }
            echo $viewCache->get(md5($viewName)); //取缓存
        } else {
            require_once $fileName; //直接拿文件
        }
    }
}