<?php
namespace DB2Util\DBConnection\Statement;

/**
 * DBStatementException
 * DB Statement Exception
 */
class DBStatementException extends \Exception{
    /**
     * PHP Constructor
     * @param string $msg Message
     */
    public function __construct($msg){
        parent::__construct($msg);
    }

}

?>