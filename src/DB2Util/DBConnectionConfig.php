<?php
namespace DB2Util;

use \DB2Util\DBConnection\DBConnectionConfigInterface;

class DBConnectionConfig implements DBConnectionConfigInterface{

    private $_dsn;
    private $_username; 
    private $_password;
    private $_external_connection_mode = false;

    /**
     * SetDsn
     * Set database dsn
     * @param string $dsn
     */
    public function setDsn($dsn){
        $this->_dsn = $dsn;
    }

    /**
     * GetDsn
     * Get Dsn string
     * @return string
     */
    public function getDsn(){
        return $this->_dsn;
    }

    /**
     * SetHostName
     * Set host name
     * @param string $hostname Host name
     */
    public function setHostName($hostname){

    }

    /**
     * getHostName
     * @return string Hostname
     */
    public function getHostName(){

    }

    /**
     * setPort
     * Set database port
     * @param string $port 
     */
    public function setPortNumber($port){

    }

    /**
     * getPort
     * @return string port number
     */
    public function getPortNumber(){

    }

    /**
     * setDatabaseName
     * Set database name
     * @param string $db_name
     */
    public function setDatabaseName(){

    }

    /**
     * getDatabaseName
     * Get database name
     * @return string database name
     */
    public function getDatabaseName(){

    }

    /**
     * setUsername
     * Set user name
     * @param string $username
     */
    public function setUsername($username){
        $this->_username = $username;
    }

    /**
     * getUsername
     * Get username
     * @return string username
     */
    public function getUsername(){
        return $this->_username;
    }

    /**
     * setPassword
     * Set password
     * @param string $password
     */
    public function setPassword($password){
        $this->_password = $password;
    }

    /**
     * getPassword
     * Get database password
     * @return string password
     */
    public function getPassword(){
        return $this->_password;
    }


    /**
     * enableExternalConnection
     * Enable Exteranl Connection
     */
    public function setExternalConnectionMode($external_connection_mode){
        $this->_external_connection_mode = $external_connection_mode;
    }


    /**
     * getExternalConnection
     * Disable Exteranl Connection
     */
    public function getExternalConnectionMode(){
        return  $this->_external_connection_mode;
    }

}

?>