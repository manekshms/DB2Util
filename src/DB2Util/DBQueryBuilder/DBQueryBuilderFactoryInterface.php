<?php
namespace DB2Util\DBQueryBuilder;
use DB2Util\DBConnection\DBConnectionInterface;

/**
 * DBQueryBuilderInteface
 */
interface DBQueryBuilderFactoryInterface {
    
    /**
     * getInstance
     * @param DBConnectionInterface $dbConnection
     * @return QueryBuilder
     */
    public function getInstance(DBConnectionInterface $dbConnection);

}
?>