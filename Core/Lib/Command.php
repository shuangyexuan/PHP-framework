<?php

namespace Lib;

class Command {
    public static function build(){
        $params = getopt('m:c:a:p:');
        $params['m'] = (isset($params['m'])) ? $params['m'] : 'Home';
        $params['c'] = (isset($params['c'])) ? $params['c'] : 'Index';
        $params['a'] = (isset($params['a'])) ? $params['a'] : 'index';
        $params['p'] = (isset($params['p'])) ? $params['p'] : '';
        define('__MODULE__', $params['m']);
        define('__CLASSNAME__', $params['c']);
        define('__FUNCTIONNAME__', $params['a']);
        $paramArr = explode('.', $params['p']);
        $className = '\\' . $params['m'] . '\\Controller\\' . $params['c'];        
        $paramName = (new \ReflectionMethod($className, __FUNCTIONNAME__))->getParameters();
        $paramNameArray = [];
        $param = [];
        for ($i = 0; $i < count($paramName); $i++) {
            $paramNameArray[$paramName[$i]->name] = '';
        }
        for ($i = 0; $i < count($paramArr); $i = $i + 2) {
            if (isset($paramNameArray[$paramArr[$i]])) {
                $param[$paramArr[$i]] = $paramArr[$i + 1];
            }
        }
        $object = new $className();
        call_user_func_array([$object, $params['a']], $param);
    }
}