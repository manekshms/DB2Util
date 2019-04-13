<?php
namespace DB2Util\DBConnection;

/**
 * DBConnectionFactoryInterface
 * Generate DBConnection Instance
 */
interface DBConnectionFactoryInterface{

    /**
     * getInstance
     * @return DBConnectino
     */
    public function getInstance(ConnectionConfigInterface $dbConnectionConfig);
}
?>