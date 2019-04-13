<?php
namespace DB2Util\DBConnection;
use DB2Util\DBConnection\Statement\DBStatementFactory;

class DBConnectionFactory implements DBConnectionFactoryInterface{
    /**
     * getInstance
     * Get connection instance
     * @param ConnectionConfigInterface $dbConnectionConfig
     */
    public function getInstance(ConnectionConfigInterface $dbConnectionConfig){
        $dbConnObj = new DBConnection($dbConnectionConfig);
        $stmtFactory = new DBStatementFactory();
        $dbConnObj->setStatementFactory($stmtFactory);
        return $dbConnObj;
    }
}
?>