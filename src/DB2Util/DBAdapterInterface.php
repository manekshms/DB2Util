<?php
    namespace DB2Util;
    
    use \DB2Util\DBConnection\ConnectionInterface;
    use \DB2Util\DBConnection\DBConnectionInterface;
    use \DB2Util\DBConnection\DBConnectionFactoryInterface;
    use \DB2Util\DBQueryBuilder\DBQueryBuilderFactoryInterface;

	interface DbAdapterInterface{

        /**
         * connect 
         */  
        public function connect();

        /**
         * SetAdapterConfig 
         * @param DB2UtilConfig $adapterConfig
         */
        public function setAdapterConfig($adapterConfig);

        /**
         * getAdapterConfig 
         * @return DB2UtilConfig
         */
        public function getAdapterConfig();


        /**
         * setConnectionFactory
         * @param ConnectionConfigInterface $connecitonFactory
         */
        public function setConnectionFactory(DBConnectionFactoryInterface $connecitonFactory);

        /**
         * getConnectionFactory
         * @return  DBConnectinFactoryInterface
         */
        public function getConnectionFactory();


        /**
         * setQueryBuilderFactory
         * Set query builder factory
         * @param QueryBuilderFactoryInterface $queryBuilderFactory
         */
        public function setQueryBuilderFactory(DBQueryBuilderFactoryInterface $queryBuilderFactory);

        /**
         * getQueryBuilderFactory
         * Get query builder factory
         * @return QueryBuilderFactoryInterface
         */
        public function getQueryBuilderFactory();


        /**
         * getQueryBuilder
         * @return QueryBuilderInterface
         */
        public function getQueryBuilder();

        /**
         * Set Connection
         * Set Database Connection Object
         * @param  ConnectionInterface $DBConnection
         * @return void
         */
        public function setConnection(DBConnectionInterface $DBConnection);

        /**
         * Get connection
         * Get Database connection Object
         * @return ConnectionInterface
         */
        public function getConnection();
        
        /**
         * Select
         * Select Data from database
         * @param string $table Table name
         * @param string|array $column Select Column names
         * @param array $where_conditions Optional Where Condition example ['username' => 'adminuser']
         * @param array $order_by Optional Order by example ['name' => 'asc']
         * @param string $limit limit example 10
         * @param string $offset offset example 5
         * @return array
         * @throws DB2UtilException
         */
		public function select($table, $select_column_names = "*", $where_conditions = "",  $order_by = "", $limit = "", $offset = null);

        /**
         * Insert 
         * Insert new entry in table
         * @param string $table table name
         * @param array $data associative array containing column name and value example ['name' => 'foo', 'country' => 'UK'] 
         * @return boolean true on success
         * @throws DB2UtilException on error
         */
		public function insert($table, array $data);

        /**
         * Update
         * Update data in a table
         * @param string $table table name 
         * @param array $data data to update, Associative array with column name and values
         * @param array $where_conditions , Associative array with column and value
         * @return boolean true on success
         * @throws DB2UtilException
         */
		public function update($table, array $data, array $where_conditions);

        /**
         * Delete
         * Delete a row from table
         * @param string $table table name 
         * @param array $where_conditions, Associative array with where conditions
         * @return boolean true on success
         * @throws DB2UtilException
         */
        public function delete($table, $where_conditions);
        

        /**
         * Count
         * Get Count of records
         * @param string $table_name Table name 
         * @param array $where_conditions Optional associative array example ['name' = 'bob']
         */
        public function count($table_name, $where_conditions);

	}	
?>