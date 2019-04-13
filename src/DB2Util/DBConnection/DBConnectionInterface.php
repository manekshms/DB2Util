<?php
    namespace DB2Util\DBConnection;

    interface DBConnectionInterface {

        /**
         * getConnectionConfig
         * @return ConnectionConfigInterface
         */
        public function getConnectionConfig();

        /**
         * setConnectionConfig
         * @param ConnectionConfigInterface
         */
        public function setConnectionConfig(ConnectionConfigInterface $connectionConfig);


        /**
         * Connect
         * Connect to a database
         * @return void
         * @throws Exception
         */
        public function connect();


        /**
         * Disconnect
         * Disconnect database
         * @return void
         */
        public function disconnect();

        /**
         * Query
         * Run a SQL query return Statement
         * Run Select query
         * @param string $sql Sql query
         * @param array $params Bind parameter array
         * @return Statement
         * @throws DB2UtilException
         */
        public function query($sql, $params);

        /**
         * Execute Query
         * Execute query insert and update 
         * @param string $sql Sql query
         * @param array $params Bind parameter array
         * @return Statement
         * @throws DB2UtilException
         */
        public function executeQuery($sql, $params);

        /**
         * Get Last Insert Id
         * @return string String representation of Row Id 
         */
        public function getLastInsertId();

        /**
         * Begin Transaction
         * @return boolean true on success false on failure
         * @throws DB2UtilException
         */
        public function beginTransaction();

        /**
         * commit Transaction
         * @return boolean true on success false on failure
         * @throws DB2UtilException
         */
        public function commitTransaction();

        /**
         * Rollback Transaction
         * @return boolean true on success false on failure
         * @throws DB2UtilException
         */
        public function rollBackTransaction();

        /**
         * getLastQuery
         * @param boolean $debug default false 
         * if set true return associative array with last sql query and parameter and processed query 
         * Example : ['query' => 'select * from user where user_name => "', params => array('admin'), 'processed_query' =>' select * from user where user_name = "admin"' ]
         * @return string|array
         */
        public function getLastSQLQuery($debug = false);
    }

?>