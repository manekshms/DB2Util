<?php
    namespace DB2Util\DBQueryBuilder;
    use \DB2Util\DBConnection\DBConnectionInterface;

    interface DBQueryBuilderInterface{

        /**
         * setDBConnection
         * @param DBConnection database connection
         */
        public function setDBConnection(DBConnectionInterface $dbConn);

        /**
         * getDBConnection
         * @return DBConnection
         */
        public function getDBConnection();

        /**
         * table
         * Table name
         * @param string $table_name table name
         * @return QueryBuilderInterface $this;
         */
        public function table($table_name);

        /**
         * get
         * Get data from database
         * @return array Resut array
         */
        public function get();

        /**
         * mapResult
         * @param function $cb_function
         */
        public function mapResult($cb_function);

        /**
         * select
         * Select columns from table
         * @param string|array 
         * @return QueryBuilder $this
         */
        public function select($value);

        /**
         * where
         * Where condition
         * @param string|array $column_name 
         * @param string $operator Operator
         * @param string $column_value
         * @return QueryBuilder $this
         */
        public function where($column_name, $operator, $column_value);

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
        public function orWhere($column_name, $operator, $column_value);

        /**
         * whereIn
         * Where in
         * @param string $column_name column name
         * @param array $in array of expected data
         * @return QueryBuilder $this
         */
        public function whereIn($column_name, $in);


        /**
         * whereIn
         * Where Not in
         * @param string $column_name column name
         * @param array $in array
         * @return QueryBuilder $this
         */
        public function whereNotIn($column_name, $in);

        /**
         * pluck
         * pluck a single column data
         * @param string $column_name column name
         * @return array
         */
        public function pluck($column_name);

        /**
         * max
         * Find max of a value
         * @param string $column_name column name
         * @return mixed
         */
        public function max($column_name);


        /**
         * min
         * Find min of a value
         * @param string $column_name column name
         * @return mixed
         */
        public function min($column_name);

        /**
         * count 
         * Get count of result
         * @return int
         */
        public function count();

        /**
         * orderBy
         * @param string $column_name Column name
         * @param string $direction Optional default asc allowed values [asc, desc]
         * @return QueryBuilder $this
         */
        public function orderBy($column_name, $direction = 'asc');


        /**
         * orderByDesc
         * Order by desc
         * @param string $column_name Column name
         * @return QueryBuilder $this
         */
        public function orderByDesc($column_name);

        /**
         * limit
         * Limit number of record
         * @param int $value limit number
         * @return QueryBuilder $this
         */
        public function limit($value);

        /**
         * offset
         * Set offset of result
         * @param int $value offset value
         * @return QueryBuilder $this
         */
        public function offset($value);


        /**
         * join
         * Join Table
         * @return DBQueryBuilder
         * @param string $table_name joining table name
         * @param string|array $first_column join table column or array of on condition
         * @param string $operator Optional example = > etc
         * @param string $second_column_name Optional Second column name 
         */
        public function join($table_name, $first_column_name, $operator, $second_column_name);


        /**
         * groupBy
         * Group by recoard
         * @param string|array $value group by columns
         * @return DBQueryBuilder
         */
        public function groupBy($value);


        /**
         * having
         * Having in a group of records
         * @param string|array $columns_name column name
         * @param string $operator Operator
         * @param string $value
         * @return DBQueryBuilder
         */
        public function having($column_name, $operator, $value);


        /**
         * union
         * Union two or multiple resultset
         * @param DBQueryBuilderInterface|array $queryBuilder
         * @return DBQueryBuilderInterface
         */
        public function union($queryBuilder);


        /**
         * unionAll
         * Union two or multiple resultset
         * @param DBQueryBuilderInterface|array $queryBuilder
         * @return DBQueryBuilderInterface
         */
        public function unionAll($queryBuilder);


        /**
         * insert
         * Insert new record
         * @param array $value Associative array of column name and value
         * @return boolean true on success
         */
        public function insert($value);


        /**
         * insert
         * Insert new record
         * @param array $value Associative array of column name and value
         * @return boolean int Insert id on success
         * @throws DBQueryBuilderException
         */
        public function insertGetId($value);
        

        /**
         * update
         * Update a data in table
         * @return boolean true
         */
        public function update($value);

        /**
         * increment
         * Increment a column value in table
         * @param string $column_name
         * @param int $amount number to increment
         * @return boolean true on success 
         */
        public function increment($column_name, $amount = 1);

        /**
         * decrement
         * Decrement a column value in table
         * @param string $column_name Column name
         * @param int $amount number to decrement
         * @return boolean true on success
         */
        public function decrement($column_name, $amount = 1);

        /**
         * delete a recoard from table
         * @return boolean true on success
         */
        public function delete();



    }
?>