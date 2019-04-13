<?php
namespace DB2Util\DBConnection;
    /**
     * DBConnectionException
     * DBConnection Exception class
     */
    class DBConnectionException extends \Exception{

        public function __construct($msg){
            parent::__construct($msg);
        }

    }
?>