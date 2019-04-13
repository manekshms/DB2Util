<?php
    namespace DB2Util\DBConnection\Statement;

    /**
     * StatementInterface
     * Database Statement Interfac3e
     */
    interface DBStatementInterface {

        /**
         * Fetch
         * Fetch a row from the result set
         * @param boolean $mode Optional Default false
         * @return array
         * @throws Exception
         */
        public function fetch($mode);

        /**
         * Fetch all
         * Fetch all rows in the result set
         * @param boolean $mode Optional default false
         * @return array
         * @throws Exception
         */
        public function fetchAll($mode);

        /**
         * Fetch columns
         * Fetch a column in the result set
         * @return mixed
         * @throws Exception
         */
        public function fetchColumn();

    }

?>