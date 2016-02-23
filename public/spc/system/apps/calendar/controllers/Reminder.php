<?php

/**
 * Smart PHP Calendar Reminder Class
 * Copyright (c) 2010 Yasin Dagli, Smart PHP Calendar, All rights reserved.
 *
 * Checks popup and email reminders
 */

//var_dump(defined('USE_PHP_MAILER'));

class Calendar_Controller_Reminder extends SpcController {

    /**
     * Application userid
     *
     * @var int
     */
    protected $_userId;

    /**
     * Calendar Reminder Model
     *
     * @var object
     */
    public $reminder;

    /**
     * Current timestamp
     *
     * @var int
     */
    protected $_now;

    public $errors = array();

    public function __construct() {
        parent::__construct();
        $this->reminder = new Calendar_Model_Reminder;
    }

   	public function getDefaultReminders($calId) {
		$defaultReminders = $this->reminder->getDefaultReminders($calId);
        echo Spc::jsonEncode(array('defaultReminders' => $defaultReminders));
	}

    public function saveDefaultReminders($reminderOptions) {
        $this->reminder->saveDefaultReminders($reminderOptions);
    }

    public function getReminderMessages($calId) {
        $messages = $this->reminder->getReminderMessages($calId);
        echo Spc::jsonEncode(array('messages' => $messages));
    }

    public function saveReminderMessage($options) {
        $this->reminder->saveReminderMessage($options);
    }

    /**
	 * Checks Popup Reminder
	 *
	 * @param void
	 * @return
	 */
	public function checkPopupReminder() {
		$this->reminder->checkPopupReminder();
    }

    public function initCalReminders() {
        $this->reminder->initCalReminders();
    }
}