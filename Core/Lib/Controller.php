<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

/**
 * 控制器基类
 * @author TOP糯米
 */
class Controller {
    private $view;

    public function __construct() {
        $this->view = View::getInstance();
    }

    public function params($name, $value) {
        $this->view->setParams($name, $value);
    }

    public function load($file = '', $cache = false) {
        if ($file == '') {
            $file = __CLASSNAME__ . '/' . __FUNCTIONNAME__;
        }
        $this->view->load($file, $cache);
    }

    public function message($msg, $url = '') {
        $jump = 'window.history.back(-1);';
        if ($url) {
            $jump = 'window.location.href="' . url($url) . '";';
        }
        echo '<script>alert(\'' . $msg . '\');' . $jump . '</script>';
        exit;
    }

    public function redirect($url) {
        header('Location: ' . url($url));
    }

    public function showJson($result, $status = 'error', $code = -1, $ext = []) {
        echo json_encode(['result' => $result, 'status' => $status, 'code' => $code, 'ext' => $ext]);
        exit;
    }

    public function filter($str) {
        return filter($str);
    }
}