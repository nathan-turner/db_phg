<?php

/**
 * Smart PHP Calendar
 *
 * @copyright  Copyright (c) 2008-2013 Yasin Dagli (http://www.smartphpcalendar.com)
 */

/**
 * Smart PHP Calendar Ical Engine
 */
class Calendar_Model_Ical extends SpcDbTable {

    private $iCalEngineVer = 0.4; //2013-01-08

    /**
     * Events table
     *
     * @var string
     */
    protected $_name = 'calendar_events';

    private $_spcEvents = array();
    private $_icalEvents = array();
    private $_importTimezone;
    private $_timeDiff;

    /**
     * SPC-Event default repeating rules
     * (Used for importing)
     *
     * @var array
     */
    private $_spcDefaultRepeatRules = array(
        'repeat_type' => 'none',
        'repeat_interval' => 1,
        'repeat_count' => 0,
        'repeat_end_date' => '9999-01-01',
        'repeat_data' => ''
    );

    /**
     * Day Names for iCal
     * (Used for exporting)
     *
     * @var array
     */
    public $icalWeekDayNames = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');

    //--------------------------------------------------------------------------
    //
    // EXPORT METHODS
    //
    //--------------------------------------------------------------------------

    /**
     * Exports SPC Calendar or SPC Event in ical string
     *
     * @param int $calId
     * @param int $eventId
     * @return string (iCal)
     */
    public function exportCalendar($calId = null, $eventId = null) {
        //get calendar name
        if ($calId) {
            $select = $this->select()->from('spc_calendar_calendars', array('name'))->where("id = $calId");
        } else if ($eventId) {
            $select = $this->select()
                            ->from('spc_calendar_calendars', array('name'))
                            ->join('spc_calendar_events', 'spc_calendar_calendars.id = spc_calendar_events.cal_id')
                            ->where("spc_calendar_events.id = $eventId");
        }


        $calName = $this->fetchColumn($select);

        $select = $this->select(array(
            'id',
            'cal_id',
            'start_date',
            'start_time',
            'end_date',
            'end_time',
            'title',
            'location',
            'description',
            'repeat_type',
            'repeat_interval',
            'repeat_count',
            'repeat_end_date',
            'repeat_data',
            'created_on',
            'updated_on',
            'invitation',
            'public'
        ));

        //export event
        if ($eventId !== null) {
            $select->where("id = $eventId");

        //export calendar
        } else {
            $select->where("cal_id = $calId");
        }

        $events = $this->fetchAll($select);

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "PRODID:-//Smart PHP Calendar//Smart PHP Calendar iCal Engine {$this->iCalEngineVer} //EN\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";

        //request for a meeting | event invitation
        if ($eventId && $events[0]['invitation'] == 1) {
            $ical .= "METHOD:REQUEST\r\n";

        //standard calendar export
        } else {
            $ical .= "METHOD:PUBLISH\r\n";
        }

        $ical .= "X-WR-CALNAME:$calName\r\n";

        $tz = Spc::getUserPref('timezone');
        $this->_timeDiff = date('Z');

        $ical .= "X-WR-TIMEZONE:$tz\r\n";

        foreach ($events as $event) {
            $ical .= $this->getICalEvent($event) . "\r\n";
        }

        $ical .= 'END:VCALENDAR';

        return $ical;
    }

    /**
     * Converts SPC Event array to iCal event object
     * (Used for exporting)
     *
     * @param array $event
     * @return string
     */
    public function getICalEvent($event) {
        $id             = $event['id'];
        $calId          = $event['cal_id'];

        $startDate      = $event['start_date'];
        $startTime      = $event['start_time'];
        $endDate        = $event['end_date'];
        $endTime        = $event['end_time'];

        $title          = $event['title'];
        $location       = $event['location'];
        $description    = $event['description'];

        $createdOn      = $event['created_on'];
        $createdOnTs    = strtotime($createdOn);
        $modifiedOn     = ($event['updated_on'] == null) ? $createdOn : $event['updated_on'];
        $modifiedOnTs    = strtotime($modifiedOn);

        $privacy = $event['public'] == 1 ? 'PUBLIC' : 'PRIVATE';

        //begin VEVENT
        $ical = "BEGIN:VEVENT\r\n";

        //event start date, end date
        //DTSTART, DTEND (exact UTC time for standard | all-day | multi-day)
        if (($startTime != '00:00') && ($endTime != '00:00')) {
            //DTSTART
            list($year, $month, $day) = explode('-', $startDate);
            list($hour, $min) = explode(':', $startTime);
            $startTs = mktime($hour, $min, 0, $month, $day, $year) - $this->_timeDiff;

            $startDate = date('Ymd', $startTs) . 'T' . date('His', $startTs) . 'Z';

            //DTEND
            list($year, $month, $day) = explode('-', $endDate);
            list($hour, $min) = explode(':', $endTime);
            $endTs = mktime($hour, $min, 0, $month, $day, $year) - $this->_timeDiff;

            $endDate = date('Ymd', $endTs) . 'T' . date('His', $endTs) . 'Z';

            $ical .= "DTSTART:{$startDate}\r\n";
            $ical .= "DTEND:{$endDate}\r\n";

        //DTSTART, DTEND (all-day | multi-day)
        } else {
            $startTs = strtotime($startDate);
            $endTs = strtotime($endDate);
            $startDate = date('Ymd', $startTs);
            $endDate = date('Ymd', strtotime('+1 day', $endTs));

            $ical .= "DTSTART;VALUE=DATE:{$startDate}\r\n";
            $ical .= "DTEND;VALUE=DATE:{$endDate}\r\n";
        }

        //event unique id
        $uid = md5($calId . '-' . $id);
        $ical .= "UID:$uid@smartphpcalendar.com\r\n";

        //privacy
        $ical .= "CLASS:{$privacy}\r\n";

        //event title
        $ical .= "SUMMARY:{$title}\r\n";

        //event location
        $ical .= "LOCATION:{$location}\r\n";

        //event description
        $description = preg_replace("/\\n|\\r|\\r\\n/", "\n", $description);
        $ical .= "DESCRIPTION:{$description}\r\n";

        //event repeating info
        $ical .= $this->getIcalEventRepeatInfo($event) . "\r\n";

        //create-time
        $ical .= "CREATED:" . date('Ymd', $createdOnTs) . 'T' . date('His', $createdOnTs) . 'Z' . "\r\n";

        //modify-time
        $ical .= "LAST-MODIFIED:" . date('Ymd', $modifiedOnTs) . 'T' . date('His', $modifiedOnTs) . 'Z' . "\r\n";

        //invitation-participants
        if ($event['invitation'] == 1) {
            $ical .= $this->getIcalEventParticipants($id);
        }

        //end VEVENT
        $ical .= "END:VEVENT";

        return $ical;
    }

    /**
     * Gets Ical Weekly Repeating Event's Days
     * (Used for exporting)
     *
     * @param array $dayIndexes (0: Sunday, 1: Monday, ...)
     * @return array (e.g. MO,WE,FR)
     */
    public function getIcalWeeklyRepeatDays($dayIndexes) {
        $weeklyRepeatDays = array();
        foreach ($dayIndexes as $dayIndex) {
            $weeklyRepeatDays[] = $this->icalWeekDayNames[$dayIndex];
        }

        return join(',', $weeklyRepeatDays);
    }

    /**
     * Gets SPC Repeating Event's ical string (RRULE)
     * (Used for exporting)
     *
     * @param array $event
     * @return string
     */
    public function getIcalEventRepeatInfo($event) {
        $type     = $event['repeat_type'];
        if ($type == 'none') {
            return '';
        }

        $repeatStartDate = $event['start_date'];
        list ($y, $m, $d) = explode('-', $repeatStartDate);
        $interval = $event['repeat_interval'];
        $count    = $event['repeat_count'];
        $endDate  = $event['repeat_end_date'];
        $data     = $event['repeat_data'];

        $repeatInfo = array();

        switch ($type) {
            case 'daily':
                $repeatInfo[] = 'RRULE:FREQ=DAILY';
                break;

            case 'weekly':
                $repeatInfo[] = 'RRULE:FREQ=WEEKLY';
                $repeatInfo[] = 'BYDAY=' . $this->getIcalWeeklyRepeatDays(explode(',', $data));
                break;

            case 'monthly':
                $repeatInfo[] = 'RRULE:FREQ=MONTHLY';
                //repeat by month day (everry September 15)
                if ($data == '') {
                    $repeatInfo[] = 'BYMONTHDAY=' . (int)$d;

                //repeat by week day (every Wednesday of the first week of September)
                } else {
                    list($weekIndex, $dayIndex) = explode(',', $data);
                    $repeatInfo[] = 'BYDAY=' . $weekIndex . $this->icalWeekDayNames[(int)$dayIndex];
                }
                break;

            case 'yearly':
                break;

            default:
                throw new Exception("Undefined repeat type: $type");
        }

        //interval
        if ($interval > 1) {
            $repeatInfo[] = "INTERVAL=$interval";
        }

        //count
        if ($count > 0) {
            $repeatInfo[] = "COUNT=$count";
        }

        //until (end-date)
        //Smart PHP Calendar Repeat Engine sets end-date only if it ends on specific date
        //other situations it will be allways 9999-01-01
        if ($endDate != '9999-01-01') {
            $repeatInfo[] = 'UNTIL=' . date('Ymd', strtotime($endDate)) . 'T235959Z';
        }

        return join(';', $repeatInfo);
    }

    public function getIcalEventParticipants($eventId) {
        $organizerName = SPC_USERNAME;
        $organizerEmail = SPC_USER_EMAIL;
        $icalParticipantsStr = "ORGANIZER;CN={$organizerName}:MAILTO:{$organizerEmail}\r\n";

        $select = $this->select()->from('spc_calendar_guests')->where("event_id = {$eventId}");
        $attendees = $this->fetchAll($select);

        $spcToiCalPartStats = array(
            'pending' => 'NEEDS-ACTION',
            'yes' => 'ACCEPTED',
            'no' => 'DECLINED',
            'maybe' => 'TENTATIVE'
        );

        foreach ($attendees as $attendee) {
            $partStat = $spcToiCalPartStats[$attendee['response']];
            $attendeeEmail = $attendee['email'];
            $icalParticipantsStr .= "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT={$partStat}:MAILTO:{$attendeeEmail}\r\n";
        }

        return $icalParticipantsStr;
    }

    //--------------------------------------------------------------------------
    //
    // IMPORT METHODS
    //
    //--------------------------------------------------------------------------

    /**
     * Parses iCalendar file to arrays of events (VEVENT Objects)
     * Each array represents an ical VEVENT Object
     * VEVENT Object's properties are kept as key => value pairs
     *
     * @example
     *      //VEVENT OBJECTS
     *      array(
     *
     *           //VEVENT OBJECT
     *          array(
     *              'DTSTART' => 'DTSTART:20110914T203000Z',
     *              'DTEND' => 'DTEND:20110914T215000Z',
     *              ...
     *          ),
     *
     *           //VEVENT OBJECT
     *          array(
     *              'DTSTART' => 'DTSTART:20110914T203000Z',
     *              'DTEND' => 'DTEND:20110914T215000Z',
     *              ...
     *          ),
     *          ...
     *      );
     *
     * @param string $ics (iCalendar file path to be parsed)
     */
    public function parseIcalEvents($ics) {
        if (filter_var($ics, FILTER_VALIDATE_URL)) {
            if (preg_match('/^webcal:\/\//i', $ics)) {
                $ics = str_replace('webcal://', 'http://', $ics);
            }
        }

        $icalEvents = array();
        $f = file($ics, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $i = 0;

        $timezoneSet = false;

        foreach ($f as $l) {
            if (preg_match('/TIMEZONE:(.*)/', $l, $m)) {
                //set import timezone
                $this->_importTimezone = trim($m[1]);
                #$f = fopen('tz.txt', 'r+'); fwrite($f, $this->_importTimezone . ' :Y');
                //set timezone diff
                date_default_timezone_set($this->_importTimezone);
                #date_default_timezone_set(SPC_USER_TIMEZONE);
                $this->_timeDiff = date('Z');
                $timezoneSet = true;
                continue;
            }

            if (!isset($icalEvents[$i])) {
                if (stristr('BEGIN:VEVENT', $l) !== false) {
                    $icalEvents[$i] = array();
                }
                continue;
            }

            if (stristr('END:VEVENT', $l) !== false) {
                $i++;
                continue;
            }

            //multi-line ical prop
            //if one ical property starts and takes more than one line add next lines to it
            if (!preg_match('/^[A-Z]/', $l)) {
                $icalEvents[$i][count($icalEvents[$i]) - 1] .= $l;

            //single-line ical property
            } else {
                $icalEvents[$i][] = $l;
            }
        }

        //if iCalendar file has no timezone set it to user's default timezone
        if (!$timezoneSet) {
            $this->_importTimezone = SPC_USER_TIMEZONE;
            #$f = fopen('tz.txt', 'a+');
            #fwrite($f, $this->_importTimezone);
            //set timezone diff
            date_default_timezone_set($this->_importTimezone);
            $this->_timeDiff = date('Z');
            #fwrite($f, ' time-diff: ' . $this->_timeDiff);
        }

        return $icalEvents;
    }

    public function convertIcalEventRepeatRule($icalRRule) {
        //default SPC repeating rules
        $spcRepeatRule = $this->_spcDefaultRepeatRules;

        $icalRRule = explode(';', substr($icalRRule, strpos($icalRRule, ':') + 1));
        foreach ($icalRRule as $rRule) {
            list ($rKey, $rVal) = explode('=', $rRule);

            //repeat frequence
            if ($rKey == 'FREQ') {
                if ($rVal == 'ANNUALLY') {
                    $rVal = 'yearly';
                }
                $spcRepeatRule['repeat_type'] = strtolower($rVal);
                continue;
            }

            //interval
            if ($rKey == 'INTERVAL') {
                $spcRepeatRule['repeat_interval'] = $rVal;
            }

            //count
            if ($rKey == 'COUNT') {
                $spcRepeatRule['repeat_count'] = $rVal;
            }

            //end-date
            if ($rKey == 'UNTIL') {
                list ($endDate, $endTime) = explode('T', $rVal);
                $endDate = date('Y-m-d', strtotime($endDate));
                $spcRepeatRule['repeat_end_date'] = $endDate;
            }

            //repeat-data
            if ($rKey == 'BYDAY') {
                //SPC holds weekly repeating days as indexes
                //convert ical week daynames to day-indexes
                //0:SU, 1:MO, ...
                if ($spcRepeatRule['repeat_type'] == 'weekly') {
                    $repeatDayIndexes = array();
                    foreach (explode(',', $rVal) as $icalDayName) {
                        $repeatDayIndexes[] = array_search($icalDayName, $this->icalWeekDayNames);
                    }
                    $rVal = join(',', $repeatDayIndexes);
                }

                // SPC holds monthly repeating days as indexes
                // (by weekday, e.g. monthly on the fourth Wednesday)
                //
                // week-index,weekday-index
                // (monthly on the fourth Wednesday = 4,3)
                // (this info kept in ics files as this: 4WE)
                if ($spcRepeatRule['repeat_type'] == 'monthly') {
                    $repeatWeekIndex = substr($rVal, 0, 1);
                    $repeatWeekDayIndex = array_search(substr($rVal, 1), $this->icalWeekDayNames);
                    $rVal = $repeatWeekIndex  . ',' . $repeatWeekDayIndex;
                }

                //insert repeat data
                //repeat data is used for weekly repeating
                //or monthly repeating events (by monthday, first Wednesday, last Friday, etc)
                $spcRepeatRule['repeat_data'] = $rVal;
            }
        }

        return $spcRepeatRule;
    }

    /**
     *
     * @param type $i
     * @param type $l
     */
    public function convertIcalProp($prop, &$spcEvent) {
        if (preg_match('/^RRULE/', $prop)) {
            $prop = preg_replace('/\s/', '', $prop);
            return;
        }

        //start-date and start-time
        if (preg_match('/^DTSTART/', $prop)) {
            $dateTime = end(@explode(':', $prop));
            if (stristr($prop, 'VALUE=DATE') !== false) {
                $startDate = substr($dateTime, 0, 4) . '-' . substr($dateTime, 4, 2) . '-' . substr($dateTime, 6, 2);
                $startTime = '00:00';
            } else {
                $timeDiff = $this->_timeDiff;
                if (stristr($prop, 'TZID') !== false) {
                    preg_match('/TZID=(.*)[;:]/i', $prop, $m);
                    if (preg_match('/GMT/i', $m[1])) {
                        date_default_timezone_set('Europe/London');
                    } else {
                        date_default_timezone_set($m[1]);
                    }
                    $timeDiff = date('Z');
                }

                list ($startDate, $time) = explode('T', $dateTime);
                $year = substr($startDate, 0, 4);
                $month = substr($startDate, 4, 2);
                $day = substr($startDate, 6, 2);
                $hour = substr($time, 0, 2);
                $min = substr($time, 2, 2);
                $sec = substr($time, 4, 2);

                $ts = mktime($hour, $min, $sec, $month, $day, $year) + $timeDiff;

                list($startDate, $startTime) = explode('|', date('Y-m-d|H:i', $ts));
            }

            $spcEvent['start_date'] = $startDate;
            $spcEvent['start_time'] = $startTime;
            return;
        }

        //end-date and end-time
        if (preg_match('/^DTEND/', $prop)) {
            $dateTime = end(@explode(':', $prop));
            if (stristr($prop, 'VALUE=DATE') !== false) {
                $endDate = substr($dateTime, 0, 4) . '-' . substr($dateTime, 4, 2) . '-' . substr($dateTime, 6, 2);
                $endTime = '00:00';
            } else {

                $timeDiff = $this->_timeDiff;
                if (stristr($prop, 'TZID') !== false) {
                    preg_match('/TZID=(.*?)[;:]/i', $prop, $m);
                    if (preg_match('/GMT/i', $m[1])) {
                        date_default_timezone_set('Europe/London');
                    } else {
                        date_default_timezone_set($m[1]);
                    }
                    $timeDiff = date('Z');
                }

                list ($endDate, $time) = explode('T', $dateTime);
                $year = substr($endDate, 0, 4);
                $month = substr($endDate, 4, 2);
                $day = substr($endDate, 6, 2);
                $hour = substr($time, 0, 2);
                $min = substr($time, 2, 2);
                $sec = substr($time, 4, 2);

                $ts = mktime($hour, $min, $sec, $month, $day, $year) + $timeDiff;

                list($endDate, $endTime) = explode('|', date('Y-m-d|H:i', $ts));
            }

            $spcEvent['end_date'] = $endDate;
            $spcEvent['end_time'] = $endTime;
            return;
        }

        //title
        if (preg_match('/^SUMMARY/', $prop)) {
            $spcEvent['title'] = substr($prop, (strpos($prop, ':') + 1));
            return;
        }

        //location
        if (preg_match('/^LOCATION/', $prop)) {
            $spcEvent['location'] = substr($prop, (strpos($prop, ':') + 1));
            return;
        }

        //description
        if (preg_match('/^DESCRIPTION/', $prop)) {
            $spcEvent['description'] = substr($prop, (strpos($prop, ':') + 1));
            #$spcEvent['description'] = preg_replace("/\\n|\\r|\\r\\n|\\n|\\n\\r/", "", $spcEvent['description']);
            return;
        }

        //repeat
        if (preg_match('/^RRULE/', $prop)) {
            $spcEvent = array_merge($spcEvent, $this->convertIcalEventRepeatRule($prop));
            return;
        }
    }

    public function convertIcalEvent($icalEvent) {
        $spcEvent = array();
        foreach ($icalEvent as $prop) {
            $this->convertIcalProp($prop, $spcEvent);
        }

        return $spcEvent;
    }

    public function convertIcalEvents($icalEvents) {
        $spcEvents = array();
        foreach ($icalEvents as $icalEvent) {
            $spcEvents[] = $this->convertIcalEvent($icalEvent);
        }

        return $spcEvents;
    }

    /**
     * Import iCalendar File
     *
     * @param int $calId,
     * @param string $ics (iCalendar file path)
     * @return bool
     */
    public function import($calId, $ics) {
        //parse .ics file to iCal VEVENT event objects
        $parsedIcalEvents = $this->parseIcalEvents($ics);

        #echo '<pre><textarea>'; print_r($parsedIcalEvents); exit;
        //convert parsed ical events to SPC-Events
        $spcEvents = $this->convertIcalEvents($parsedIcalEvents);
        #echo '<pre>'; print_r($spcEvents); exit;

        #$defaultImportEventPrivacy = (ICAL_IMPORT_EVENT_PRIVACY == 'public') ? '1' : '0';

        $calModel = new Calendar_Model_Calendar();
        $orgCal = $calModel->getCalendar($calId);
        $defaultImportEventPrivacy = $orgCal['public'];

        foreach ($spcEvents as &$e) {
            $startDate      = $e['start_date'];
            $endDate        = isset($e['end_date']) ? $e['end_date'] : $startDate;
            $startTime      = $e['start_time'];
            $endTime        = isset($e['end_time']) ? $e['end_time'] : $startTime;

            $e['cal_id']        = $calId;
            $e['title']         = isset($e['title']) && $e['title'] ? $e['title'] : '';
            $e['location']      = isset($e['location']) && $e['location'] ? $e['location'] : '';
            $e['description']   = isset($e['description']) && $e['description'] ? $e['description'] : '';
            $e['image']         = '';

            //Smart PHP Calendar - all-day event conversion (ical to spc)
            if (($startTime == '00:00') && ($endTime == '00:00')) {
                $e['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime($endDate)));
            }

            //spc event type
            $e['type'] = 'standard';
            if ($startDate != $endDate) {
                $e['type'] = 'multi_day';
            }

            //free/busy
            $e['available'] = '0';
            //privacy (public|private)
            $e['public'] = $defaultImportEventPrivacy;

            //repeat
            if (!isset($e['repeat_type'])) {
                $e = array_merge($e, $this->_spcDefaultRepeatRules);
            }

            //created by (calendar owner)
            $e['created_by'] = SPC_USERNAME;

            $this->sanitizeInput($e);

            $this->insert($e);
        }

        //!TODO: batch insert
        #$this->insert($this->_icalEvents);
        return true;
    }
}