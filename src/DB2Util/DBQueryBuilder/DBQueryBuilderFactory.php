<?php
namespace DB2Util\DBQueryBuilder;
use DB2Util\DBConnection\DBConnectionInterface;
/**
 * DBQueryBuilderFactory
 * 
 */
class DBQueryBuilderFactory implements DBQueryBuilderFactoryInterface{

    /**
     * getInstance
     * @param DBConnectionInterface $dbConnection
     * @return QueryBuilder
     */
    public function getInstance(DBConnectionInterface $dbConnection){
        return new DBQueryBuilder($dbConnection);        
    }

} 
?>