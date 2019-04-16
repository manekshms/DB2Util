<?php
namespace DB2Util;
use \DB2Util\DBConnection\ConnectionConfigInterface;

/**
 * DB2UtilconfigInterface
 * Database adapter config interface
 */
interface DB2UtilConfigInterface{

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