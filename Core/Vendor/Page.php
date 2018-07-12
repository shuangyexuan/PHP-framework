<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Vendor;

/**
 * 分页类
 * @author TOP糯米
 */
class Page {
    public $startNum = 10;
    public $listNum = 10;
    private $allNum = 0;
    private $totalPage = 0;
    private $p;

    /**
     * 实例化时传入分页记录数、全部记录数
     * @param int $listNum
     * @param int $allNum
     */
    public function __construct($listNum, $allNum) {
        $this->p = (isset($_GET['p']) && (int)$_GET['p']) ? (int)$_GET['p'] : 1;
        $this->listNum = $listNum;
        $this->allNum = $allNum;
        $this->totalPage = ceil($this->allNum / $this->listNum);
        $this->startNum = ($this->p - 1) * $this->listNum;
    }

    /**
     * 获取总页数
     * @return number
     */
    public function getTotalPage() {
        return $this->totalPage;
    }

    /**
     * 获取分页HTML
     * @return string
     */
    public function show() {
        $html = '<div style="width: 100%; height: 30px; line-height: 30px; text-align: center;" id="ManagePage">';
        $uri_string = $_GET['s'];
        $m = [];
        preg_match('/\/p\/(.*?).html/i', $uri_string, $m);
        $uri_string = '/' . ltrim(((!empty($m)) ? str_replace($m[0], '', $uri_string) : explode('.', $uri_string)[0]), '/');
        $html .= ($this->p != 1) ? '<a href="' . url($uri_string . '/p/' . ($this->p - 1) . '.html') . '">上一页</a>' : '';
        for ($i = 0; $i < $this->totalPage; $i++) {
            if ($this->p == ($i + 1)) {
                $html .= '<span>' . ($i + 1) . '</span>';
            } else {
                $html .= '<a href="' . url($uri_string . '/p/' . ($i + 1)) . '">' . ($i + 1) . '</a>';
            }
        }
        $html .= ($this->p < $this->totalPage) ? '<a href="' . url($uri_string . '/p/' . ($this->p + 1)) . '">下一页</a>' : '';
        return $html . '</div>';
    }
}