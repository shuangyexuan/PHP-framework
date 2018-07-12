<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Lib\Database\Driver;

/**
 * Mysqli数据库驱动类
 * @author TOP糯米
 */
class Mysqli {
    private $link;
    private $prefix;
    private $sql;

    public function __construct($host, $user, $pwd, $dbname, $charset, $prefix) {
        $this->link = mysqli_connect($host, $user, $pwd, $dbname);
        if ($this->link === false) {
            throw new \Exception('Db connection faild!');
        }
        mysqli_query($this->link, 'set names ' . $charset);
        $this->prefix = $prefix;
    }

    //some function

    /**
     * 查询多条记录，多表联查请传入tables参数
     * @param $table
     * @param $field
     * @param $where
     * @param $order
     * @param $limit
     * @param $tables
     * @return array
     * @throws \Exception
     */
    public function select($table, $field, $where, $order, $limit, $tables) {
        $filed = $this->field($field);
        $where = $this->where($where, ($tables) ? true : false);
        $order = $this->order($order);
        $limit = $this->limit($limit);
        $this->sql = 'select ' . $filed . ' from ' . $table . $tables . $where . $order . $limit;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * 查询单条记录，多表联查请传入tables参数
     * @param $table
     * @param $field
     * @param $where
     * @param $order
     * @param $tables
     * @return array|null
     * @throws \Exception
     */
    public function find($table, $field, $where, $order, $tables) {
        $filed = $this->field($field);
        $where = $this->where($where, ($tables) ? true : false);
        $order = $this->order($order);
        $limit = $this->limit(1);
        $this->sql = 'select ' . $filed . ' from ' . $table . $tables . $where . $order . $limit;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        return mysqli_fetch_assoc($result);
    }

    /**
     * 插入记录
     * @param $table
     * @param $data
     * @return bool|int|string
     * @throws \Exception
     */
    public function insert($table, $data) {
        $data = $this->addData($data);
        $this->sql = 'insert into ' . $table . '(' . $data[0] . ') values(' . $data[1] . ')';
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        if ($result) {
            return mysqli_insert_id($this->link);
        }
        return false;

    }

    /**
     * 更新记录
     * @param $table
     * @param $where
     * @param $data
     * @return bool|int
     * @throws \Exception
     */
    public function update($table, $where, $data) {
        $where = $this->where($where);
        $data = $this->updateData($data);
        $this->sql = 'update ' . $table . ' set ' . $data . $where;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        if ($result) {
            return mysqli_affected_rows($this->link);
        }
        return false;
    }

    /**
     * 删除记录，多表联删请传入tables参数
     * @param $table
     * @param $where
     * @param $limit
     * @param $tables
     * @return bool|int
     * @throws \Exception
     */
    public function delete($table, $where, $limit, $tables) {
        $where = $this->where($where, ($tables) ? true : false);
        $limit = $this->limit($limit);
        $this->sql = 'delete from ' . $table . $tables . $where . $limit;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }

        if ($result) {
            return mysqli_affected_rows($this->link);
        }
        return false;
    }

    public function count($table, $field, $where, $order, $limit, $tables) {
        $filed = $this->field($field);
        $where = $this->where($where, ($tables) ? true : false);
        $order = $this->order($order);
        $limit = $this->limit($limit);
        $this->sql = 'select count(' . $filed . ') from ' . $table . $tables . $where . $order . $limit;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        $row = mysqli_fetch_array($result);
        return $row[0];
    }

    public function sum($table, $field, $where, $separate, $tables) {
        $filed = $this->field($field, $separate);
        $where = $this->where($where, ($tables) ? true : false);
        $this->sql = 'select sum(' . $filed . ') from ' . $table . $tables . $where;
        if (!$result = mysqli_query($this->link, $this->sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        $row = mysqli_fetch_array($result);
        return $row[0];
    }

    public function tableDesc($table) {
        $sql = 'desc ' . $table;
        if (!$result = mysqli_query($this->link, $sql)) {
            throw new \Exception(mysqli_error($this->link));
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function query($sql) {
        $this->sql = $sql;
        return mysqli_query($this->link, $this->sql);
    }

    /**
     * 获取最后一条sql
     * @return mixed
     */
    public function _sql() {
        return $this->sql;
    }

//function end

    /**
     * 字段
     * @param $field
     * @return string
     * 仔细一想，有多表查询，这里还是不给字段名称加符号，留给模型调用时来操作
     */
    public function field($field, $separate = ',') {
        if ($field == '*' || !$field) {
            return '*';
        } else if (is_array($field) && !empty($field)) {
            for ($i = 0; $i < count($field); $i++) {
                $field[$i] = trim($field[$i]);
            }
            return implode($separate, $field);
        } else if (strstr($field, 'distinct')) {
            $field = str_replace('distinct', '', strtr($field, [' ' => ''])); //去除空格并解析出字段名以重新组合
            return 'distinct ' . $field;
        } else {
            $field = explode(',', $field);
            if (is_array($field) && !empty($field) && (isset($field[0]) && $field[0] != '')) {
                for ($i = 0; $i < count($field); $i++) {
                    $field[$i] = trim($field[$i]);
                }
                return implode($separate, $field);
            }
        }
    }

    /**
     * 条件
     * @param $where
     * @return string
     * 有多表查询，这里也不给字段名称加符号
     */
    public function where($where, $tables = false) {
        $string = ' where ';
        $dot = ($tables) ? '' : '\'';
        if (is_array($where)) {
            if (!empty($where)) {
                $keys = array_keys($where);
                $vals = array_values($where);
                $and = ' and ';
                for ($i = 0; $i < count($keys); $i++) {
                    $string .= (($i == 0) ? '' : $and) . $keys[$i] . '=' . $dot . $vals[$i] . $dot;
                }
                return $string;
            }
            return '';
        } else if ($where != '') {
            return $string . $where;
        }
        return '';
    }

    /**
     * 排序
     * @param $order
     * @return string
     */
    public function order($order) {
        $str = '';
        if ($order) {
            $str = ' order by ' . $order;
        }
        return $str;
    }

    /**
     * 范围
     * @param $limit
     * @return string
     */
    public function limit($limit) {
        $str = '';
        if ($limit) {
            $str = ' limit ' . $limit;
        }
        return $str;
    }

    public function updateData($data) {
        $keys = array_keys($data);
        $vals = array_values($data);
        $str = '';
        $dot = ',';
        $d = '\'';
        for ($i = 0; $i < count($keys); $i++) {
            $str .= (($i == 0) ? '' : $dot) . '`' . $keys[$i] . '`' . '=' . ((is_array($vals[$i])) ? '' : $d) . ((is_array($vals[$i])) ? $vals[$i][0] : $vals[$i]) . ((is_array($vals[$i])) ? '' : $d);
        }
        return $str;
    }

    public function addData($data) {
        $arr = [];
        $arr[0] = '`' . join('`,`', array_keys($data)) . '`';
        $arr[1] = '\'' . join('\',\'', array_values($data)) . '\'';
        return $arr;
    }

    /**
     * 利用析构方法关闭数据库连接
     */
    public function __destruct() {
        mysqli_close($this->link);
    }
}