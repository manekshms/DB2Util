<?php
namespace DB2Util\DBConnection;

use DB2Util\DBConnection\Statement\DBStatementFactoryInterface;
use DB2Util\DBConnectio\Statement\DBStatement;
use DB2Util\DBConnectio\Statement\DBStatementFactory;

class DBConnection implements DBConnectionInterface
{

    private $_connectionObj;
    private $_connectionConfig;
    private $_statementFactory;
    private $_last_query;
    private $_last_query_params;

    /**
     * Constructor
     * @param Connectionconfig Optional Connection Configuration
     */
    public function __construct($connectionConfig = null)
    {
        if ($connectionConfig !== null) {
            $this->setConnectionConfig($connectionConfig);
        }
    }

    /**
     * setStatementFactory
     * Set StatementFactory Object
     * @param DBStatementFactory
     */
    public function setStatementFactory(DBStatementFactoryInterface $statementFactory)
    {
        $this->_statementFactory = $statementFactory;
    }

    /**
     * getStatementFactory
     * Get statement Factory
     * @return DBStatementFactory
     */
    public function getStatementFactory()
    {
        return $this->_statementFactory;
    }

    /**
     * getStatementInstance
     * Get Satement Insatance
     * @return DBStatement
     */
    public function getStatementInstance($stmt)
    {
        return $this->getStatementFactory()->getInstance($stmt);
    }

    /**
     * Connect
     * Connect to a database
     * @return void
     * @throws DBConnectionException
     */
    public function connect()
    {
        // If external connection is enabled no need to create  connection
        if ($this->getConnectionConfig()->getExternalConnectionMode() == true) {
            $this->_connectionObj = $this->getConnectionconfig()->getExternalConnection();
            $this->_connectionObj->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this;
        }
        $dsn = $this->getConnectionconfig()->getDsn();
        $username = $this->getConnectionconfig()->getUsername();
        $password = $this->getConnectionconfig()->getPassword();
        try {
            $conn = new \PDO($dsn, $username, $password);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->_connectionObj = $conn;
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

    /**
     * Disconnect
     * Disconnect database
     * @return void
     */
    public function disconnect()
    {
        $this->_connectionObj = null;
    }

    /**
     * getConnectionObj
     * Get Connection Obj
     * @return object;
     */
    protected function getConnectionObj()
    {
        return $this->_connectionObj;
    }

    /**
     * Query
     * Run a SQL query return Statement
     * Run Select query
     * @param string $sql Sql query
     * @param array $params Bind parameter array
     * @return Statement
     * @throws DBConnectionException
     */
    public function query($sql, $params = array())
    {
        if ($sql == "") {
            throw new DBConnectionException("Sql parameter cannot be empty");
        }
        $connObj = $this->getConnectionObj();
        // Prepare sql query
        try {
            $this->_setLastSQlQuery($sql);
            $this->_setLastSQLQueryParams($params);
            $stmt = $connObj->prepare($sql);
            $dbStmt = $this->getStatementInstance($stmt);
            $dbStmt->bindParams($params);
            $dbStmt->execute();
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
        return $dbStmt;
    }

    /**
     * Execute Query
     * Execute query insert and update
     * @param string $sql Sql query
     * @param array $params Bind parameter array
     * @return Statement
     * @throws DBConnectionException
     */
    public function executeQuery($sql, $params = array())
    {
        if ($sql == "") {
            throw new DBConnectionException("Sql parameter cannot be empty");
        }
        $connObj = $this->getConnectionObj();
        // Prepare sql query
        try {
            $this->_setLastSQlQuery($sql);
            $this->_setLastSQLQueryParams($params);
            $stmt = $connObj->prepare($sql);
            $dbStmt = $this->getStatementInstance($stmt);
            $dbStmt->bindParams($params);
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
        $this->_setLastSQlQuery($sql);
        $this->_setLastSQLQueryParams($params);
        return $dbStmt->execute();
    }

    /**
     * Get Last Insert Id
     * @return string String representation of Row Id
     * @throws DBConnectionException
     */
    public function getLastInsertId()
    {
        try {
            return $this->query("SELECT IDENTITY_VAL_LOCAL() AS VAL FROM SYSIBM.SYSDUMMY1")->fetchAll()[0]['VAL'];
            //return $this->getConnectionObj()->lastInsertId();
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

    /**
     * Begin Transaction
     * @return boolean true on success false on failure
     * @throws DBConnectionException
     */
    public function beginTransaction()
    {
        try {
            return $this->gettConnectionObj()->beginTransaction();
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

    /**
     * commit Transaction
     * @return boolean true on success false on failure
     * @throws DBConnectionException
     */
    public function commitTransaction()
    {
        try {
            return $this->gettConnectionObj()->commit();
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

    /**
     * Rollback Transaction
     * @return boolean true on success false on failure
     * @throws DBConnectionException
     */
    public function rollBackTransaction()
    {
        try {
            return $this->gettConnectionObj()->rollBack();
        } catch (\Exception $e) {
            throw new DBConnectionException($e->getMessage());
        }
    }

    /**
     * setConnectionConfig
     * Set Connection Config
     * @param ConnectionConfigInterface
     */
    public function setConnectionConfig(ConnectionConfigInterface $config)
    {
        $this->_connectionConfig = $config;
    }

    /**
     * getConnectionConfig
     * Get database connecion configuration
     * @return string
     */
    public function getConnectionconfig()
    {
        return $this->_connectionConfig;
    }

    /**
     * getLastQuery
     * @param boolean $debug default false
     * if set true return associative array with last sql query and parameter and processed query
     * Example : ['query' => 'select * from user where user_name => "', params => array('admin'), 'processed_query' =>' select * from user where user_name = "admin"' ]
     * @return string|array
     */
    public function getLastSQLQuery($debug = false)
    {

        if (!$debug) {
            return $this->_getLastSQlQuery();
        }

        $output = array();
        $output['query'] = $this->_getLastSQlQuery();
        $output['params'] = $this->_getLastSQLQueryParams();
        $output['processed_query'] = $this->buildSqlStringFromQueryAndParams($this->_getLastSQlQuery(), $this->_getLastSQLQueryParams());

        return $output;

    }

    /**
     * BuildSqlStringFromQueryAndParams
     * @param string sql string
     * @param array $params parameters
     * @return string Sql string
     */
    private function buildSqlStringFromQueryAndParams($sql, $params = array())
    {
        $sql = $sql;
        if (count($params) > 0) {
            $sql = preg_replace_callback("/\?/", function ($matches) use (&$params) {
                $d = array_shift($params);
                $out = null;
                switch ($d) {
                    case is_null($d):
                        $out = " null ";
                        break;
                    default:
                        $out = "'" . $d . "'";
                }
                return $out;
            }, $sql);
        }
        return $sql;
    }

    /**
     * _setLastSQLQuery
     * set last query
     * @param string $last_query Last SQL query
     */
    private function _setLastSQlQuery($last_query)
    {
        $this->_last_query = $last_query;
    }

    /**
     * _getLastSQLQuery
     * set last query
     */
    private function _getLastSQlQuery()
    {
        return $this->_last_query;
    }

    /**
     * _setLastSQLQueryParams
     * @param array $params query parameters
     */
    private function _setLastSQLQueryParams($params)
    {
        $this->_last_query_params = $params;
    }

    /**
     * _getLastSQLQueryParams
     * Get last query parameter array
     * @return array
     */
    private function _getLastSQLQueryParams()
    {
        return $this->_last_query_params;
    }

}
