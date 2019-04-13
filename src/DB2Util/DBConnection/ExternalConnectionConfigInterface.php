<?php
namespace DB2Util\DBConnection;

interface ExternalConnectionConfigInterface extends ConnectionConfigInterface{
    /**
     * setExternalConnection
     * @param mixed $externalConnection
     */
    public function setExternalConnection($externalConnection);

    /**
     * getExternalConnection
     * @return mixed external connection resource
     */
    public function getExternalConnection();



}

?>