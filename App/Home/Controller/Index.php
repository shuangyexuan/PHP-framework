<?php

namespace Home\Controller;

use Lib\Controller;

class Index extends Controller {
    private $param;
    
    public function _init() {
        $this->param = '1';
    }

    public function index() {
        var_dump(request()->create('https://www.w3cschool.cn/'));
        $this->params('data', $this->param);
        $this->load();
    }
    
}