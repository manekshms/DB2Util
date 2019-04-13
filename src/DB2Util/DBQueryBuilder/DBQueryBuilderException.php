<?php
namespace DB2Util\DBQueryBuilder;
    /**
     * DBQueryBuilderException
     * Query builder exception
     */
    class DBQueryBuilderException extends \Exception{

        /**
         * Constructor
         * @param string $msg message
         */
        public function __construct($msg){
            parent::__construct($msg);
        }
    }
?>