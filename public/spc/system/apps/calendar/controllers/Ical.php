<?php
/**
 * Smart PHP Calendar
 *
 * @category   Spc
 * @package    Calendar
 * @copyright  Copyright (c) 2008-2011 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */

/**
 * ICal Controller
 */
class Calendar_Controller_Ical extends SpcController {

    /**
     * Ical Model
     *
     * @var object
     */
    public $ical;

    public function __construct() {
        parent::__construct();

        $this->ical = new Calendar_Model_Ical();
    }

    public function exportCalendar($calId, $eventId = null) {
        return $this->ical->exportCalendar($calId, $eventId);
    }

    public function import($calId, $ics) {
        $this->ical->import($calId, $ics);
    }
}