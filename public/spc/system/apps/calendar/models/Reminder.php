<?php

class Calendar_Model_Reminder extends SpcDbTable {

    /**
     * spc_calendar_reminders table name
     *
     * @var string
     */
    protected $_name = 'calendar_reminders';

    /**
     * SPC Email
     *
     * @var object
     */
    protected $mail;

    /**
     * Date Helper Object
     *
     * @var object
     */
    protected $_dateHelper;

    /**
     * Current timestamp
     *
     * @var int
     */
    protected $_now;

    public function __construct() {
        parent::__construct();

        $this->_dateHelper = new Calendar_Helper_Date;
        $this->mail = new Core_Controller_Email;
    }

    /**
     * Updates current time by user's timezone
     *
     * @param string $timezone
     */
    public function updateNow($timezone = 'Europe/Istanbul') {
        date_default_timezone_set($timezone);
        $this->_now = date('Y-m-d H:i:00');
    }

    /**
     * Inits default calendar reminders and popup reminder messages for client app
     */
    public function initCalReminders() {
        $userId = SPC_USERID;

        $calDefaultReminders = array();
        $calPopupMessages = array();

        $calModel = new Calendar_Model_Calendar();
        $userCals = $calModel->getUserCals($userId, true);

        foreach ($userCals as $calId => $cal) {
            $calPopupMessages[$calId] = $cal['reminder_message_popup'];
            //create calendar default reminders arrays, see below
            $calDefaultReminders[$calId] = array();

            $select = $this->select(array('cal_id', 'type', 'time', 'time_type'),
                                    'spc_calendar_default_reminders')

                           ->where("cal_id = $calId");

            foreach ($this->fetchAll($select) as $reminder) {
                $calDefaultReminders[$calId][] = $reminder;
            }
        }

        echo Spc::jsonEncode(array('defaultReminders' => $calDefaultReminders,
                                    'defaultPopupReminderMessages' => $calPopupMessages));
    }

    public function getDefaultReminders($calId) {
        $select = $this->select()
                ->from('spc_calendar_default_reminders')
                ->where("cal_id = $calId");

        return $this->fetchAll($select);
	}

    public function getReminderMessages($calId) {
       $select = $this->select(array(
           'reminder_message_email AS emailMsg',
           'reminder_message_popup AS popupMsg'
       ),
       'spc_calendar_calendars')->where("id = $calId");

        list($messages['email'], $messages['popup']) = $this->fetchRow($select, 'row');
        return $messages;
	}

    public function saveReminderMessage($options) {
        $calId = $options['calId'];
        $messages = $options['messages'];
		$emailMsg = $messages['email'];
		$popupMsg = $messages['popup'];
        $saveForAll = isset ($options['saveForAll']) ? true : false;

		if ($saveForAll) {
            $this->update(array(
                'reminder_message_email' => $emailMsg,
                'reminder_message_popup' => $popupMsg
                ),
                    "user_id = {$this->_userId}",
                        'spc_calendar_calendars');
        } else {
             $this->update(array(
                'reminder_message_email' => $emailMsg,
                'reminder_message_popup' => $popupMsg
                ),
                    "id = {$calId}",
                        'spc_calendar_calendars');
        }
	}

    public function saveDefaultReminders($reminderOptions) {
        $calId = $reminderOptions['calId'];
        $reminders = $reminderOptions['reminders'];

		if (!isset($reminderOptions['saveForAll'])) {
            $this->delete("cal_id = $calId", 'spc_calendar_default_reminders');
		} else {
			$this->delete("user_id = {$this->_userId}", 'spc_calendar_default_reminders');
		}

		if (empty($reminders)) {
			return;
		}

		if (isset($reminderOptions['saveForAll'])) {
			$reminderSql = 'INSERT INTO
								spc_calendar_default_reminders
							VALUES ';

			$remindersArr = array();
			foreach ($_SESSION['spcUserCalendars'] as $calId => $calendar) {
				foreach ($reminders as $reminder) {
					$remindersArr[] = sprintf('(NULL, %d, %d, "%s", %d, "%s")',
											  $this->_userId,
											  $calId,
											  $reminder['type'],
											  $reminder['time'],
											  $reminder['timeType']);
				}
			}

			$reminderSql .= implode(', ', $remindersArr);
            $this->query($reminderSql);
            return;
		}

		$reminderSql = 'INSERT INTO
							spc_calendar_default_reminders
						VALUES ';

		$remindersArr = array();

		foreach ($reminders as $reminder) {

			$remindersArr[] = sprintf('(NULL, %d, %d, "%s", %d, "%s")',
									  $this->_userId,
									  $calId,
									  $reminder['type'],
									  $reminder['time'],
									  $reminder['timeType']);
		}

		$reminderSql .= implode(', ', $remindersArr);

		$this->query($reminderSql);
    }

    /**
     * Gets event reminders (on event click)
     *
     * @param int $eventId
     * @return array
     */
    public function getEventReminders($eventId) {
        $select = $this->select()
                        ->where("event_id = $eventId");

        $reminders = $this->fetchAll($select);
        return $reminders;
    }

    /**
     *
     * @param string $date
     * @param string $startTime
     * @param int $reminderTime
     * @param string $reminderTimeUnit
     * @return int
     */
    public function calculateReminderTimeStamp($date, $startTime, $reminderTime, $reminderTimeUnit) {
		$eventTs = strtotime("$date $startTime");

        switch ($reminderTimeUnit) {
            case 'minute':
                $remindTs = $eventTs - ($reminderTime * (60));
                break;

            case 'hour':
                $remindTs = $eventTs - ($reminderTime * (60 * 60));
                break;

            case 'day':
                $remindTs = $eventTs - ($reminderTime * (24 * 60 * 60));
                break;

            case 'week':
                $remindTs = $eventTs - ($reminderTime * (7 * 24 * 60 * 60));
                break;

            default :
                throw new Exception('Undefined Timeunit');
        }

		return date('Y-m-d H:i:00', $remindTs);
	}

    /**
     * Inserts event reminders
     *
     * @param int $eventId
     * @param array $reminders
     */
    public function insertEventReminders($eventId, $eventStartDate, $eventStartTime, $reminders) {
        foreach ($reminders as $reminder) {
            $reminder['event_id'] = $eventId;
            $reminder['ts'] = $this->calculateReminderTimeStamp($eventStartDate,
                                                                $eventStartTime,
                                                                $reminder['time'],
                                                                $reminder['time_unit']);
            list($d, $t) = explode(' ', $reminder['ts']);
            $reminder['remind_time'] = substr($t, 0, 5);

            $this->insert($reminder);
        }
    }

    /**
     * Updates event reminders
     * If reminders is null update type will be assumed upon dragged event update
     * In this situation all reminders will be retrieved from spc_calendar_reminders
     * table and will be rewritten with the new $eventStartDate and $eventStartTime
     * Otherwise all reminders will be deleted and new reminders (in the edit-event-dialog)
     * will be written.
     *
     * @param int $eventId
     * @param string $eventStartDate
     * @param string $eventStartTime
     * @param mixed $reminders
     */
    public function updateEventReminders($eventId, $eventStartDate, $eventStartTime, $reminders = null) {
        //drag-update
        if (!$reminders) {
            $select = $this->select(
                array('type', 'time', 'time_unit', 'is_repeating_event', 'remind_time')
            )
            ->where("event_id = $eventId");

            $reminders = $this->fetchAll($select);

            $this->delete("event_id = $eventId");

            $this->insertEventReminders($eventId, $eventStartDate, $eventStartTime, $reminders);
            return;
        }

        $this->delete("event_id = $eventId");

        $this->insertEventReminders($eventId, $eventStartDate, $eventStartTime, $reminders);
    }

    /**
	 * Checks Client Browser Popup Reminder
	 *
	 * @param void
	 * @return
	 */
	public function checkPopupReminder() {
		$userId             = SPC_USERID;
		$userEmail          = SPC_USER_EMAIL;
		$userLang           = SPC_USER_LANG;
        $userTimezone       = SPC_USER_TIMEZONE;
        $userTimeFormat     = Spc::getUserPref('timeformat', 'calendar');

        $this->updateNow($userTimezone);

        $clock = $this->_dateHelper->getClock();
        $timemarker = $this->_dateHelper->getTimemarker();
        $remindEvents = $this->getRemindEvents($userId, 'popup', $this->_now);

        echo Spc::jsonEncode(array(
            'clock' => $clock,
            'timemarker' => $timemarker,
            'events' => $remindEvents
        ));
    }

    /**
     * Calendar Event Email Reminder
     */
    public function checkEmailReminder() {
        //get all calendar users
        $select = $this->select(
            array('id', 'username', 'email', 'timezone', 'language'),
            'spc_users'
        )
        ->join('spc_calendar_settings',
               'spc_calendar_settings.user_id = spc_users.id',
               array('shortdate_format', 'timeformat'));

        $users = $this->fetchAll($select);

        foreach ($users as $user) {
            $userId                 = $user['id'];
            $userEmail              = $user['email'];
            $userLanguage           = $user['language'];
            $userTimezone           = $user['timezone'];
            $userShortdateFormat    = $user['shortdate_format'];
			$timeformat             = $user['timeformat'];

            $this->updateNow($userTimezone);
            $remindEvents = $this->getRemindEvents($userId, 'email', $this->_now);
            foreach ($remindEvents as $event) {
                $event['user_start_date'] = $this->_dateHelper->convertDate($event['start_date'], $userShortdateFormat);
                $event['user_start_time'] = $this->_dateHelper->convertTime($event['start_time'], $timeformat);

                $event['user_end_date'] = $this->_dateHelper->convertDate($event['end_date'], $userShortdateFormat);
                $event['user_end_time'] = $this->_dateHelper->convertTime($event['end_time'], $timeformat);

                $this->sendEmailReminder($userEmail, $event);
            }
        }
    }

    /**
     * Adds reminded Popup events to the SESSION['spcRemindedPopupEvents'] variable
     * and guarantees that each event will be reminded once
     *
     * @param int $eventId
     */
    public function checkPopupRemindedEvent($eventId) {
        if (isset($_SESSION['spcPopupRemindedEvents'][$eventId])) {
            return true;
        }

        $_SESSION['spcPopupRemindedEvents'][$eventId] = true;

        return false;
    }

    /**
     * Gets events to be reminded on $timestamp
     *
     * @param int $userId
     * @param string $remindType
     * @param int $timeStamp
     * @return array
     */
    public function getRemindEvents($userId, $remindType, $timestamp) {
        //
        // get reminders for non-repeating events
        //
        $calModel = new Calendar_Model_Calendar;
        $userCals = $calModel->getUserCals($userId, true);
        $remindEvents = array();

        foreach ($userCals as $calId => $cal) {
            $select = $this->select(
                array('id',
                    'cal_id',
                    'start_date',
                    'start_time',
                    'end_date',
                    'end_time',
                    'title',
                    'location',
                    'description'), 'spc_calendar_events')

                    ->join('spc_calendar_reminders', 'spc_calendar_reminders.event_id = spc_calendar_events.id')

                    ->where("spc_calendar_reminders.is_repeating_event = '0'")
                    ->where("spc_calendar_reminders.ts = '$timestamp'")
                    ->where("spc_calendar_reminders.type = '$remindType'")
                    ->where("spc_calendar_events.cal_id = $calId");

            foreach ($this->fetchAll($select) as $event) {
                if ($this->checkPopupRemindedEvent($event['id'])) {
                    continue;
                }

                $event['cal_name'] = $cal['name'];
                $event['email_reminder_msg_tmpl'] = $cal['reminder_message_email'];

                $remindEvents[] = $event;
            }
        }

        //
        // get reminders for repeating events
        //
        list($remindDate, $remindTime) = explode(' ', $timestamp);
        $remindTime = substr($remindTime, 0, 5);

        $repeatingEvents = array();

        foreach ($userCals as $calId => $cal) {
            $select = $this->select(
                array('id',
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
                    'repeat_data'
                    ), 'spc_calendar_events')

                    ->join('spc_calendar_reminders',
                           'spc_calendar_reminders.event_id = spc_calendar_events.id')

                    ->where('spc_calendar_reminders.is_repeating_event = "1"')
                    ->where("spc_calendar_reminders.type = '$remindType'")
                    ->where("spc_calendar_reminders.remind_time = '$remindTime'")
                    ->where("spc_calendar_events.cal_id = $calId")
                    ->where("spc_calendar_events.repeat_end_date >= '$remindDate'");

            foreach ($this->fetchAll($select) as $event) {
                if ($this->checkPopupRemindedEvent($event['id'])) {
                    continue;
                }

                $event['cal_name'] = $cal['name'];
                $event['email_reminder_msg_tmpl'] = $cal['reminder_message_email'];

                $repeatingEvents[] = $event;
            }
        }

        $eventModel = new Calendar_Model_Event;
        foreach ($repeatingEvents as $repeatingEvent) {
            $generatedEvents = array();
            $eventModel->addRepeatEvents($repeatingEvent, $generatedEvents, $remindDate, $remindDate);
            if (isset($generatedEvents['all'][$remindDate])) {
                if ($this->checkPopupRemindedEvent($repeatingEvent['id'])) {
                    continue;
                }

                $remindEvents[] = $repeatingEvent;
            }
        }

        return $remindEvents;
    }

    /**
     * Sends calendar email reminder
     *
     * @param string $userEmail
     * @param array $event
     */
    public function sendEmailReminder($userEmail, $event) {
        $patterns = array('/%calendar%/',
                          '/%start-date%/',
                          '/%start-time%/',
                          '/%end-date%/',
                          '/%end-time%/',
                          '/%title%/',
                          '/%location%/',
                          '/%description%/');

        $replacements = array($event['cal_name'],
                              $event['user_start_date'],
                              $event['user_start_time'],
                              $event['user_end_date'],
                              $event['user_end_time'],
                              $event['title'],
                              $event['location'],
                              $event['description']);

        $reminderMsg = preg_replace($patterns, $replacements, $event['email_reminder_msg_tmpl']);

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type:text/html;charset=utf-8' . "\r\n";
        $headers .= 'From: <reminder@smartphpcalendar.com>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        echo $userEmail . '<br />' . $reminderMsg;

        $this->mail->send(EMAIL_REMINDER_FROM, $userEmail, EMAIL_REMINDER_SUBJECT, $reminderMsg, $headers);
    }
}