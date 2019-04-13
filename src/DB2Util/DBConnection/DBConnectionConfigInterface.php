<?php
    namespace DB2Util\DBConnection;

    interface DBConnectionConfigInterface extends ConnectionConfigInterface{

        /**
         * SetDsn
         * Set database dsn
         * @param string $dsn
         */
        public function setDsn($dsn);

        /**
         * GetDsn
         * Get Dsn string
         * @return string
         */
        public function getDsn();

        /**
         * SetHostName
         * Set host name
         * @param string $hostname Host name
         */
        public function setHostName($hostname);

        /**
         * getHostName
         * @return string Hostname
         */
        public function getHostName();

        /**
         * setPort
         * Set database port
         * @param string $port 
         */
        public function setPortNumber($port);

        /**
         * getPort
         * @return string port number
         */
        public function getPortNumber();

        /**
         * setDatabaseName
         * Set database name
         * @param string $db_name
         */
        public function setDatabaseName();

        /**
         * getDatabaseName
         * Get database name
         * @return string database name
         */
        public function getDatabaseName();

        /**
         * setUsername
         * Set user name
         * @param string $username
         */
        public function setUsername($username);

        /**
         * getUsername
         * Get username
         * @return string username
         */
        public function getUsername();

        /**
         * setPassword
         * Set password
         * @param string $password
         */
        public function setPassword($password);

        /**
         * getPassword
         * Get database password
         * @return string password
         */
        public function getPassword();

    }
?>