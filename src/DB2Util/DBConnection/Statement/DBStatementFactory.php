<?php
namespace DB2Util\DBConnection\Statement;

    /**
     * DBStatementFactoryInterface
     * DBStatement Factory for generating statement
     */
    class DBStatementFactory implements DBStatementFactoryInterface{

        /**
         * getInstance
         * Get Instance of Statement Object
         * @param PDOStatement
         * @return DBStatement
         */
        public function getInstance($stmt){
            return new DBStatement($stmt);
        }


    }
?>