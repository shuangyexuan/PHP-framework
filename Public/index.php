<?php
// 项目根目录
define('BASEPATH', dirname(__FILE__) . '/../');
// 框架所在目录
define('FRAMEWORK', BASEPATH . 'Core/');
// 加载框架
require_once FRAMEWORK . 'Core.php';
// run
Core::run();
