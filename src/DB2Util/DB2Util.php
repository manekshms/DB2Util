<?php
/**
 * DB2Util
 * @package DB2Util 
 * @author Maneksh MS
 */
namespace DB2Util;
use \DB2Util\DBConnection\DBConnectionInterface;
use \DB2Util\DBConnection\DBConnectionFactoryInterface;
use \DB2Util\DBQueryBuilder\DBQueryBuilderFactoryInterface;

class DB2Util implements DbAdapterInterface{

    private $_connectionObj;

    private $_adapterConfig;

    private $_connectionFactory;

    private $_queryBuillderFactory;


    /**
     * Constructor
     * @param array Optional
     */
    public function __construct($config = null){
        if($config){
            $this->initializeConfig($config);
        }
    }
    
    /**
     * initializeConfig
     * @param array config array
     */
    private function initializeConfig($config){
        $dsn = $config['dsn'];
        $username = $config['username'];
        $password = $config['password'];
        $dbAdapterConfig = new DB2UtilConfig();
        $dbConnectionConfig = new DBConnectionConfig();
        $dbConnectionConfig->setDsn($dsn);
        $dbConnectionConfig->setUsername($username);
        $dbConnectionConfig->setPassword($password);
        $dbAdapterConfig->setConnectionConfig($dbConnectionConfig);
        $dbConnectionFactory = new \DB2Util\DBConnection\DBConnectionFactory();
        $dbQueryBuilderFactory = new \DB2Util\DBQueryBuilder\DBQueryBuilderFactory();
        $this->setConnectionFactory($dbConnectionFactory);
        $this->setQueryBuilderFactory($dbQueryBuilderFactory);
        $this->setAdapterConfig($dbAdapterConfig);
    }

    /**
     * connect
     * @throws DB2UtilException
     */
    public function connect(){
        $connectinConfig = $this->getAdapterConfig()->getConnectionConfig();
        $connctionObj = $this->getConnectionFactory()->getInstance($connectinConfig);
        try{
            $connctionObj->connect();
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
        $this->setConnection($connctionObj);
    }

    /**
     * SetAdapterConfig 
     * @param DB2UtilConfig $adapterConfig
     */
    public function setAdapterConfig($adapterConfig){
        $this->_adapterConfig = $adapterConfig;
    }

    /**
     * getAdapterConfig 
     * @return DB2UtilConfig
     */
    public function getAdapterConfig(){
        return $this->_adapterConfig;
    }


    /**
     * setConnectionFactory
     * @param DBConnectionFactoryInterface $connecitonFactory
     */
    public function setConnectionFactory(DBConnectionFactoryInterface $connecitonFactory){
        $this->_connectionFactory = $connecitonFactory;
    }

    /**
     * getConnectionFactory
     * @return  DBConnectinFactoryInterface
     */
    public function getConnectionFactory(){
        return $this->_connectionFactory;
    }


    /**
     * setQueryBuilderFactory
     * Set query builder factory
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     */
    public function setQueryBuilderFactory(DBQueryBuilderFactoryInterface $queryBuilderFactory){
        $this->_queryBuillderFactory = $queryBuilderFactory;
    }

    /**
     * getQueryBuilderFactory
     * Get query builder factory
     * @return QueryBuilderFactoryInterface
     */
    public function getQueryBuilderFactory(){
        return $this->_queryBuillderFactory;
    }

    /**
     * getQueryBuilder
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder(){
        return $this->getQueryBuilderFactory()->getInstance($this->getConnection());
    }

    /**
     * Set Connection
     * Set Database Connection Object
     * @param  ConnectionInterface $DBConnection
     * @return void
     */
    public function setConnection(DBConnectionInterface $DBConnection){
        $this->_connectionObj = $DBConnection;
    }

    /**
     * Get connection
     * Get Database connection Object
     * @return ConnectionInterface
     */
    public function getConnection(){
        return $this->_connectionObj;
    }

}

?>