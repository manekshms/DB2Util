<?php
namespace DB2Util\DBConnection\Statement;

class DBStatement implements DBStatementInterface{

        private $_stmt;

        /**
         * Constructor
         */
        public function __construct(\PDOStatement $stmt){
            $this->setStatement($stmt);
        } 

        /**
         * setStatement
         * Set Statement Obj
         * @param PDOStatement
         */
        protected function setStatement($stmt){
            $this->_stmt = $stmt;
        }

        /**
         * getStatement
         * @return PDOStatement
         */
        protected function getStatement(){
            return $this->_stmt;
        }

        /**
         * Fetch
         * Fetch a row from the result set
         * @param boolean $mode Optional Default false
         * @return array
         * @throws DBStatementException
         */
        public function fetch($mode = false){
            // If mode == false then fetch associative array if true then fetch both associative array
            // and index array
            $mode = ($mode === false)? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH;  
            $stmt = $this->getStatement();
            try{
                return $stmt->fetch($mode);
            }catch(\Exception $e){
                throw new DBStatementException($e->getMessage());
            }
        }

        /** Fetch all
         * Fetch all rows in the result set
         * @param boolean $mode Optional default false
         * @return array
         * @throws DBStatementException
         */
        public function fetchAll($mode = false){
            // If mode == false then fetch associative array if true then fetch both associative array
            // and index array
            $mode = ($mode === false)? \PDO::FETCH_ASSOC : \PDO::FETCH_BOTH;
            $stmt = $this->getStatement();
            try{
                return $stmt->fetchAll($mode);
            }catch(\Exception $e){
                throw new DBStatementException($e->getMessage());
            }
        }

        /**
         * Fetch columns
         * Fetch a column in the result set
         * @return mixed
         * @throws DBStatementException
         */
        public function fetchColumn(){
            $stmt = $this->getStatement();
            try{
                return $stmt->fetchColumn();
            }catch(\Exception $e){
                throw new DBStatementException($e->getMessage());
            }
        }

        /**
         * bindParams
         * Bind Params
         * @param array $params parameter to bind
         * @throws DBStatementException
         */
        public function bindParams($params){
            if(!empty($params) && is_array($params)){
                $stmt = $this->getStatement();
                $index = 1;
                foreach($params as $param){
                    try{
                        $this->bind($stmt, $index++, $param);
                    }catch(\Exception $e){
                        throw new DBStatementException($e->getMessage());
                    }
                }
            }
        }



		/**
		 * bind value to a prepare statement
		* @param PDOStatement $stmt, $param mixed, $value mixed, $data_type [pdo constant]
        * @return boolean true on success and false on failure
        * @throws Exception
		*/
		private function bind($stmt, $params, $value, $data_type = null){
			$data_type = (!is_null($data_type))? $data_type : $this->getPDODateType($value);
			$status = $stmt->bindValue($params, $value, $data_type);
		}

		/**
		 * get pdo date type
		* @param $value Mixed
		* @return  PDO database constant
		*/
		private function getPDODateType($value){
            switch (true) {
                case is_bool($value):
                $var_type = \PDO::PARAM_BOOL;
                break;
            case is_int($value):
                $var_type = \PDO::PARAM_INT;
                break;
            default:
                $var_type = \PDO::PARAM_STR;
            }
            return $var_type;
		}

        /**
         * execute
         * Execute a statement
         * @throw DBStatementException
         */
        public function execute(){
            try{
                return $this->getStatement()->execute();
            }catch(\Exception $e){
                throw new DBStatementException($e->getMessage());
            }
        }
}

?>