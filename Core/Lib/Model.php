<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib;

use Lib\Database\Driver\Mysqli;

/**
 * 模型基类
 * @author TOP糯米
 */
class Model {
    public $db;
    protected $table;
    protected $pk = 'id';
    protected $map = [];
    protected $error = '';
    private $tables;
    private $field;
    private $separate;
    private $where;
    private $order;
    private $limit;

    public function __construct() {
        if ($this->table) {
            $this->db = $this->connect();
            $this->table = Config::get('db')['prefix'] . $this->table;
        }
    }

    public function getError() {
        if ($this->error) {
            return $this->error;
        }
        return false;
    }

    /**
     * 根据表结构获取数据
     * @return array
     */
    public function getData() {
        //初始化$data
        $data = [];
        if ($this->table) {
            $tableInfo = $this->getTableDesc();
            $mapData = $this->processMap($this->map);
            foreach ($tableInfo as $value) {
                //如果表字段不等于当前主键，并且映射后数组中存在以表字段为键名的值，则为$data赋值
                if ($value['Field'] != $this->pk && isset($mapData[$value['Field']])) {
                    //此处不做过滤
                    $data[$value['Field']] = $mapData[$value['Field']];
                }
            }
        }
        //此处$data仅有除主键外的以表字段为键名的值
        return $data;
    }

    /**
     * 处理字段映射
     * @param $map
     * @return mixed
     */
    private function processMap($map) {
        $mapData = $_POST;
        //遍历POST过来的数据
        foreach ($mapData as $key => $value) {
            //遍历数据库字段
            foreach ($map as $k => $v) {
                if ($key == $k) {
                    // 删除原有键名
                    unset($mapData[$k]);
                    //添加数据库字段为键名并赋值
                    $mapData[$v] = $value;
                }
            }
        }
        return $mapData;
    }

    public function filter($str) {
        return filter($str);
    }

    /**
     * 获取数据库实例
     * @return mixed
     */
    public function connect() {
        $config = Config::get('db');
        if (!isset($this->db[md5(serialize($config))])) {
            switch ($config['type']) {
                case 'Mysqli':
                    $this->db[md5(serialize($config))] = new Mysqli($config['host'], $config['user'], $config['pwd'], $config['dbname'], $config['charset'], $config['prefix']);
                    break;
                //其他新增数据库......
            }
        }
        return $this->db[md5(serialize($config))];
    }

    /**
     * 连贯操作 字段
     * @param string $field
     * @return $this
     */
    public function field($field = '*', $separate = ',') {
        $this->field = $field;
        $this->separate = $separate;
        return $this;
    }

    /**
     * 连贯操作 条件
     * @param string $where
     * @return $this
     */
    public function where($where = '') {
        $this->where = $where;
        return $this;
    }

    /**
     * 连贯操作 排序
     * @param string $order
     * @return $this
     */
    public function order($order = '') {
        $this->order = $order;
        return $this;
    }

    /**
     * 连贯操作 范围
     * @param string $limit
     * @return $this
     */
    public function limit($limit = '') {
        $this->limit = $limit;
        return $this;
    }

    /**
     * 多表（拼接就在这里吧，没想到更好的办法）
     * @param $thisName
     * @param string $tables
     * @return $this
     */
    public function tables($thisName, $tables = '') {
        $this->tables = ' as ' . (($thisName) ? $thisName : 't') . (($tables) ? ', ' . $tables : '');
        return $this;
    }

    public function join($thisName, $tables = '') {
        $this->tables = ' as ' . (($thisName) ? $thisName : 't') . ' ' . $tables;
        return $this;
    }

    /**
     * 查询一条记录
     * @param string $pkValue
     * @return mixed
     */
    public function find($pkValue = '') {
        if ((empty($this->where) || $this->where == '') && $this->pk && $pkValue) {
            $this->where[$this->pk] = $pkValue;
        }
        $result = $this->db->find($this->table, $this->field, $this->where, $this->order, $this->tables);
        $this->_clean();
        return $result;
    }

    /**
     * 查询记录
     * @return mixed
     */
    public function select() {
        $result = $this->db->select($this->table, $this->field, $this->where, $this->order, $this->limit, $this->tables);
        $this->_clean();
        return $result;
    }

    /**
     * 删除
     * @param string $pkValue
     * @return mixed
     */
    public function delete($pkValue = '') {
        if ((empty($this->where) || $this->where == '') && $this->pk && $pkValue) {
            $this->where[$this->pk] = $pkValue;
        }
        $result = $this->db->delete($this->table, $this->where, $this->limit, $this->tables);
        $this->_clean();
        return $result;
    }

    /**
     * 插入一条记录
     * @param $data
     * @return mixed
     */
    public function insert($data) {
        $result = $this->db->insert($this->table, $data);
        return $result;
    }

    /**
     * 更新一条记录
     * @param $data
     * @param string $pkValue
     * @return mixed
     */
    public function update($data, $pkValue = '') {
        if ((empty($this->where) || $this->where == '') && $this->pk && $pkValue) {
            $this->where[$this->pk] = $pkValue;
        }
        $result = $this->db->update($this->table, $this->where, $data);
        $this->_clean();
        return $result;
    }

    public function sum() {
        $result = $this->db->sum($this->table, $this->field, $this->where, $this->separate, $this->tables);
        $this->_clean();
        return $result;
    }

    /**
     * 查询计数
     * @return mixed
     */
    public function count() {
        $result = $this->db->count($this->table, $this->field, $this->where, $this->order, $this->limit, $this->tables);
        $this->_clean();
        return $result;
    }

    /**
     * 获取最后一次SQL语句
     * @return mixed
     */
    public function _sql() {
        $result = $this->db->_sql();
        return $result;
    }

    /**
     * 获取表结构
     * @return mixed
     */
    public function getTableDesc() {
        $result = $this->db->tableDesc($this->table);
        $this->_clean();
        return $result;
    }

    /**
     * 执行一条SQL语句
     * @param $sql
     * @return mixed
     */
    public function query($sql) {
        return $this->db->query($sql);
    }

    /**
     * 初始化查询条件，以免带入下一次查询
     */
    private function _clean() {
        $this->tables = '';
        $this->field = '';
        $this->where = '';
        $this->order = '';
        $this->limit = '';
        $this->separate = '';
    }
}