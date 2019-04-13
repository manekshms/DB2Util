<?php
namespace DB2Util;
use \DB2Util\DBConnection\ConnectionConfigInterface;

/**
 * DB2UtilconfigInterface
 * Database adapter config interface
 */
interface DB2UtilConfigInterface{
    /**
     * setDeleteWhereConditionStrictMode
     * Set Delete Where Condition Strict Mode
     * @param boolean $mode
     */
    public function setDeleteWhereConditionStrictMode($mode);

    /**
     * getDeleteWhereconditionStrictMode
     * Get Delete Where condition strict mode
     * @return boolean
     */
    public function getDeleteWhereConditionStrictMode();

    /**
     * setUpdateWhereConditionStrictMode
     * Set Update query where condition strict mode
     * @param boolean true if strict false if non strict
     */
    public function setUpdateWhereConditionStrictMode($mode);


    /**
     * getUpdateWhereConditionStrictMode
     * Get Update Where Condition Stict Mode
     * @return boolean
     */
    public function getUpdateWhereConditionStrictMode();

    /**
     * setConnectionConfig
     * @param DBConnectionConfigInterface $dbConnConfig  database connection config
     */
    public function setConnectionConfig(ConnectionConfigInterface $dbConnConfig);

    /**
     * getConnectionConfig
     * @return  DBConnectionConfigInterface
     */
    public function getConnectionConfig();


}
?>