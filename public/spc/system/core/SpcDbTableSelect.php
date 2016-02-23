<?php

/**
 * Smart PHP Calendar
 * Database Table Select represents SQL SELECT query statement
 *
 * @category   Spc
 * @package    Core
 * @copyright  Copyright (c) 2008-2011 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */
class SpcDbTableSelect {

    /**
     * Table name
     *
     * @var string
     */
    protected $_table;

    /**
     * Columns
     *
     * @var array
     */
    protected $_cols = array();

    /**
     * From
     *
     * @var string
     */
    protected $_from;

    /**
     * Where
     *
     * @var string
     */
    protected $_where;

    /**
     * Limit
     *
     * @var string
     */
    protected $_limit;

    /**
     * Join
     *
     * @var string
     */
    protected $_join;

    /**
     * Order By
     *
     * @var array
     */
    protected $_order = array();

    /**
     * Group By
     *
     * @var string
     */
    protected $_groupBy;

    /**
     * Constructor
     * Inits table and columns
     *
     * @param void
     */
    public function __construct($table, $cols = null) {
        $this->_table = $table;

        if ($cols === null) {
            $this->_cols[] = "$table.*";
        } else if ($cols !== false) {
            if (!is_array($cols)) {
                $cols = (array)$cols;
            }

            foreach ($cols as $col) {
                $this->_cols[] = "$table.$col";
            }
        }
    }

    /**
     * Inits from clause
     *
     * @param string        $table
     * @return object       $this
     */
    public function from($table, $cols = null) {
        $this->_table = $table;

        //reinit cols
        $this->_cols = array();
        if ($cols == null) {
            $this->_cols[] = "$table.*";
        } else {
            $cols = (array)$cols;
            foreach ($cols as $col) {
                $this->_cols[] = "$table.$col";
            }
        }

        return $this;
    }

    /**
     * Sets (appends) where clause
     *
     * ! Where clause must be given exactly
     * @example $select->where("username = 'foo'");
     *
     * @param string        $where
     * @return object       $this
     */
    public function where($where) {
        if ($this->_where) {
            $this->_where .= " AND ($where)";
        } else {
            $this->_where .= "($where)";
        }

        return $this;
    }

    /**
     * Sets (appends) where clause with OR operator
     *
     * ! Where clause must be given exactly
     * @example $select->orWhere("username = 'foo'");
     *
     * @param string        $where
     * @return object       $this
     */
    public function orWhere($where) {
        $this->_where .= " OR ($where)";
        return $this;
    }

    /**
     * Sets (appends) order clause with default ASC order
     *
     * @param string        $order
     * @return object       $this
     */
    public function order($order) {
        if (!preg_match('/ASC|DESC/i', $order)) {
            $order = "$order ASC";
        }

        $this->_order[] = $order;
        return $this;
    }

    /**
     * Sets limit clause
     *
     * @param string        $limit
     * @return object       $this
     */
    public function limit($limit) {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Sets (appends) join clause
     *
     * @param string        $table table to inner join
     * @param string        $cond join condition
     * @param array         $cols cols will be retrieved from joined table
     * @return object       $this
     */
    public function join($table, $cond, $cols = null) {
        $this->_join[] = "INNER JOIN $table ON $cond";

        if ($cols === false) {
            return $this;
        } else if ($cols === null || $cols === "*") {
            $cols = "$table.*";
        } else if (is_array($cols)) {
            foreach ($cols as $col) {
                $this->_cols[] = "$table.$col";
            }
        }

        return $this;
    }

    /**
     * Sets (appends) join clause
     *
     * @param string        $table table to left join
     * @param string        $cond join condition
     * @param array         $cols cols will be retrieved from joined table
     * @return object       $this
     */
    public function leftJoin($table, $cond, $cols) {
        $this->_join[] = "LEFT JOIN $table ON $cond";
        foreach ($cols as $col) {
            $this->_cols[] = "$table.$col";
        }
        return $this;
    }


    /**
     * Sets (appends) join clause
     *
     * @param string        $table table to right join
     * @param string        $cond join condition
     * @param array         $cols cols will be retrieved from joined table
     * @return object       $this
     */
    public function rightJoin($table, $cond, $cols) {
        $this->_join[] = "RIGHT JOIN $table ON $cond";
        foreach ($cols as $col) {
            $this->_cols[] = "$table.$col";
        }
        return $this;
    }

    /**
     * Sets GROUP BY clause
     *
     * @param string        $groupBy
     * @return object       $this
     */
    public function group($groupBy) {
        $this->_groupBy = $groupBy;
        return $this;
    }

    /**
     * Assembles the SELECT with called SQL Clauses
     *
     * @param void
     * @return string       SQL SELECT Clause
     */
    public function assemble() {
        $sql = "SELECT \n\t"
            . implode(', ', $this->_cols)
            . " FROM $this->_table \n"
            . (count($this->_join) ? implode(" \n", $this->_join) . "\n": '')
            . (($this->_where) ? "WHERE \n\t" . $this->_where : '')
            . ((count($this->_order)) ? " \n ORDER BY " . implode(', ', $this->_order) : '')
            . (($this->_groupBy) ? " \n GROUP BY " . $this->_groupBy : '')
            . (($this->_limit) ? " \n LIMIT " . $this->_limit : '');

        return $sql;
    }

    /**
     * Spc Select Object's String representation
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->assemble();
    }
}