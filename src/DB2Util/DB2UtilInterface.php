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
        
	}	
?>