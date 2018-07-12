<?php

// 函数库

function url($url = '') {
    if ($url) {
        $urlArr = explode('.', $url);
        return $urlArr[0] . '.html';
    }
    return '/Home/Index/index.html';
}

function removeDir($dirName) {
    if ($handle = @opendir($dirName)) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir($dirName . '/' . $item)) {
                    removeDir($dirName . '/' . $item);
                } else {
                    unlink($dirName . '/' . $item);
                }
            }
        }
        closedir($handle);
        rmdir($dirName);
    }
}

function filter($str) {
    $replaceArr = array(
        "/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
    );
    $str = preg_replace($replaceArr, '', $str);
    $str = htmlspecialchars($str);
    return $str;
}

function format_time($time) {
    $timer = $time;
    $diff = $_SERVER['REQUEST_TIME'] - $timer;
    $day = floor($diff / 86400);
    $free = $diff % 86400;
    if ($day > 0) {
        return date('Y-m-d H:i:s', $time);
    } else {
        if ($free > 0) {
            $hour = floor($free / 3600);
            $free = $free % 3600;
            if ($hour > 0) {
                return $hour . "小时前";
            } else {
                if ($free > 0) {
                    $min = floor($free / 60);
                    $free = $free % 60;
                    if ($min > 0) {
                        return $min . "分钟前";
                    } else {
                        if ($free > 0) {
                            return $free . "秒前";
                        } else {
                            return '刚刚';
                        }
                    }
                } else {
                    return '刚刚';
                }
            }
        } else {
            return '刚刚';
        }
    }
}

function curl($url, $data = [], $header = []) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if (! empty($data)) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, $header);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $res = curl_exec($curl);
    curl_close($curl);
    if ($res) {
        return $res;
    }
    return false;
}

function get_client_ip($type = 0, $client = true) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($client) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

function request() {
    static $instance = false;
    if(!$instance){
        $instance = new \Lib\Request();
    }
    return $instance;
}
