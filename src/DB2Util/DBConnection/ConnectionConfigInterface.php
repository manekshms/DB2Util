<?php
namespace DB2Util\DBConnection;

/**
 * Connection Config
 */
interface ConnectionConfigInterface{

        /**
         * enableExternalConnection
         * Enable Exteranl Connection
         * @param boolean $external_connection_mode
         */
        public function setExternalConnectionMode($external_connection_mode);


        /**
         * getExternalConnection
         * Disable Exteranl Connection
         * @return boolean
         */
        public function getExternalConnectionMode();


}


?>