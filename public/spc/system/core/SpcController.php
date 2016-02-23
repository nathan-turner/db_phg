<?php

abstract class SpcController {

    /**
     * Spc Database
     *
     * @var object
     */
    protected $_db;

    /**
     * System UserID
     *
     * @var int
     */
    protected $_userId;

    public function __construct() {
        $this->_db = new SpcDb;

        $this->_userId = defined('SPC_USERID') ? SPC_USERID : null;
    }
}