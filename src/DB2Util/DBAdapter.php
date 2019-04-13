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

    public function select($table, $select_column_names = "*", $where_conditions = "",  $order_by = "", $limit = null, $offset = null){
        $select_fileds = " SELECT ".$select_column_names." FROM  ";
        $sql = "";
        $sql_order_string = "";
        $sql_where_string = "";
        $sql_where_params = [];
        $params = array();

        // where condition build
        if(!empty($where_conditions)){
            $where = array();
            foreach($where_conditions as $key => $values){
                if($values === null){
                    $where[] = $key." IS NULL ";	
                    unset($where_conditions[$key]);
                }else{
                    $where[] = $key." = ? ";	
                }
            }
            $sql_where_string = " WHERE ".implode($where, " AND ");
            $sql_where_params = array_values($where_conditions);	
        }

        // order by  
        if(!empty($order_by)){
            $order_array = array();
            foreach($order_by as $key => $value){
                $order_array[] = $key." ".$value;
            }
            $sql_order_string = " ORDER BY ".implode($order_array , ", ");
        }

        // only  limit	
        if($limit !== null && $offset === null){
            $sql_limit_string = " FETCH FIRST ".$limit." ROWS ONLY ";
            $sql .= $select_fileds.$table.$sql_where_string.$sql_limit_string;
            $params = $sql_where_params;
        }

        // limit and offset
        if($limit !== null && $offset !== null){
            $limit_offset_sql = " ( SELECT ROW_NUMBER() OVER(".$sql_order_string.") as ROW_ID, ".$table.".* FROM ".$table." ".$sql_where_string." ) ";
            $sql .= $select_fileds.$limit_offset_sql." WHERE ROW_ID BETWEEN ".$offset." AND ".($offset + ($limit - 1));
            $params = $sql_where_params;
        }
        // limit and offset is null
        if($limit === null && $offset === null){
            $sql .= $select_fileds.$table.$sql_where_string.$sql_order_string;
            $params = $sql_where_params;
        }
        try{
            $stmt = $this->getConnection()->query($sql, $params);
            $resultSet = $stmt->fetchAll();
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
        return $resultSet;
    }

    /**
     * Insert 
     * Insert new entry in table
     * @param string $table table name
     * @param array $data associative array containing column name and value example ['name' => 'foo', 'country' => 'UK'] 
     * @return boolean true on success
     * @throws DB2UtilException on error
     */
    public function insert($table, array $data){
        $column_name = array_keys($data);
        $column_values = array_values($data);
        $sql = " INSERT INTO ".$table."( ".implode(", ", $column_name)." )";
        $sql .= " VALUES ( ".implode( ", ", array_fill(0, count($column_values), "?"))." ) ";
        try{
            return $this->getConnection()->executeQuery($sql, $column_values);
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
    }

    /**
     * Update
     * Update data in a table
     * @param string $table table name 
     * @param array $data data to update, Associative array with column name and values
     * @param array $where_conditions , Associative array with column and value
     * @return boolean true on success
     * @throws DB2UtilException
     */
    public function update($table_name, array $data, array $where_conditions= null){
        if($where_conditions === null &&
           $this->getAdapterConfig()->getUpdateWhereConditionStrictMode() === true){
            throw new \Exception('update is in strict mode');
        }
        if($where_conditions === null){
            $where_conditions = [];
        }
        $column_names = array_keys($data);
        $column_value = array_values($data);
        $update_sql_string = array_map(function($name){ return $name." = ? "; }, $column_names);
        $sql = " UPDATE ".$table_name." SET ".implode(", ", $update_sql_string);
        $params = $column_value;
        if(count($where_conditions) > 0){
            $where_string = array_map(function($column){ return $column." = ? ";}, array_keys($where_conditions));
            $sql .= " WHERE ".implode(" AND ", $where_string); 
            $params = array_merge($params, array_values($where_conditions));
        }
        try{
            $stmt = $this->getConnection()->executeQuery($sql, $params);
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
        return true;
    }

    /**
     * Delete
     * Delete a row from table
     * @param string $table table name 
     * @param array $where_conditions, Associative array with where conditions
     * @return boolean true on success
     * @throws DB2UtilException
     */
    public function delete($table_name, $where_conditions = null){
        if($where_conditions === null &&
           $this->getAdapterConfig()->getDeleteWhereConditionStrictMode() === true){
            throw new \Exception('delete is in strict mode');
        }
        if($where_conditions === null){
            $where_conditions = [];
        }
        $sql = " DELETE FROM  ".$table_name;
        $params = [];
        if(count($where_conditions) > 0){
            $where_string = array_map(function($column_name){ return $column_name." = ? ";}, array_keys($where_conditions));
            $sql .= " WHERE ".implode(" AND ", $where_string);
            $params = array_values($where_conditions);
        }
        try{
            $stmt = $this->getConnection()->executeQuery($sql, $params);
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
        return true;
    }
    

    /**
     * Count
     * Get Count of records
     * @param string $table_name Table name 
     * @param array $where_conditions Optional associative array example ['name' = 'bob']
     * @throws DB2UtilException
     */
    public function count($table_name, $where_conditions=array()){
        $sql = " SELECT COUNT(*) FROM ".$table_name;
        $params = array();
        if(count($where_conditions) > 0){
            $where_string = array_map(function($column_name){ return $column_name." = ? ";}, array_keys($where_conditions));
            $sql .= " WHERE ".implode(" AND ", $where_string);
            $params = array_merge($params, array_values($where_conditions));
        }
        try{
            return $this->getConnection()->query($sql, $params)->fetchColumn();
        }catch(\Exception $e){
            throw new DB2UtilException($e->getMessage());
        }
    }

}

?>