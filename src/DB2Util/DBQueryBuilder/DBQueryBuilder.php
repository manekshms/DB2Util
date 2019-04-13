<?php
namespace DB2Util\DBQueryBuilder;

use \DB2Util\DBConnection\DBConnection;
use \DB2Util\DBConnection\DBConnectionInterface;

/**
 * @author Maneksh M S
 */
class DBQueryBuilder implements DBQueryBuilderInterface {

    const UNION = 1;

    const UNION_ALL = 2;

    private $_dbConnection;

    private $_table_name;

    private $_last_sql_query;

    private $_last_sql_params = array();

    private $_where_conditions = array();

    private $_select_columns = array();

    private $_order_by_columns = array();

    private $_join_conditions = array();

    private $_group_by_columns = array();

    private $_having_columns = array();

    private $_union_result_set = array();

    private $_available_operators = ['=', '!=', '>', '<', '<>', '>=', '<=', 'like', 'between', 'in', 'not in'];

    private $_limit = null;

    private $_offset = null;

    private $_map_functions = array();

    public function setMapFunction($cb) {
        $this->_map_functions = $cb;
    }

    public function getMapFunction() {
        return $this->_map_functions;
    }

    /**
     * constructor
     * @param DBConnection $dbConnection
     */
    public function __construct(DBConnection $dbConnection) {
        $this->setDBConnection($dbConnection);
    }

    /**
     * setDBConnection
     * @param DBConnection database connection
     */
    public function setDBConnection(DBConnectionInterface $dbConn) {
        $this->_dbConnection = $dbConn;
    }

    /**
     * getDBConnection
     * @return DBConnection
     */
    public function getDBConnection() {
        return $this->_dbConnection;
    }

    /**
     * getTableName
     * @return string table name
     */
    protected function getTableName() {
        return $this->_table_name;
    }

    /**
     * setTableName
     * set table name
     * @param string $table_name table name
     */
    protected function setTableName($table_name) {
        $this->_table_name = $table_name;
    }

    /**
     * setLastSqlQuery
     * Set last executed sql query
     * @param string $sql Sql string
     */
    protected function setLastSqlQuery($sql) {
        $this->_last_sql_query = $sql;
    }

    /**
     * getLastSqlQuery
     * Get last executed SQl query
     * @return string
     */
    public function getLastSqlQuery() {
        return $this->_last_sql_query;
    }

    /**
     * setLastSqlParams
     * Set last SQL params
     * @param array $sql_params
     */
    protected function setLastSqlParams($sql_params) {
        $this->_last_sql_params = $sql_params;
    }

    /**
     * getLastSqlParams
     * Get last executed SQl params
     * @return array
     */
    public function getLastSqlParams() {
        return $this->_last_sql_params;
    }

    /**
     * setSelectColumnName
     * Set select column name
     * @param array $column_names
     */
    private function setSelectColumnNames($column_names) {
        $this->_select_columns = $column_names;
    }

    /**
     * getSelectColumnNames
     * @return array selected column names
     */
    private function getSelectColumnNames() {
        return $this->_select_columns;
    }

    /**
     * setWhereConditions
     * @param array $conditions
     */
    public function setWhereConditions($conditions) {
        $this->_where_conditions = $conditions;
    }

    /**
     * getWhereConditons
     * @return array
     */
    public function getWhereConditions() {
        return $this->_where_conditions;
    }

    /**
     * setLimit
     * @param int $value limit value
     */
    public function setLimit($value) {
        $this->_limit = $value;
    }

    /**
     * getLimit
     * @return int limit
     */
    public function getLimit() {
        return $this->_limit;
    }

    /**
     * setOffset
     * @param int $value offset value
     */
    public function setOffset($value) {
        $this->_offset = $value;
    }

    /**
     * getLimit
     * @return int limit
     */
    public function getOffset() {
        return $this->_offset;
    }

    /**
     * setOrderByColumns
     * @param array $order_by_columns Set order by columns
     */
    public function setOrderByColumns($order_by_columns) {
        $this->_order_by_columns = $order_by_columns;
    }

    /**
     * getOrderByColumns
     * @return array
     */
    public function getOrderByColumns() {
        return $this->_order_by_columns;
    }

    /**
     * setJoinConditions
     * @param array join conditions
     */
    public function setJoinConditions($join_conditions) {
        $this->_join_conditions = $join_conditions;
    }

    /**
     * getJoinConditions
     * @return array join conditions
     */
    public function getJoinConditions() {
        return $this->_join_conditions;
    }

    /**
     * setGroupByColumns
     * @param array $value Group by columns name array
     */
    public function setGroupByColumns($value) {
        $this->_group_by_columns = $value;
    }

    /**
     * getGroupByColumns
     * @retun array Group by columns name array
     */
    public function getGroupByColumns() {
        return $this->_group_by_columns;
    }

    /**
     * setHavingColumns
     * Set Having columns
     * @param array $value having
     */
    public function setHavingColumns($value) {
        $this->_having_columns = $value;
    }

    /**
     * getHavingColumns
     * Get Having columns
     * @return array having array
     */
    public function getHavingColumns() {
        return $this->_having_columns;
    }

    /**
     * setUnionResultSet
     * @param array $union_result_set
     */
    private function setUnionResultSet($union_result_set) {
        $this->_union_result_set = $union_result_set;
    }

    /**
     * getUnionResultSet
     * @return array
     */
    private function getUnionResultSet() {
        return $this->_union_result_set;
    }

    /**
     * table
     * Table name
     * @param string $table_name table name
     * @return QueryBuilderInterface $this;
     */
    public function table($table_name) {
        $this->setTableName($table_name);
        return $this;
    }

    /**
     * get
     * Get data from database
     * @return array Resut array
     */
    public function get() {
        $table_name = $this->_table_name;
        if ($table_name == null) {
            throw new QueryBuilderException('Table name is empty');
        }
        $select_query = $this->getSelectSqlQuery();
        $select_query_params = $this->getSelectSqlQueryParams();
        if (count($this->getUnionResultSet()) > 0) {
            $sql = "( " . $select_query . " ) ";
            $params = $select_query_params;
            $union_result_set = $this->getUnionResultSet();
            foreach ($union_result_set as $res) {
                $reflectionObject = new \ReflectionObject($res[1]);
                $reflectionMethodGetSelectSqlQuery = $reflectionObject->getMethod("getSelectSqlQuery");
                $reflectionMethodGetSelectSqlQuery->setAccessible(true);
                $reflectionMethodgetSelectSqlQueryParams = $reflectionObject->getMethod("getSelectSqlQueryParams");
                $reflectionMethodgetSelectSqlQueryParams->setAccessible(true);
                $sql .= (($res[0] == static::UNION) ? ' UNION ( ' : ' UNION ALL (') . $reflectionMethodGetSelectSqlQuery->invoke($res[1]) . " ) ";
                $param = $reflectionMethodgetSelectSqlQueryParams->invoke($res[1]);
                $params = array_merge($params, $param);
            }
            try {
                return $this->getDBConnection()->query($sql, $params)->fetchAll();
            } catch (\Exception $e) {
                throw new DBQueryBuilderException($e->getMessage());
            }
        }
        try {
            $map_functions = $this->getMapFunction();
            if (count($map_functions) > 0) {
                $stmt = $this->getDBConnection()->query($select_query, $select_query_params);
                $out = array();
                while ($row = $stmt->fetch()) {
                    foreach ($map_functions as $map) {
                        $row = $map($row);
                    }
                    $out[] = $row;
                }
                return $out;
            } else {
                return $this->getDBConnection()->query($select_query, $select_query_params)->fetchAll();
            }
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * mapResult
     * @param function $cb_function
     * @reutrn $this
     */
    public function mapResult($cb_function) {
        $map_functions = $this->getMapFunction();
        $map_functions[] = $cb_function;
        $this->setMapFunction($map_functions);
        return $this;
    }

    /**
     * processSelect
     * @return string select string
     */
    private function generateSelectString() {
        $select_columns = $this->getSelectColumnNames();
        if (empty($select_columns)) {
            $select_columns = ["*"];
        }
        return implode(', ', $select_columns);
    }

    /**
     * generateAndWhereString
     * @param array $rows
     * @return string
     */
    private function generateAndWhereString($rows) {
        $where_array = array_reduce($rows, function ($carry, $item) {

            if (is_array($item)) {
                switch ($item[1]) {

                case 'in':
                    array_push($carry, $item[0] . " " . $item[1] . " ( " . implode(", ", array_fill(0, count($item[2]), " ?")) . " ) ");
                    break;

                case 'not in':
                    array_push($carry, $item[0] . " " . $item[1] . " ( " . implode(", ", array_fill(0, count($item[2]), " ?")) . " ) ");
                    break;

                case 'between':
                    array_push($carry, $item[0] . " " . $item[1] . implode(" AND ", array_fill(0, count($item[2]), " ?")));
                    break;

                default:
                    if (is_null($item[2])) {
                        array_push($carry, $item[0] . " IS NULL ");
                    } else {
                        array_push($carry, $item[0] . " " . $item[1] . " ? ");
                    }
                    break;
                }
            }

            if (is_callable($item)) {
                $queryBuilder = new self($this->getDBConnection());
                $item($queryBuilder);
                $queryBuilderReflection = new \ReflectionObject($queryBuilder);
                $method = $queryBuilderReflection->getMethod('generateWhereString');
                $method->setAccessible(true);
                $result = " ( " . $method->invoke($queryBuilder) . " ) ";
                array_push($carry, $result);
            }

            return $carry;
        }, array());
        return implode(" AND ", $where_array);
    }

    /**
     * generateOrWhereString
     * @param array $rows
     * @return string
     */
    private function generateOrWhereString($rows) {
        $where_array = array_reduce($rows, function ($carry, $item) {
            if (is_array($item)) {

                switch ($item[1]) {

                case 'in':
                    array_push($carry, $item[0] . " " . $item[1] . " ( " . implode(", ", array_fill(0, count($item[2]), " ?")) . " ) ");
                    break;

                case 'not in':
                    array_push($carry, $item[0] . " " . $item[1] . " ( " . implode(", ", array_fill(0, count($item[2]), " ?")) . " ) ");
                    break;

                case 'between':
                    array_push($carry, $item[0] . " " . $item[1] . implode(" AND ", array_fill(0, count($item[2]), " ?")));
                    break;

                default:
                    if (is_null($item[2])) {
                        array_push($carry, $item[0] . " IS NULL ");
                    } else {
                        array_push($carry, $item[0] . " " . $item[1] . " ? ");
                    }
                    break;
                }
            }

            if (is_callable($item)) {
                $queryBuilder = new self($this->getDBConnection());
                $item($queryBuilder);
                $queryBuilderReflection = new \ReflectionObject($queryBuilder);
                $method = $queryBuilderReflection->getMethod('generateWhereString');
                $method->setAccessible(true);
                $result = " ( " . $method->invoke($queryBuilder) . " ) ";
                array_push($carry, $result);
            }
            return $carry;
        }, array());
        return implode(" OR ", $where_array);
    }

    /**
     * generateWhereString
     * @return string
     */
    private function generateWhereString() {
        $where_conditions = $this->getWhereConditions();
        $where_string = "";
        foreach ($where_conditions as $val) {
            if (isset($val['AND'])) {
                $and_string = $this->generateAndWhereString($val['AND']);
                if (!empty($where_string)) {
                    $where_string .= " AND " . $and_string;
                } else {
                    $where_string = $and_string;
                }
            }

            if (isset($val['OR'])) {
                $or_string = $this->generateOrWhereString($val['OR']);
                if (!empty($where_string)) {
                    $where_string .= " OR " . $or_string;
                } else {
                    $where_string = $or_string;
                }
            }
        }
        return $where_string;
    }

    /**
     * generateWhereParams
     * @return array array of where parameter
     */
    private function generateWhereParams() {
        $where_conditions = $this->getWhereConditions();
        $where_params = [];
        foreach ($where_conditions as $row) {
            $data = isset($row['OR']) ? $row['OR'] : $row['AND'];
            $params = array_reduce($data, function ($carry, $item) {
                if (is_array($item)) {
                    switch ($item[1]) {

                    case 'in':
                        $carry = array_merge($carry, (array) $item[2]);
                        break;

                    case 'not in':
                        $carry = array_merge($carry, (array) $item[2]);
                        break;

                    case 'between':
                        $carry = array_merge($carry, (array) $item[2]);
                        break;

                    default:
                        if (!is_null($item[2])) {
                            $carry[] = $item[2];
                        }
                        break;
                    }
                }

                if (is_callable($item)) {
                    $queryBuilder = new self($this->getDBConnection());
                    $item($queryBuilder);
                    $queryBuilderReflection = new \ReflectionObject($queryBuilder);
                    $method = $queryBuilderReflection->getMethod('generateWhereParams');
                    $method->setAccessible(true);
                    $result = $method->invoke($queryBuilder);
                    $carry = array_merge($carry, $result);
                }

                return $carry;
            }, array());
            $where_params = array_merge($where_params, $params);
        }
        return $where_params;
    }

    /**
     * generateOrderByString
     * @return string order string
     */
    private function generateOrderByString() {
        $order_by_columns = $this->getOrderByColumns();
        $order_by_string = null;
        if (!empty($order_by_columns)) {
            $order_by_array = array_reduce($order_by_columns, function ($carry, $item) {
                $carry[] = $item[0] . " " . $item[1];
                return $carry;
            }, array());
            $order_by_string = implode(", ", $order_by_array);
        }
        return $order_by_string;
    }

    /**
     * generateJoinConditionString
     * @return string join condition string
     */
    public function generateJoinConditionString() {
        $join_condition_string = null;
        $join_conditions = $this->getJoinConditions();

        if (!empty($join_conditions)) {
            $join_conditions_string_array = array_reduce($join_conditions, function ($carry, $item) {
                $table_name = $item[0];
                $condition_type = $item[1];
                $on_conditions = $item[2];
                $on_array = array_reduce($on_conditions, function ($inner_carry, $inner_item) {
                    $inner_carry[] = $inner_item[0] . " " . $inner_item[1] . " " . $inner_item[2];
                    return $inner_carry;
                }, array());
                $carry[] = $condition_type . " " . $table_name . " ON ( " . implode(" AND ", $on_array) . " ) ";
                return $carry;
            }, array());
            $join_condition_string = " " . implode(" ", $join_conditions_string_array) . " ";
        }
        return $join_condition_string;
    }

    /**
     * generateGroupByString
     * Generate Group by string
     * @return string
     */
    public function generateGroupByString() {
        $group_by_string = null;
        $group_by_columns = $this->getGroupByColumns();
        if (!empty($group_by_columns)) {
            $group_by_string = implode(", ", $group_by_columns);
        }
        return $group_by_string;
    }

    /**
     * generateHavingString
     * @return string Haing string
     */
    private function generateHavingString() {
        $having_string = null;
        $having_columns = $this->getHavingColumns();
        if (count($having_columns) > 0) {
            $having_array = array_reduce($having_columns, function ($carry, $item) {
                $row = "";
                switch ($item[1]) {
                case 'in':
                    if (!is_array($item[2])) {
                        $item[2] = [$item[2]];
                    }
                    $item[2] = array_fill(0, count($item), '?');
                    $row = $item[0] . " " . $item[1] . " ( " . implode(", ", $item[2]) . " ) ";
                    break;
                case 'not in':
                    if (!is_array($item[2])) {
                        $item[2] = [$item[2]];
                    }
                    $item[2] = array_fill(0, count($item), '?');
                    $row = $item[0] . " " . $item[1] . " ( " . implode(", ", $item[2]) . " ) ";
                    break;
                case 'between':
                    $item[2] = array_fill(0, count($item[2]), '?');
                    $row = $item[0] . " " . $item[1] . " " . implode(" AND ", $item[2]) . " ";
                    break;
                default:
                    $row = $item[0] . " " . $item[1] . "  ? ";
                    break;
                }
                $carry[] = $row;
                return $carry;
            }, []);
            $having_string = implode(" AND ", $having_array);
        }
        return $having_string;
    }

    /**
     * generate Where Params
     * @return array
     */
    private function generateHavingParams() {
        $out = array();
        $having_columns = $this->getHavingColumns();
        $out = array_reduce($having_columns, function ($carry, $item) {
            if (is_array($item[2])) {
                $carry = array_merge($carry, $item[2]);
            } else {
                $carry[] = $item[2];
            }
            return $carry;
        }, []);
        return $out;
    }

    public function reset() {
        $this->setTableName(null);
    }

    /**
     * select
     * Select columns from table
     * @param string|array
     * @return QueryBuilder $this''
     */
    public function select($value) {
        $this->setSelectColumnNames(array_merge($this->getSelectColumnNames(), (array) $value));
        return $this;
    }

    /**
     * where
     * Where condition
     * @param string|array $column_name
     * @param string $operator Operator
     * @param string $column_value
     * @return DBQueryBuilder $this
     * @throws DBQueryBuilderException
     * EXample where('usename' 'admin');
     * EXample where('usename', '=', 'admin');
     * EXample where(['usename', 'admin']);
     * EXample where(['usename', '=', 'admin']);
     * EXample where([['usename', '=', 'admin']['name', '<>' 'admin']]);
     * EXample where([['usename', 'admin'], ['age', '20']]);
     */
    public function where($column_name, $operator = null, $column_value = null) {
        $number_of_arguments = func_num_args();
        $current_where_conditions = $this->getWhereConditions();
        $new_where_conditions = [];
        // If $column_name is array
        // for index array type
        if ($number_of_arguments === 1 && is_array($column_name) && !$this->isAssociativeArray($column_name)) {

            if (count($column_name) == 2 && !is_array($column_name[0])) {
                $new_where_conditions[] = [$column_name[0], '=', $column_name[1]];
            }

            if (count($column_name) == 3 && !is_array($column_name[0])) {
                if (!$this->isOperatorExists($column_name[1])) {
                    throw new DBQueryBuilderException('Invalid Operator in where condition Operator is " ' . $column_name[1] . '"');
                }
                $new_where_conditions[] = [$column_name[0], $column_name[1], $column_name[2]];
            }
            foreach ($column_name as $val) {
                if (!is_array($val)) {
                    throw new DBQueryBuilderException('Invalid where condition parameter');
                }
                if (count($val) === 2) {
                    $new_where_conditions[] = [$val[0], '=', $val[1]];
                }
                if (count($val) === 3) {
                    if (!$this->isOperatorExists($val[1])) {
                        throw new DBQueryBuilderException('Invalid Operator in where condition, Operator is "' . $val[1] . '"');
                    }
                    $new_where_conditions[] = [$val[0], $val[1], $val[2]];
                }
            }
        }
        // for associative array type
        if ($number_of_arguments === 1 && is_array($column_name) && $this->isAssociativeArray($column_name)) {
            foreach ($column_name as $key => $val) {
                $new_where_conditions[] = [$key, '=', $val];
            }
        }

        if ($number_of_arguments === 2) {
            $new_where_conditions[] = [$column_name, '=', $operator];
        }

        if ($number_of_arguments === 3) {
            if (!$this->isOperatorExists($operator)) {
                throw new DBQueryBuilderException('Invalid where condition operator, Operator is "' . $operator . '" ');
            }
            $new_where_conditions[] = [$column_name, $operator, $column_value];
        }

        if (is_callable($column_name)) {
            $new_where_conditions[] = $column_name;
        }

        if (count($current_where_conditions) > 0) {
            $key = array_keys($current_where_conditions[count($current_where_conditions) - 1]);
            if ($key[0] === 'AND') {
                $current_where_conditions[count($current_where_conditions) - 1]['AND'] = array_merge($current_where_conditions[count($current_where_conditions) - 1]['AND'], array_values($new_where_conditions));
            } else {
                $current_where_conditions[] = ['AND' => $new_where_conditions];
            }
        } else {
            $current_where_conditions[] = ['AND' => $new_where_conditions];
        }
        $this->setWhereConditions($current_where_conditions);
        return $this;
    }

    /**
     * orWhere
     * Or Where condition
     * @param string|array $column_name
     * @param string $operator Operator
     * @param string $column_value
     * @return DBQueryBuilder $this
     * @throws DBQueryBuilderException
     * EXample orWhere('usename' 'admin');
     * EXample orWhere('usename', '=', 'admin');
     * EXample orWhere(['usename', 'admin']);
     * EXample orWhere(['usename', '=', 'admin']);
     * EXample orWhere([['usename', '=', 'admin']['name', '<>' 'admin']]);
     * EXample orWhere([['usename', 'admin'], ['age', '20']]);
     */
    public function orWhere($column_name, $operator = null, $column_value = null) {
        $number_of_arguments = func_num_args();

        $current_where_conditions = $this->getWhereConditions();
        $new_where_conditions = [];
        // If $column_name is array
        if ($number_of_arguments === 1 && is_array($column_name)) {
            if (count($column_name) == 2 && !is_array($column_name[0])) {
                $number_of_arguments[] = [$column_name[0], '=', $column_name[1]];
            }
            if (count($column_name) == 3 && !is_array($column_name[0])) {
                if (!$this->isOperatorExists($column_name[1])) {
                    throw new DBQueryBuilderException('Invalid Operator in where condition Operator is " ' . $column_name[1] . '"');
                }
                $new_where_conditions = [$column_name[0], $column_name[1], $column_name[2]];
            }
            foreach ($column_name as $val) {
                if (!is_array($val)) {
                    throw new DBQueryBuilderException('Invalid where condition parameter');
                }
                if (count($val) === 2) {
                    $new_where_conditions[] = [$val[0], '=', $val[1]];
                }
                if (count($val) === 3) {
                    if (!$this->isOperatorExists($val[1])) {
                        throw new DBQueryBuilderException('Invalid Operator in where condition, Operator is "' . $val[1] . '"');
                    }
                    $new_where_conditions[] = [$val[0], $val[1], $val[2]];
                }
            }
        }

        if ($number_of_arguments === 2) {
            $new_where_conditions[] = [$column_name, '=', $operator];
        }

        if ($number_of_arguments === 3) {
            if (!$this->isOperatorExists($operator)) {
                throw new DBQueryBuilderException('Invalid where condition operator, Operator is "' . $operator . '" ');
            }
            $new_where_conditions[] = [$column_name, $operator, $column_value];
        }

        if (is_callable($column_name)) {
            $new_where_conditions[] = $column_name;
        }

        if (count($current_where_conditions) > 0) {
            $key = array_keys($current_where_conditions[count($current_where_conditions) - 1]);
            if ($key[0] === 'OR') {
                $current_where_conditions[count($current_where_conditions) - 1]['OR'] = array_merge($current_where_conditions[count($current_where_conditions) - 1]['OR'], $new_where_conditions);
            } else {
                $current_where_conditions[] = ['OR' => $new_where_conditions];
            }
        } else {
            $current_where_conditions[] = ['OR' => $new_where_conditions];
        }
        $this->setWhereConditions($current_where_conditions);
        return $this;
    }

    /**
     * whereIn
     * Where in
     * @param string $column_name column name
     * @param array $in array of expected data
     * @return QueryBuilder $this
     */
    public function whereIn($column_name, $in) {
        return $this->where($column_name, 'in', $in);
    }

    /**
     * whereIn
     * Where in
     * @param string $column_name column name
     * @param array $in array of expected data
     * @return QueryBuilder $this
     */
    public function whereNotIn($column_name, $in) {
        return $this->where($column_name, 'not in', $in);
    }

    /**
     * pluck
     * pluck a single column data
     * @param string|array $column_name column name
     * @return QueryBuilder $this
     */
    public function pluck($column_name) {
        $this->select($column_name);
        $map_functions = $this->getMapFunction();
        $new_function = function ($result) {
            $selected_column = $this->getSelectColumnNames();
            $selected_column = strtoupper(array_pop($selected_column));
            return $result[$selected_column];
        };
        array_unshift($map_functions, $new_function);
        $this->setMapFunction($map_functions);
        return $this->get();
    }

    /**
     * max
     * Find max of a value
     * @param string $column_name column name
     * @return QueryBuilder $this
     */
    public function max($column_name) {
        $this->select("max(" . $column_name . ") as MAX_VAL");
        $result = $this->get();
        return $result[0]['MAX_VAL'];
    }

    /**
     * min
     * Find max of a value
     * @param string $column_name column name
     * @return string
     */
    public function min($column_name) {
        $this->select("min(" . $column_name . ") as MIN_VAL");
        $result = $this->get();
        return $result[0]['MIN_VAL'];
    }

    /**
     * count
     * Get count of result
     * @return QueryBuilder $this
     */
    public function count() {
        $this->select("count(*) AS CNT");
        $map_functions = $this->getMapFunction();
        array_unshift($map_functions, function ($row) {
            return $row['CNT'];
        });
        $this->setMapFunction($map_functions);
        $result = $this->get();
        return array_pop($result);
    }

    /**
     * orderBy
     * @param string $column_name Column name
     * @param string $direction Optional default asc allowed values [asc, desc]
     * @return QueryBuilder $this
     */
    public function orderBy($column_name, $direction = 'asc') {
        $column_name = strtoupper($column_name);
        $direction = strtoupper($direction);
        $order_by_columns = $this->getOrderByColumns();
        $order_by_columns[] = [$column_name, $direction];
        $this->setOrderByColumns($order_by_columns);
        return $this;
    }

    /**
     * orderByDesc
     * Order by desc
     * @param string $column_name Column name
     * @return QueryBuilder $this
     */
    public function orderByDesc($column_name) {
        return $this->orderBy($column_name, 'desc');
    }

    /**
     * limit
     * Limit number of record
     * @param int $value limit number
     * @return QueryBuilder $this
     */
    public function limit($value) {
        $this->setLimit($value);
        return $this;
    }

    /**
     * offset
     * Set offset of result
     * @param int $value offset value
     * @return QueryBuilder $this
     */
    public function offset($value) {
        $this->setOffset($value);
        return $this;
    }

    /**
     * join
     * Join Table
     * @param string $table_name joining table name
     * @param string|array $first_column join table column or array of on condition
     * @param string $operator Optional example = > etc
     * @param string $second_column_name Optional Second column name
     * @return DBQueryBuilder
     */
    public function join($table_name, $first_column_name, $operator, $second_column_name) {
        $join_conditions = $this->getJoinConditions();
        if (is_array($first_column_name)) {
            if (count($first_column_name) > 0 && is_array($first_column_name[0])) {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'INNER JOIN ', $first_column_name]]);
            } else {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'INNER JOIN ', [$first_column_name]]]);
            }
        } else {
            $join_conditions = array_merge($join_conditions, [[$table_name, 'INNER JOIN ', [[$first_column_name, $operator, $second_column_name]]]]);
        }
        $this->setJoinConditions($join_conditions);
        return $this;
    }

    /**
     * left join
     * left Join Table
     * @param string $table_name joining table name
     * @param string|array $first_column join table column or array of on condition
     * @param string $operator Optional example = > etc
     * @param string $second_column_name Optional Second column name
     * @return DBQueryBuilder
     */
    public function leftJoin($table_name, $first_column_name, $operator, $second_column_name) {
        $join_conditions = $this->getJoinConditions();
        if (is_array($first_column_name)) {
            if (count($first_column_name) > 0 && is_array($first_column_name[0])) {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'LEFT JOIN ', $first_column_name]]);
            } else {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'LEFT JOIN ', [$first_column_name]]]);
            }
        } else {
            $join_conditions = array_merge($join_conditions, [[$table_name, 'LEFT JOIN ', [[$first_column_name, $operator, $second_column_name]]]]);
        }
        $this->setJoinConditions($join_conditions);
        return $this;
    }

    /**
     * Right join
     * Right Join Table
     * @param string $table_name joining table name
     * @param string|array $first_column join table column or array of on condition
     * @param string $operator Optional example = > etc
     * @param string $second_column_name Optional Second column name
     * @return DBQueryBuilder
     */
    public function rightJoin($table_name, $first_column_name, $operator, $second_column_name) {
        $join_conditions = $this->getJoinConditions();
        if (is_array($first_column_name)) {
            if (count($first_column_name) > 0 && is_array($first_column_name[0])) {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'RIGHT JOIN ', $first_column_name]]);
            } else {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'RIGHT JOIN ', [$first_column_name]]]);
            }
        } else {
            $join_conditions = array_merge($join_conditions, [[$table_name, 'RIGHT JOIN ', [[$first_column_name, $operator, $second_column_name]]]]);
        }
        $this->setJoinConditions($join_conditions);
        return $this;
    }

    /**
     * Right join
     * Right Join Table
     * @param string $table_name joining table name
     * @param string|array $first_column join table column or array of on condition
     * @param string $operator Optional example = > etc
     * @param string $second_column_name Optional Second column name
     * @return DBQueryBuilder
     */
    public function outerJoin($table_name, $first_column_name, $operator, $second_column_name) {
        $join_conditions = $this->getJoinConditions();
        if (is_array($first_column_name)) {
            if (count($first_column_name) > 0 && is_array($first_column_name[0])) {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'FULL OUTER JOIN ', $first_column_name]]);
            } else {
                $join_conditions = array_merge($join_conditions, [[$table_name, 'FULL OUTER JOIN ', [$first_column_name]]]);
            }
        } else {
            $join_conditions = array_merge($join_conditions, [[$table_name, 'FULL OUTER JOIN ', [[$first_column_name, $operator, $second_column_name]]]]);
        }
        $this->setJoinConditions($join_conditions);
        return $this;
    }

    /**
     * groupBy
     * Group by recoard
     * @param string|array $value group by columns
     * @return DBQueryBuilder
     */
    public function groupBy($value) {
        $group_by_columns = $this->getGroupByColumns();
        if (is_array($value)) {
            $group_by_columns = array_merge($group_by_columns, $value);
        } else {
            $group_by_columns[] = $value;
        }
        $this->setGroupByColumns($group_by_columns);
        return $this;
    }

    /**
     * having
     * Having in a group of records
     * @param string|array $columns_name column name
     * @param string $operator Operator
     * @param string $value
     * @return DBQueryBuilder
     */
    public function having($column_name, $operator = null, $value = "") {
        $having_column = $this->getHavingColumns();
        if (is_array($column_name)) {
            if (is_array($column_name[0])) {
                $having_column = array_merge($having_column, $column_name);
            } else {
                $having_column = array_merge($having_column, [$column_name]);
            }
        } else {
            if ($value === "") {
                $having_column[] = [$column_name, "=", $operator];
            } else {
                $having_column[] = [$column_name, $operator, $value];
            }
        }
        $this->setHavingColumns($having_column);
        return $this;
    }

    /**
     * union
     * Union two or multiple resultset
     * @param DBQueryBuilderInterface|array $queryBuilder
     * @return DBQueryBuilderInterface
     */
    public function union($queryBuilder) {
        $union_result_set = $this->getUnionResultSet();
        if (!is_array($queryBuilder)) {
            $queryBuilder = [$queryBuilder];
        }
        $new_union = array_reduce($queryBuilder, function ($carry, $item) {
            $carry[] = [static::UNION, $item];
            return $carry;
        }, []);
        $union_result_set = array_merge($union_result_set, $new_union);
        $this->setUnionResultSet($union_result_set);
        return $this;
    }

    /**
     * unionAll
     * Union two or multiple resultset
     * @param DBQueryBuilderInterface|array $queryBuilder
     * @return DBQueryBuilderInterface
     */
    public function unionAll($queryBuilder) {
        $union_result_set = $this->getUnionResultSet();
        if (!is_array($queryBuilder)) {
            $queryBuilder = [$queryBuilder];
        }
        $new_union = array_reduce($queryBuilder, function ($carry, $item) {
            $carry[] = [static::UNION_ALL, $item];
            return $carry;
        }, []);
        $union_result_set = array_merge($union_result_set, $new_union);
        $this->setUnionResultSet($union_result_set);
        return $this;
    }

    /**
     * insert
     * Insert new record
     * @param array $value Associative array of column name and value
     * @return boolean true on success
     */
    public function insert($value) {
        // if single row to insert then convert to index array
        if ($this->isAssociativeArray($value)) {
            $value = [$value];
        }
        $column_names = array_keys($value[0]);
        $params = array();
        $data_rows = array_reduce($value, function ($carry, $item) use (&$params) {
            $params = array_merge($params, array_values($item));
            $carry[] = array_values($item);
            return $carry;
        }, array());
        $data_string_array = array_fill(0, count($value), " ( " . implode(", ", array_fill(0, count($column_names), "? ")) . " ) ");
        $sql = " INSERT INTO " . $this->getTableName();
        $sql .= " ( " . implode(', ', $column_names) . " ) ";
        $sql .= " VALUES " . implode(", ", $data_string_array);
        try {
            return $this->getDBConnection()->executeQuery($sql, $params);
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * insert
     * Insert new record
     * @param array $value Associative array of column name and value
     * @return int auto increment id on success
     * @throws DBQueryBuilderException
     */
    public function insertGetId($value) {
        // if single row to insert then convert to index array
        if ($this->isAssociativeArray($value)) {
            $value = [$value];
        }
        $column_names = array_keys($value[0]);
        $params = array();
        $data_rows = array_reduce($value, function ($carry, $item) use (&$params) {
            $params = array_merge($params, array_values($item));
            $carry[] = array_values($item);
            return $carry;
        }, array());
        $data_string_array = array_fill(0, count($value), " ( " . implode(", ", array_fill(0, count($column_names), "? ")) . " ) ");
        $sql = " INSERT INTO " . $this->getTableName();
        $sql .= " ( " . implode(', ', $column_names) . " ) ";
        $sql .= " VALUES " . implode(", ", $data_string_array);
        try {
            $this->getDBConnection()->executeQuery($sql, $params);
            return $this->getDBConnection()->getLastInsertId();
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * update
     * Update a data in table
     * @return boolean true
     */
    public function update($value) {
        $table_name = $this->getTableName();
        $column_names = array_keys($value);
        $column_update_array = array_reduce($column_names, function ($carry, $item) {
            $carry[] = $item . " = ? ";
            return $carry;
        }, array());
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $values = array_values($value);
        $sql = " UPDATE " . $table_name . " SET ";
        $sql .= implode(", ", $column_update_array);
        if (!empty($where_string)) {
            $sql .= " WHERE " . $where_string;
        }
        $params = array_merge($values, $where_params);
        try {
            return $this->getDBConnection()->executeQuery($sql, $params);
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * increment
     * Increment a column value in table
     * @param string $column_name
     * @param int $amount number to increment
     * @return boolean true on success
     */
    public function increment($column_name, $amount = 1) {
        $amount = (int) $amount;
        $table_name = $this->getTableName();
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $sql = " UPDATE " . $table_name . " SET " . $column_name . " = " . $column_name . " + " . $amount;
        $params = [];
        if (!empty($where_string)) {
            $sql .= " WHERE " . $where_string;
            $params = array_merge($params, $where_params);
        }
        try {
            return $this->getDBConnection()->executeQuery($sql, $params);
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * decrement
     * Decrement a column value in table
     * @param string $column_name Column name
     * @param int $amount number to decrement
     * @return boolean true on success
     */
    public function decrement($column_name, $amount = 1) {
        $amount = (int) $amount;
        $table_name = $this->getTableName();
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $sql = " UPDATE " . $table_name . " SET " . $column_name . " = " . $column_name . " - " . $amount;
        $params = [];
        if (!empty($where_string)) {
            $sql .= " WHERE " . $where_string;
            $params = array_merge($params, $where_params);
        }
        try {
            return $this->getDBConnection()->executeQuery($sql, $params);
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * delete a recoard from table
     * @return boolean true on success
     */
    public function delete() {
        $table_name = $this->getTableName();
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $sql = " DELETE FROM " . $table_name;
        $params = array();
        if (!empty($where_string)) {
            $sql .= " WHERE " . $where_string;
            $params = array_merge($params, $where_params);
        }
        try {
            return $this->getDBConnection()->executeQuery($sql, $params);
        } catch (\Exception $e) {
            throw new DBQueryBuilderException($e->getMessage());
        }
    }

    /**
     * getSelectSqlQuery
     * @return string;
     */
    private function getSelectSqlQuery() {
        $table_name = $this->_table_name;
        if ($table_name == null) {
            throw new QueryBuilderException('Table name is empty');
        }
        $select_string = $this->generateSelectString();
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $order_by_string = $this->generateOrderByString();
        $join_condition_string = $this->generateJoinConditionString();
        $group_by_string = $this->generateGroupByString();
        $having_string = $this->generateHavingString();
        $having_params = $this->generateHavingParams();
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $sql = "SELECT " . $select_string;
        $params = array();
        if ($limit !== null && $offset === null) {
            $sql .= " FROM " . $table_name;
            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }
            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }
            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            if (!empty($order_by_string)) {
                $sql .= " ORDER BY " . $order_by_string;
            }
            $sql .= " FETCH FIRST " . $limit . " ROWS ONLY ";
        } else if ($limit !== null && $offset !== null) {
            $sql = "   SELECT * ";
            if (!empty($order_by_string)) {
                $order_by_string = " ORDER BY " . $order_by_string;
            } else {
                $order_by_string = "";
            }
            $sql .= " FROM ( SELECT ROW_NUMBER() OVER (" . $order_by_string . ") AS ROW_ID , DATA.* FROM ( SELECT " . $select_string . " FROM " . $table_name;

            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }

            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }
            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            $sql .= " ) as DATA ) WHERE ROW_ID BETWEEN " . $offset . " AND " . ($offset + ($limit - 1));
        } else {
            $sql .= " FROM " . $table_name;

            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }

            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }

            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            if (!empty($order_by_string)) {
                $sql .= " ORDER BY " . $order_by_string;
            }
        }
        return $sql;
    }

    /**
     * getSelectSqlQueryParams
     * @return array
     */
    private function getSelectSqlQueryParams() {
        $table_name = $this->_table_name;
        if ($table_name == null) {
            throw new QueryBuilderException('Table name is empty');
        }
        $select_string = $this->generateSelectString();
        $where_string = $this->generateWhereString();
        $where_params = $this->generateWhereParams();
        $order_by_string = $this->generateOrderByString();
        $join_condition_string = $this->generateJoinConditionString();
        $group_by_string = $this->generateGroupByString();
        $having_string = $this->generateHavingString();
        $having_params = $this->generateHavingParams();
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $sql = "SELECT " . $select_string;
        $params = [];
        if ($limit !== null && $offset === null) {
            $sql .= " FROM " . $table_name;
            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }
            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }
            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            if (!empty($order_by_string)) {
                $sql .= " ORDER BY " . $order_by_string;
            }
            $sql .= " FETCH FIRST " . $limit . " ROWS ONLY ";
        } else if ($limit !== null && $offset !== null) {
            $sql = " SELECT * ";
            if (!empty($order_by_string)) {
                $order_by_string = " ORDER BY " . $order_by_string;
            } else {
                $order_by_string = "";
            }
            $sql .= " FROM ( SELECT ROW_NUMBER() OVER (" . $order_by_string . ") AS ROW_ID , DATA.* FROM ( SELECT " . $select_string . " FROM " . $table_name;

            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }

            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }
            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            $sql .= " ) as DATA ) WHERE ROW_ID BETWEEN " . $offset . " AND " . ($offset + ($limit - 1));
        } else {
            $sql .= " FROM " . $table_name;

            if (!empty($join_condition_string)) {
                $sql .= $join_condition_string;
            }

            if (!empty($where_string)) {
                $sql .= " WHERE " . $where_string;
                $params = array_merge($params, $where_params);
            }

            if (!empty($group_by_string)) {
                $sql .= " GROUP BY " . $group_by_string;
            }

            if (!empty($having_string)) {
                $sql .= " HAVING " . $having_string;
                $params = array_merge($params, $having_params);
            }

            if (!empty($order_by_string)) {
                $sql .= " ORDER BY " . $order_by_string;
            }
        }
        return $params;
    }

    /***********************************************************************************************'
     * ----------------------------------------------------------------------------------------------
     * Helper Methods
     * ----------------------------------------------------------------------------------------------
     */

    /**
     * isOperatorExists
     * @param string $operator Operator
     * @return boolean
     */
    private function isOperatorExists($operator) {
        return in_array($operator, $this->_available_operators);
    }

    /**
     * isAssociativeArray
     * @param array $value
     * @return boolean true on success and false if not associative array
     */
    private function isAssociativeArray($value) {
        if (!is_array($value)) {
            return false;
        }
        return array_keys($value) !== range(0, count($value) - 1);
    }
}
