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
abstract class SpcDbTable extends SpcDb {
    protected $_userId;

    public function __construct($dbParams = null) {
        parent::__construct($dbParams);
        
        $this->_userId = defined('SPC_USERID') ? SPC_USERID : null;
    }
}