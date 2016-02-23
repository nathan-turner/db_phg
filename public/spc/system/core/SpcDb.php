<?php

/**
 * Smart PHP Calendar
 *
 * @category   Spc
 * @package    Core
 * @copyright  Copyright (c) 2008-2011 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */

/**
 * SpcDbTable is an object that acts as a Gateway to a database table.
 * One instance handles all the rows in the table.
 *
 * A Table Data Gateway holds all the SQL for accessing a single table or view:
 * selects, inserts, updates, and deletes. Other code calls its methods for all
 * interaction with the database.
 */
class SpcDb {

    /**
     * Mysql connection
     *
     * @var resource
     */
    protected static $_conn;

    /**
     * MySQL tables
     *
     * @var array
     */
    protected static $_tables;

    /**
     * Default Table name
     *
     * @var string
     */
    protected $_name;

    /**
     * Holds the last MySQL error message
     *
     * @var string
     */
    protected $_error;

    /**
     * Table prefix
     *
     * @var string
     */
    protected $_prefix = 'spc_';

    /**
     * Default row fetch mode
     *
     * @var string
     */
    protected $_fetchMode = 'assoc';

    protected static $_postSanitized = false;

    /**
     * Sets the default or custom connection
     *
     * @param resource      $dbAdapter mysql connection
     */
    public function __construct($dbParams = null) {
        if ($dbParams !== null) {
            self::$_conn = self::getDbConn($dbParams);
        } else if (!self::$_conn) {
            self::$_conn = self::getDbConn();
        }

        if (self::$_postSanitized === false) {
            $_POST = $this->sanitizeInput($_POST);
            self::$_postSanitized = true;
        }
    }

    /**
     * Gets default mysql connection
     *
     * @param void
     *
     * @return resource
     */
    public static function getDbConn($dbParams = null) {
        if ($dbParams !== null) {
            $host = $cfg['host'];
            $username = $cfg['username'];
            $password = $cfg['password'];
            $dbName = $cfg['dbName'];
        } else {
            if (self::$_conn) {
                return self::$_conn;
            }

            $host = SPC_DB_HOST;
            $username = SPC_DB_USERNAME;
            $password = SPC_DB_PASSWORD;
            $dbName = SPC_DB_DBNAME;
        }

        self::$_conn = mysql_connect($host, $username, $password);
        mysql_select_db($dbName);

        return self::$_conn;
    }

    /**
     * Sanitizes $var
     *
     * @param mixed $var
     * @return mixed
     */
    public function sanitizeInput(&$var) {
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $var[$key] = self::sanitizeInput($val);
            }

            return $var;
        } else {
            if (get_magic_quotes_gpc() == 1) {
                $var = mysql_real_escape_string(stripslashes($var));
            } else {
                $var = mysql_real_escape_string($var);
            }

            return $var;
        }
    }

    public function setTableName($table) {
        $this->_name = $table;
    }

    /**
     * Gets table name
     *
     * @param void
     * @return string
     */
    public function getTableName() {
        if ($this->_prefix) {
            return $this->_prefix . $this->_name;
        }

        return $this->_name;
    }

    public function getErrorMsg() {
        return $this->_error;
    }

    /**
     * Begin MySQL Transaction
     */
    public function begin() {
        mysql_query('BEGIN');
    }

    /**
     * Rollback MySQL Transaction
     */
    public function rollback() {
        mysql_query('ROLLBACK');
    }

    /**
     * Commit MySQL Transaction
     */
    public function commit() {
        mysql_query('COMMIT');
    }

    /**
     * Checks if there is a database table $table
     *
     * @param string $table
     * @return boolean
     */
    public function checkTable($table) {
        if (!self::$_tables) {
            $tables = $this->fetchAll('SHOW TABLES', 'row');
            foreach ($tables as $tableRow) {
                self::$_tables[] = $tableRow[0];
            }
        }

        if (in_array($table, self::$_tables) === false) {
            return false;
        }

        return true;
    }

    /**
     * Returns new SpcDbTableSelect object with default table and SQL * Wildcard
     *
     * @param array         $cols column names
     * @param string        $table table name
     * @return object       SpcDbTableSelect object
     */
    public function select($cols = null, $table = null) {
        if ($table == null) {
            $table = $this->getTableName();
        }

        return new SpcDbTableSelect($table, $cols);
    }

    /**
     * Gets sql query type ('insert', 'update', 'delete', 'select')
     *
     * @param string        $sql query
     * @return string       query type
     */
    public function getQueryType($sql) {
        $queryTypes = array('insert', 'update', 'delete', 'select');

        foreach ($queryTypes as $queryType) {
            if (preg_match("/^\s*$queryType/i", $sql)) {
                return $queryType;
            }
        }

        $sql = explode(' ', trim($sql));
        return $sql[0];
    }

    /**
     * Quotes string values in WHERE clause
     * @example WHERE foo = 'bar'
     *
     * @param mixed $v
     * @return mixed
     */
    public function quoteStrVals($v) {
        if (is_array($v)) {
            foreach ($v as $key => $val) {
                $v[$key] = $this->quoteStrVals($val);
            }
        } else {
            if (is_string($v)) {
                return "'$v'";
            }
            return $v;
        }

        return $v;
    }

    /**
     * Executes an Sql statement and returns a value by the query's type
     * (insert, update, delete, select)
     *
     * @param mixed        $sql query or SpcDbTableSelect object
     * @return mixed
     */
    public function query($sql) {
        $sql = (string)$sql;

        $rs = mysql_query($sql, self::$_conn);

        if (!$rs) {
            $errMsg = "<SqlError> <br />
                        Sql: $sql <br />
                        Error: " . mysql_error();

            $this->_error = $errMsg;

            throw new Exception($errMsg);
        }

        $queryType = $this->getQueryType($sql);

        switch ($queryType) {
            case 'insert':
                return mysql_insert_id(self::$_conn);

            case 'update':
            case 'delete':
                return mysql_affected_rows(self::$_conn);

            case 'select':
                return $rs;

            default:
                return $rs;
        }
    }

    /**
     * Gets number of rows in a resource
     *
     * @param mixed             $data (SpcDbTableSelect or PHP resource
     * @return int
     */
    public function numRows($select) {
        if ($select instanceof SpcDbTableSelect) {
            return mysql_num_rows($this->query($select));
        } else if (is_resource($select)) {
            return mysql_num_rows($select);
        }
    }

    public function getColumns($table = null, $getFullInfo = false) {
        if (!$table) {
            $table = $this->_name;
        }

        $sql = "SHOW COLUMNS FROM {$table}";
        $rows = $this->fetchAll($sql);

        $columns = array();
        foreach ($rows as $row) {
            if ($getFullInfo) {
                $columns[] = $row;
            } else {
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }

    /**
     * Inserts a new row (or rows)
     *
     * @param array         $data column => value pairs or array of rows (column => value)
     * @return int          the number of affected rows
     */
    public function insert($data, $table = null, $replace = false) {
        #array_walk($data, array($this, 'sanitizeInput'));

        //INSERT INTO or REPLACE INTO
        $insertStatement = $replace ? 'REPLACE' : 'INSERT';

        if ($table == null) {
            $table = $this->getTableName();
        }

        $firstItemOfData = current($data);
        //insert one row
        if (!is_array($firstItemOfData)) {
            $cols = array_keys($data);
            $vals = $this->quoteStrVals(array_values($data));
            $sql = "$insertStatement INTO
                        $table "
                    . '(' . implode(', ', $cols) . ')
                    VALUES
                        (' . implode(', ', $vals) . ')';
        //insert multiple rows
        } else {
            $cols = array_keys($firstItemOfData);
            $sql = "$insertStatement INTO
                        $table "
                    . '(' . implode(', ', $cols) . ')
                    VALUES ';

            $valSet = array();
            foreach ($data as $row) {
                $valSet[] = '(' . implode(', ', $this->quoteStrVals(array_values($row))) . ')';
            }

            $sql .= join(', ', $valSet);
        }

        return $this->query($sql);
    }

    /**
     * Updates table rows with specified data based on $where variable
     *
     * @param array         $data column => value pairs
     * @param mixed         $where where clause
     * @param mixed         $table db table to update
     * @return int          the number of affected rows
     */
    public function update($data, $where = null, $table = null) {
        #array_walk($data, array($this, 'sanitizeInput'));

        if ($table === null) {
            $table = $this->getTableName();
        }

        $set = array();
        foreach ($data as $col => $val) {
            if (is_string($val)) {
                $set[] = $col . " = '$val'";
            } else {
                $set[] = $col . ' = ' . $val;
            }
        }

        $sql = "UPDATE
                    $table
                SET "
                . implode(', ', $set)
                . (($where) ? " WHERE $where" : '');

        return $this->query($sql);
    }

    /**
     * Deletes tables rows with specified data based on $where variable
     *
     * @param string        $table (db table)
     * @param string        $where (where clause)
     * @return int          the number of affected rows
     */
    public function delete($where = null, $table = null) {
        if ($table === null) {
            $table = $this->getTableName();
        }

        $sql = "DELETE FROM
                    $table"
                . (($where) ? " WHERE $where" : '');

        return $this->query($sql);
    }

    /**
     * Fetchs a single row
     *
     * @param mixed         $data sql or array
     * @param string        $fetchMode
     * @return mixed        array or object
     */
    public function fetchRow($select, $fetchMode = null) {
        $rows = $this->fetchAll($select, $fetchMode);
        if (isset($rows[0])) {
            return $rows[0];
        }

        return false;
    }

    /**
     * Fetchs a cloumn value
     *
     * @param mixed $select SpcDbTableSelect or resource
     * @param int $colIndex
     * @return mixed
     */
    public function fetchColumn($select, $colIndex = 0) {
        $row = $this->fetchRow($select, 'row');
        if (!$row) {
            return false;
        }
        
        return $row[$colIndex];
    }

    /**
     * Fetchs rows
     *
     * @param mixed         $data sql or array
     * @param string        $fetchMode
     * @return array        array of rows
     */
    public function fetchAll($select, $fetchMode = null) {
        //fix
        //PHP 5.1 causes problem with (string) casting op.
        //get direct sql with SpcDbTableSelect object's assemble method
        if (is_object($select)) {
            $select = $select->assemble();
        }

        if ($fetchMode == null) {
            $fetchMode = $this->_fetchMode;
        }

        $rs = $this->query($select);

        $rows = array();
        switch ($fetchMode) {
            case 'assoc':
                while ($row = mysql_fetch_assoc($rs)) {
                    $rows[] = $row;
                }
            break;

            case 'row':
                while ($row = mysql_fetch_row($rs)) {
                    $rows[] = $row;
                }
            break;

            case 'object':
                while ($row = mysql_fetch_object($rs)) {
                    $rows[] = $row;
                }
            break;
        }

        return $rows;
    }

    /**
     * Retrieves row(s) by their primary keys
     *
     * @param mixed         $id row primary key value
     * @param mixed         $pk row primary key column(s)
     * @param array         $cols columns to be retrieved
     * @param string        $table table name row(s) to be retrieved
     * @param string        $fetchMode row fetch mode
     * @return mixed        row(s)
     */
    public function find($id, $pk = null, $cols = null, $table = null, $fetchMode = null) {
        if ($pk === null) {
            $pk = 'id';
        }

        if ($table === null) {
            $table = $this->getTableName();
        }

        if ($fetchMode === null) {
            $fetchMode = $this->_fetchMode;
        }

        if ($cols !== null) {
            $cols = implode(', ', $cols);
        } else {
            $cols = ' * ';
        }

        $rows = array();

        //composite pk
        if (is_array($pk)) {
            $compositePkColCount = count($pk);
            //multiple rows
            if (is_array($id[0])) {
                foreach ($id as $rowPkArr) {
                    $pkSet = array();
                    for ($j = 0; $j < $compositePkColCount; $j++) {
                        if (gettype($rowPkArr[$j]) === 'string') {
                            $pkSet[] = $pk[$j] . ' = "' . $rowPkArr[$j] . '"';
                        } else {
                            $pkSet[] = $pk[$j] . ' = ' . $rowPkArr[$j];
                        }
                    }

                    $pk = implode(' AND ', $pkSet);

                    $sql = "SELECT
                                $cols
                            FROM
                                $table
                            WHERE
                                $pk";

                    $rows[] = $this->fetchRow($sql, $fetchMode);
                }

                return $rows;

            //single row
            } else {
                $pkSet = array();
                for ($i = 0, $c = count($pk); $i < $c; $i++) {
                    if (gettype($id[$i]) === 'string') {
                        $pkSet[] = $pk[$i] . ' = "' . $id[$i] . '"';
                    } else {
                        $pkSet[] = $pk[$i] . ' = ' . $id[$i];
                    }
                }

                $pk = implode(' AND ', $pkSet);

                $sql = "SELECT
                            $cols
                        FROM
                            $table
                        WHERE
                            $pk";

                return $this->fetchRow($sql, $fetchMode);
            }
        }

        //non-composite pk
        //multiple rows
        if (is_array($id)) {
            $rows = array();
            foreach ($id as $rowPk) {
                $pkClause = $pk . ' = ' . $rowPk;
                if (gettype($rowPk) === 'string') {
                    $pkClause = $pk . ' = "' . $rowPk . '"';
                }

                $sql = "SELECT
                            $cols
                        FROM
                            $table
                        WHERE
                            $pkClause";

                $rows[] = $this->fetchRow($sql, $fetchMode);
            }

            return $rows;

        //single row
        } else {
            $rowPk = $pk . ' = ' . $id;
            if (gettype($rowPk) === 'string') {
                $rowPk = $pk . ' = "' . $id . '"';
            }

            $sql = "SELECT
                        $cols
                    FROM
                        $table
                    WHERE
                        $pk = $id";

            return $this->fetchRow($sql, $fetchMode);
        }
    }
}