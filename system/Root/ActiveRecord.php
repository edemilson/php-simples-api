<?php

namespace Root;

use Root\Db;
use \PDO;

class ActiveRecord
{
    private $db;

    private $sql;

    private $select;

    private $distinct;

    private $table;

    private $limitVar;

    private $offsetVar;

    private $lastQuery;

    private $fieldsValues;

    private $fieldsValues2;

    private $lastFieldsValues;

    private $SqlArray;

    private $fieldsArray;

    public function __construct()
    {
        $this->db =  new DB();
        $this->select = '*';
        $this->distinct = '';
        $this->table = null;
        $this->fieldsValues = $this->fieldsValues2 = $this->limitVar = $this->offsetVar = null;

        //set init value
        $this->_initFieldsArray();
        $this->_initSqlArray();
    }

    public function __destruct() {
        $this->db->CloseConnection();
    }

    private function _initFieldsArray()
    {
        $this->fieldsArray = array(
                'where' => null,
                'like' => null,
                'having' => null,
                'groupby' => null,
                'orderby' => null
            );
    }

    private function _initSqlArray()
    {
        $this->SqlArray = array(
                'join' => '',
                'where' => '',
                'singlewhere' => '',
                'having' => '',
                'orderby' => '',
                'groupby' => ''
            );
    }

    public function select($field)
    {
        if (is_string($field)) $this->select = $field;

        return $this;
    }

    public function selectMax($field, $rename)
    {
        if (is_string($field)) {
            $rename = (is_string($rename)) ? $rename : $field;
            $this->select = "max(" . $field . ") as " . $rename;
        }

        return $this;
    }

    public function selectMin($field, $rename)
    {
        if (is_string($field)) {
            $rename = (is_string($rename)) ? $rename : $field;
            $this->select = "min(" . $field . ") as " . $rename;
        }

        return $this;
    }

    public function selectAvg($field, $rename)
    {
        if (is_string($field)) {
            $rename = (is_string($rename)) ? $rename : $field;
            $this->select = "avg(" . $field . ") as " . $rename;
        }

        return $this;
    }

    public function selectSum($field, $rename)
    {
        if (is_string($field)) {
            $rename = (is_string($rename)) ? $rename : $field;
            $this->select = "sum(" . $field . ") as " . $rename;
        }

        return $this;
    }

    public function distinct()
    {
        $this->distinct = " DISTINCT ";

        return $this;
    }

    public function query($query)
    {
        if (is_string($query)) $this->sql = $query;

        return $this;
    }

    public function from($table)
    {
        if (is_string($table)) $this->table = $table;

        return $this;
    }

    public function join($table, $portion, $specificType = "")
    {
        if (is_string($table) && is_string($portion)) {
            if (is_string($specificType) && ! empty($specificType)) $specificType = strtoupper($specificType);
            $this->SqlArray['join'] .= " " . $specificType . " JOIN " . $table . " ON " . $portion;
        }

        return $this;
    }

    public function where($field, $value = null)
    {
        $this->setFields('where', $field, $value);

        $this->bindWhere($this->fieldsArray['where'], 'AND');

        return $this;
    }

    public function orWhere($field, $value)
    {
        $this->setFields('where', $field, $value);

        $this->bindWhere($this->fieldsArray['where'], 'OR');

        return $this;
    }

    private function bindWhere($fields, $type = 'AND')
    {
        if (is_array($fields)) {
            $fieldsvals = array();

            foreach($fields as $column => $val) {
                $column2 = str_replace(".", "", $column);

                if (strpos($column2, ">=") !== false) {
                    $column2 = trim(str_replace(">=", "", $column2));
                    $fieldsvals [] = $column . " :". $column2;
                }
                elseif (strpos($column2, "<=") !== false) {
                    $column2 = trim(str_replace("<=", "", $column2));
                    $fieldsvals [] = $column . " :". $column2;
                }
                elseif (strpos($column2, ">") !== false) {
                    $column2 = trim(str_replace(">", "", $column2));
                    $fieldsvals [] = $column . " :". $column2;
                }
                elseif (strpos($column2, "<") !== false) {
                    $column2 = trim(str_replace("<", "", $column2));
                    $fieldsvals [] = $column . " :". $column2;
                }
                elseif (strpos($column2, "!=") !== false) {
                    $column2 = trim(str_replace("!=", "", $column2));
                    $fieldsvals [] = $column . " :". $column2;
                }
                else {
                    $fieldsvals [] = $column . " = :". $column2;
                }

                $this->fieldsValues[$column2] = $val;
            }

            if (empty($this->SqlArray['where']) && $type == 'OR') $this->SqlArray['singlewhere'] .= " OR " . implode(" " . $type ." ", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " " . $type . " ";
            $this->SqlArray['where'] .= implode(" " . $type ." ", $fieldsvals);

            $this->fieldsArray['where'] = null;
        }
    }

    public function whereIn($field, $list)
    {
        if (is_string($field) && is_array($list)) {
            $fieldsvals = array();
            foreach ($list as $key => $value) {
                $field2 = str_replace(".", "", $field);
                $column = $field2 . "in" . $key;
                $fieldsvals [] = ":" . $column;

                $this->fieldsValues[$column] = $value;
            }

            $marks = implode(",", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " AND " . $field . " IN (" . $marks . ")";
            else $this->SqlArray['singlewhere'] .= " AND " . $field . " IN (" . $marks . ")";
        }

        return $this;
    }

    public function orWhereIn($field, $list)
    {
        if (is_string($field) && is_array($list)) {
            $fieldsvals = array();
            foreach ($list as $key => $value) {
                $field2 = str_replace(".", "", $field);
                $column = $field2 . "in" . $key;
                $fieldsvals [] = ":" . $column;

                $this->fieldsValues[$column] = $value;
            }

            $marks = implode(",", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " OR " . $field . " IN (" . $marks . ")";
            else $this->SqlArray['singlewhere'] .= " OR " . $field . " IN (" . $marks . ")";
        }

        return $this;
    }

    public function whereNotIn($field, $list)
    {
        if (is_string($field) && is_array($list)) {
            $fieldsvals = array();
            foreach ($list as $key => $value) {
                $field2 = str_replace(".", "", $field);
                $column = $field2 . "in" . $key;
                $fieldsvals [] = ":" . $column;

                $this->fieldsValues[$column] = $value;
            }

            $marks = implode(",", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " AND " . $field . " NOT IN (" . $marks . ")";
            else $this->SqlArray['singlewhere'] .= " AND " . $field . " NOT IN (" . $marks . ")";
        }

        return $this;
    }

    public function orWhereNotIn($field, $list)
    {
        if (is_string($field) && is_array($list)) {
            $fieldsvals = array();
            foreach ($list as $key => $value) {
                $field2 = str_replace(".", "", $field);
                $column = $field2 . "in" . $key;
                $fieldsvals [] = ":" . $column;

                $this->fieldsValues[$column] = $value;
            }

            $marks = implode(",", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " OR " . $field . " NOT IN (" . $marks . ")";
            else $this->SqlArray['singlewhere'] .= " OR " . $field . " NOT IN (" . $marks . ")";
        }

        return $this;
    }

    public function like($field, $value, $match)
    {
        $this->setFields('like', $field, $value);

        $this->bindLike($this->fieldsArray['like'], $match, 'AND');

        return $this;
    }

    public function orLike($field, $value, $match)
    {
        $this->setFields('like', $field, $value);

        $this->bindLike($this->fieldsArray['like'], $match, 'OR');

        return $this;
    }

    public function notLike($field, $value, $match)
    {
        $this->setFields('like', $field, $value);

        $this->bindLike($this->fieldsArray['like'], $match, 'AND', true);

        return $this;
    }

    public function orNotLike($field, $value, $match)
    {
        $this->setFields('like', $field, $value);

        $this->bindLike($this->fieldsArray['like'], $match, 'OR', true);

        return $this;
    }

    private function bindLike($fields, $match = 'both', $type = 'AND', $isNot = false)
    {
        if (is_array($fields)) {
            $fieldsvals = array();

            foreach($fields as $field => $val) {
                $newField = str_replace(".", "", $field);
                $newField = $newField . "like";
                $fieldsvals [] = ($isNot) ? $field . " NOT LIKE :" . $newField : $field . " LIKE :" . $newField;

                $match = strtolower($match);

                switch ($match) {
                    case 'left':
                        $this->fieldsValues[$newField] = '%' . $val;
                        break;
                    case 'right':
                        $this->fieldsValues[$newField] = $val . '%';
                        break;
                    case 'none':
                        $this->fieldsValues[$newField] = $val;
                        break;
                    default:
                        $this->fieldsValues[$newField] = '%' . $val . '%';
                        break;
                }
            }

            if (empty($this->SqlArray['where']) && $type == 'OR') $this->SqlArray['singlewhere'] .= " OR " . implode(" " . $type ." ", $fieldsvals);

            if ( ! empty($this->SqlArray['where'])) $this->SqlArray['where'] .= " " . $type . " ";
            $this->SqlArray['where'] .= implode(" " . $type . " ", $fieldsvals);

            $this->fieldsArray['like'] = null;
        }
    }

    public function groupBy($field)
    {
        $this->setFields('groupby', $field);

        $this->bindGroupby($this->fieldsArray['groupby']);

        return $this;
    }

    private function bindGroupby($fields)
    {
        if (is_array($fields)) {
            $fieldsvals = array();

            foreach($fields as $field) {
                $fieldsvals [] = $field;
            }

            if ( ! empty($this->SqlArray['groupby'])) $this->SqlArray['groupby'] .= ",";
            $this->SqlArray['groupby'] .= implode(", ", $fieldsvals);

            $this->fieldsArray['groupby'] = null;
        }
    }

    public function having($field, $value)
    {
        $this->setFields('having', $field, $value);

        $this->bindHaving($this->fieldsArray['having']);

        return $this;
    }

    private function bindHaving($fields)
    {
        if (is_array($fields)) {
            $fieldsvals = array();

            foreach($fields as $column => $val) {
                $column2 = str_replace(".", "", $column);

                if (strpos($column2, ">=") !== false) {
                    $column2 = trim(str_replace(">=", "", $column2)) . 'having';
                    $fieldsvals [] = $column . " :" . $column2;
                }
                elseif (strpos($column2, "<=") !== false) {
                    $column2 = trim(str_replace("<=", "", $column2)) . 'having';
                    $fieldsvals [] = $column . " :" . $column2;
                }
                elseif (strpos($column2, ">") !== false) {
                    $column2 = trim(str_replace(">", "", $column2)) . 'having';
                    $fieldsvals [] = $column . " :" . $column2;
                }
                elseif (strpos($column2, "<") !== false) {
                    $column2 = trim(str_replace("<", "", $column2)) . 'having';
                    $fieldsvals [] = $column . " :" . $column2;
                }
                elseif (strpos($column2, "!=") !== false) {
                    $column2 = trim(str_replace("!=", "", $column2)) . 'having';
                    $fieldsvals [] = $column . " :" . $column2;
                }
                else {
                    $column2 = $column2 . 'having';
                    $fieldsvals [] = $column . " = :" . $column2 ;
                }

                $this->fieldsValues[$column2] = $val;
            }

            if ( ! empty($this->SqlArray['having'])) $this->SqlArray['having'] .= ",";
            $this->SqlArray['having'] .= implode(", ", $fieldsvals);

            $this->fieldsArray['having'] = null;
        }
    } 

    public function orderBy($field, $sort)
    {
        $this->setFields('orderby', $field, $sort);

        $this->bindOrderby($this->fieldsArray['orderby']);

        return $this;
    }

    private function bindOrderby($fields)
    {
        if (is_array($fields)) {
            $fieldsvals = array();

            foreach($fields as $field => $val) {
                $fieldsvals [] = $field . " " . $val;
            }

            if ( ! empty($this->SqlArray['orderby'])) $this->SqlArray['orderby'] .= ",";
            $this->SqlArray['orderby'] .= implode(",", $fieldsvals);

            if (in_array("RANDOM", $fields)) $this->SqlArray['orderby'] = " RAND()";

            $this->fieldsArray['orderby'] = null;
        }
    }

    public function limit($limit, $offset = 0)
    {
        if (is_numeric($limit)) $this->limitVar = $limit;
        if (is_numeric($offset)) $this->offsetVar = $offset;

        return $this;
    }

    private function bindLimitSql()
    {
        if (is_numeric($this->limitVar) && is_numeric($this->offsetVar)) {
            $this->sql .= " LIMIT " . $this->offsetVar . ", " . $this->limitVar;
        }
        elseif (is_numeric($this->limitVar)) {
            $this->sql .= " LIMIT " . $this->limitVar;
        }
    } 

    private function setFields($action, $field, $value)
    {
        if (is_string($field) &&  ! is_null($value)) {
            $this->fieldsArray[$action][$field] = $value;
        }
        elseif (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->fieldsArray[$action][$key] = $val;
            }
        } 
    }

    public function get($table, $limit = null, $offset = null)
    {
        if (is_string($table)) {
            $this->sql = "SELECT " . $this->distinct . $this->select . " FROM " . $table;

            if ( ! empty($this->SqlArray['join'])) {
                $this->sql .= $this->SqlArray['join'];
            }

        }
        
        if (is_null($table) && is_string($this->table)) {
            $this->sql = "SELECT " . $this->distinct . $this->select . " FROM " . $this->table;
            if ( ! empty($this->SqlArray['join'])) {
                $this->sql .= $this->SqlArray['join'];
            }
        }

        //bind where
        if ( ! empty($this->SqlArray['singlewhere'])) $this->sql .= $this->SqlArray['singlewhere'];
        elseif ( ! empty($this->SqlArray['where'])) $this->sql .= " WHERE " . $this->SqlArray['where'];

        //bind group by
        if ( ! empty($this->SqlArray['groupby'])) $this->sql .= " GROUP BY " . $this->SqlArray['groupby'];

        //bind having
        if ( ! empty($this->SqlArray['having'])) $this->sql .= " HAVING " . $this->SqlArray['having'];

        //bind order by
        if ( ! empty($this->SqlArray['orderby'])) $this->sql .= " ORDER BY " . $this->SqlArray['orderby'];

        //bind limit
        if ( ! is_null($limit)) $this->limit($limit, $offset);
        $this->bindLimitSql();

        $this->lastQuerySql = $this->sql;
        $this->lastFieldsValues = $this->fieldsValues;

        //reset
        $this->_initSqlArray();
        $this->sql = $this->fieldsValues = $this->limitVar = $this->offsetVar = null;

        return $this;
    }

    public function getWhere($table, $fields, $limit, $offset)
    {
        if (is_string($table)) {
            $this->sql = "SELECT " . $this->distinct . $this->select . " FROM " . $table;

            $this->bindWhere($fields, 'AND');

            //bind order by
            if ( ! empty($this->SqlArray['orderby'])) $this->sql .= " ORDER BY " . $this->SqlArray['orderby'];

            //bind limit
            if ( ! is_null($limit)) $this->limit($limit, $offset);
            $this->bindLimitSql();
        }

        $this->lastQuerySql = $this->sql;
        $this->lastFieldsValues = $this->fieldsValues;

        //reset
        $this->_initSqlArray();
        $this->sql = $this->fieldsValues = $this->limitVar = $this->offsetVar = null;

        return $this;
    }

    /**
     * result data
     */
    public function result()
    {
        return $this->db->query($this->lastQuerySql, $this->lastFieldsValues, PDO::FETCH_OBJ);
    }

    public function result_array()
    {
        return $this->db->query($this->lastQuerySql, $this->lastFieldsValues);
    }

    public function row()
    {
        return $this->db->row($this->lastQuerySql, $this->lastFieldsValues, PDO::FETCH_OBJ);
    }

    public function row_array()
    {
        return $this->db->row($this->lastQuerySql, $this->lastFieldsValues);
    }

    public function columnArray()
    {
        return $this->db->column($this->lastQuerySql, $this->lastFieldsValues);
    }

    public function num_rows()
    {
        $this->db->query($this->lastQuerySql, $this->lastFieldsValues);
        return $this->db->rowCount();
    }

    public function set($field, $value)
    {
        $this->fieldsValues2[$field] = $value;

        return $this;
    }

    public function insert($table, $data)
    {
        $result = 0;

        if ( ! empty($this->fieldsValues2)) $data = $this->fieldsValues2;

        if (is_string($table) && ! empty($data)) {
            $fields     = array_keys($data);
            $fieldsvals = array(implode(",",$fields), ":" . implode(",:",$fields));
            $sql        = "INSERT INTO " . $table . " (" . $fieldsvals[0] . ") VALUES (" . $fieldsvals[1] . ")";

            $result = $this->db->query($sql, $data);
            $this->lastQuerySql = $sql;
        }

        $this->fieldsValues2 = null;

        //reset
        $this->_initSqlArray();

        return ($result > 0) ? true : false;
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function insertBatch($table, $datas)
    {
        $result = 0;

        if (is_string($table) && ! empty($datas) && is_array($datas)) {

            if (isset($datas[0])) {

                $fields = array_keys($datas[0]);

                $fieldsvals = array();
                $newFields  = array();
                $newFieldsValues = null;
                foreach ($datas as $key => $data) {
                    foreach ($data as $column => $value) {
                        $column2 = $column . $key;
                        $newFields[] =  $column2;
                        $newFieldsValues[$column2] = $value;
                    }

                    $fieldsvals[] = "(" . ":" . implode(",:",$newFields) . ")";
                    $newFields = array();
                }

                $sql = "INSERT INTO " . $table . " (" . implode(",",$fields) . ") VALUES " . implode(",",$fieldsvals);

                $result = $this->db->query($sql, $newFieldsValues);
                $this->lastQuerySql = $sql;
            }
        }

        return ($result > 0) ? true : false;
    }

    public function update($table, $data, $where = null)
    {
        $result = 0;

        if ( ! empty($this->fieldsValues2)) $data = $this->fieldsValues2;

        if (is_string($table) && ! empty($data)) {
            $fieldsvals = array();
            foreach ($data as $column => $value) {
                $column2 = $column . "u";
                $fieldsvals[] = $column . " = :" . $column2;
                $newFieldsValues[$column2] = $value;
            }

            if (is_array($where)) $this->where($where);

            $sql = "UPDATE " . $table . " SET " . implode(", ",$fieldsvals) . " WHERE " . $this->SqlArray['where'];

            $newFieldsValues = array_merge($newFieldsValues, $this->fieldsValues);

            $result = $this->db->query($sql, $newFieldsValues);
            $this->lastQuerySql = $sql;
        }

        $this->fieldsValues = $this->fieldsValues2 = null;

        //reset
        $this->_initSqlArray();

        return ($result > 0) ? true : false;
    }

    public function delete($table, $where = null)
    {
        $result = 0;

        print_r($this->fieldsValues);

        if (is_string($table)) {
            $fieldsvals = array();
            foreach ($data as $column => $value) {
                $column2 = $column . "d";
                $fieldsvals[] = $column . " = :" . $column2;
                $newFieldsValues[$column2] = $value;
            }

            if (is_array($where)) $this->where($where);

            $sql = "DELETE FROM " . $table . " WHERE " . $this->SqlArray['where'];

            $result = $this->db->query($sql, $this->fieldsValues);
            $this->lastQuerySql = $sql;
        }

        $this->fieldsValues = null;

        //reset
        $this->_initSqlArray();

        return ($result > 0) ? true : false;
    }

    public function emptyTable($table)
    {
        $result = 0;

        if (is_string($table)) {
            $sql = "DELETE FROM " . $table;
            $result = $this->db->query($sql);
            $this->lastQuerySql = $sql;
        }

        return ($result > 0) ? true : false;
    }

    public function truncate($table)
    {
        $result = 0;

        if (is_string($this->table)) $table = $this->table;
        $sql = "TRUNCATE " . $table;
        $result = $this->db->query($sql);
        $this->lastQuerySql = $sql;

        return ($result > 0) ? true : false;
    }

    public function lastQuery()
    {
        return $this->lastQuerySql;
    }

    public function setQuery($sql){

        $this->lastQuerySql = $sql;
        $this->_initSqlArray();

        return $this;

    }

    public function simple_query($sql){

        $pdo = $this->db->retorna_pdo_instancia();
        $pdo->exec($sql);

    }

}