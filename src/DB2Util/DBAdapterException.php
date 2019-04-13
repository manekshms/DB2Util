<?php
namespace DB2Util;

/**
 * DB2UtilException
 */
class DB2UtilException extends \Exception{

    /**
     * Php consructor 
     */
    public function __construct($msg){
        parent::__construct($msg);
    }

}
?>