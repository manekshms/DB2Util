<?php
namespace DB2Util;

use \DB2Util\DBConnection\ConnectionConfigInterface;

class DB2UtilConfig implements DB2UtilConfigInterface{

    private $_delete_where_strict_mode = false;
    private $_update_where_strict_mode = false;
    private $_connectionConfig = null;
    /**
     * setDeleteWhereConditionStrictMode
     * Set Delete Where Condition Strict Mode
     * @param boolean $mode
     */
    public function setDeleteWhereConditionStrictMode($mode){
        $this->_delete_where_strict_mode = $mode;
    }

    /**
     * getDeleteWhereconditionStrictMode
     * Get Delete Where condition strict mode
     * @return boolean
     */
    public function getDeleteWhereConditionStrictMode(){
        return $this->_delete_where_strict_mode;
    }

    /**
     * setUpdateWhereConditionStrictMode
     * Set Update query where condition strict mode
     * @param boolean true if strict false if non strict
     */
    public function setUpdateWhereConditionStrictMode($mode){
        $this->_update_where_strict_mode = $mode;
    }


    /**
     * getUpdateWhereConditionStrictMode
     * Get Update Where Condition Stict Mode
     * @return boolean
     */
    public function getUpdateWhereConditionStrictMode(){
        return $this->_update_where_strict_mode;
    }

    /**
     * setConnectionConfig
     * @param ConnectionConfigInterface $dbConnConfig  database connection config
     */
     public function setConnectionConfig(ConnectionConfigInterface $dbConnConfig){
        $this->_connectionConfig = $dbConnConfig;
     }


    /**
     * getConnectionConfig
     * @return  DBConnectionConfigInterface
     */
    public function getConnectionConfig(){
        return $this->_connectionConfig;
    }


}

?>