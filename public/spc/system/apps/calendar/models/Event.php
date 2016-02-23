<?php

class Calendar_Model_Event extends SpcDbTable {

    /**
     * database table
     * @var object
     */
    protected $_name = 'calendar_events';

    /**
     * core system mailer
     * @var object
     */
    protected $_mailer;

    /**
     * Date utilities (date converters)
     * @var object
     */
    protected $_dateHelper;

    public function __construct($dbParams = null) {
        parent::__construct($dbParams);

        $this->_mailer = new Core_Controller_Email;
        $this->_dateHelper = new Calendar_Helper_Date;
    }

    /**
     * Gets event in a specified data ranges with the selected calendars
     *
     * @param type $calendarView
     * @param type $calendars
     * @param type $startDate
     * @param type $endDate
     * @param type $public
     * @return string
     */
    public function getEvents($calendarView, $calendars, $startDate,
                              $endDate, $public = false) {

        $sender = Spc::getSender();

        if ($sender == 'event-calendar') {
            $public = true;
        }

        $events 				= array();
		$events['all'] 			= array();
		$events['standard'] 	= array();
		$events['all_day'] 		= array();
		$events['multi_day'] 	= array();
        $events['repeat']       = array();

        $eventFields = array(
            'id',
            'cal_id',
            'type',
            'start_date',
            'start_time',
            'end_date',
            'end_time',
            'title',
            'created_by',
            'available',
            'repeat_type',
            'repeat_interval',
            'repeat_count',
            'repeat_end_date',
            'repeat_data',
            'repeat_deleted_indexes',
            'invitation',
            'invitation_event_id',
            'invitation_response'
        );

        //add description for rss and print view
        if ($calendarView == 'rss'
                || $calendarView == 'print'
                || $sender == 'event-calendar'
                || $sender == 'public') {

            $eventFields[] = 'description';
            $eventFields[] = 'location';
            $eventFields[] = 'image';
        }

        //if view is events-calendar-plugin get all public events
        if (count($calendars) == 0) {
            $calendars = array_keys($_SESSION['calendars']);
        }

        //used for loop increment to define multi-day events spanning dates
        $oneDayInSecond = (24 * 60 * 60);

		foreach ($calendars as $calId) {
            if ($calId <= 0) {
                continue;
            }

            //------------------------------------------------------------------
            // standard and all-day events
            //------------------------------------------------------------------

            $select = $this->select($eventFields)
                            ->where("cal_id = $calId")
                            ->where('repeat_type = "none"')
                            ->where('type = "standard"');

            $select->where("start_date BETWEEN '$startDate' AND '$endDate'");

            if ($public || stristr($sender, 'public')) {
                $select->where("public = '1'");
            }

            $standardEvents = $this->fetchAll($select);

			foreach ($standardEvents as $event) {
				if (($event['start_time'] == '00:00')
                        && ($event['end_time'] == '00:00')) {

					$event['type'] = 'all_day';
				}

				$events[$event['type']][$event['start_date']][] = $event;
				$events['all'][$event['start_date']][] = $event;
			}

            //------------------------------------------------------------------
            // multi-day events
            //------------------------------------------------------------------

            $select = $this->select($eventFields)
                            ->where("cal_id = $calId")
                            ->where('repeat_type = "none"')
                            ->where('type = "multi_day"');

            $select->where("start_date BETWEEN '$startDate' AND '$endDate'
                            OR end_date BETWEEN '$startDate' AND '$endDate'
                            OR start_date < '$startDate' AND end_date > '$endDate'");

            if ($public || stristr($sender, 'public')) {
                $select->where("public = '1'");
            }

            $multiDayEvents = $this->fetchAll($select);
            foreach ($multiDayEvents as $event) {
                $eventId = $event['id'];
                $events['multi_day'][] = $event;

                //split multi-day event by days and add it to all events
                $multiDayEventStartDateTs = strtotime($event['start_date']);
                $multiDayEventEndDateTs = strtotime($event['end_date']);
                $dayDiff = (($multiDayEventEndDateTs - $multiDayEventStartDateTs) / $oneDayInSecond);
                for ($i = 0; $i <= $dayDiff; $i++) {
                    $newDate = date('Y-m-d', strtotime("+$i days", $multiDayEventStartDateTs));
                    $events['all'][$newDate][] = $event;
                }
            }

            //------------------------------------------------------------------
            // repeat events
            //------------------------------------------------------------------
            $select = $this->select($eventFields)
                        ->where("cal_id = $calId")
                        ->where('repeat_type != "none"')
                        ->where("start_date BETWEEN '$startDate' AND '$endDate'
                                OR repeat_end_date BETWEEN '$startDate' AND '$endDate'
                                OR start_date < '$startDate' AND repeat_end_date > '$endDate'");

            if ($public || stristr($sender, 'public')) {
                $select->where("public = '1'");
            }

            $events['repeat'] = array_merge($events['repeat'], $this->fetchAll($select));
        }

        //TODO: do all sorting on client side
        //----------------------------------------------------------------------
        // sort events['all']
        //----------------------------------------------------------------------
        ksort($events['all']);

        return $events;
    }

    /**
     * Gets event (spc-event-click)
     *
     * @param type $eventId
     * @return type
     */
    public function getEvent($eventId) {
        $select = $this->select()
                       ->where("id = $eventId");
        $event = $this->fetchRow($select);

        $requester = Spc::getSender();

        //get event reminders
		if ($requester == 'private') {
            //reminders
            $reminderModel = new Calendar_Model_Reminder;
            $reminders = $reminderModel->getEventReminders($eventId);
            $event['reminders'] = $reminders;

            //invitation, get invitation response if event invitation is open
            if ($event['invitation'] == '1') {
                $invitationEventId = ($event['invitation_event_id'] == 0)
                                   ? $event['id']
                                   : $event['invitation_event_id'];

                $select = $this->select()
                               ->from('spc_calendar_guests', 'response')
                               ->where("event_id = $invitationEventId AND user_id = $this->_userId");

                $event['invitationResponse'] = $this->fetchColumn($select);
            }
		}

        if ($event['image']) {
            $username = Spc::getUserPref('username');
            $event['thumbPath'] = SPC_ROOT . '/system/apps/calendar/files/user-images/' . $username . '/thumb/' . $event['image'];
            $event['imagePath'] = SPC_ROOT . '/system/apps/calendar/files/user-images/' . $username . '/org/' .  $event['image'];
        }

        //add guests to the event if sender is the application owner or invitation invitee
        if ($requester == 'private' || $requester == 'private:invitee') {
            $event['guests'] = $this->getGuests($eventId);
        }

        return $event;
    }

    public function getTs($date, $time) {
        return strtotime("{$date} {$time}");
    }

    /**
     * Inserts a new event
     *
     * @param type $event
     */
    public function insertEvent($event) {
        //original event that will be pushed to the spc_calendar_events table
        $orgEvent = $event['event'];
        $orgEvent['created_by'] = SPC_USERNAME;
        $orgEvent['created_on'] = date('Y-m-d H:i:s');

        $this->begin();
        //insert event
        $eventId = $this->insert($orgEvent);

        //insert event reminders
        if (isset($event['reminders'])) {
            $reminderModel = new Calendar_Model_Reminder;
            $reminderModel->insertEventReminders($eventId,
                                                 $orgEvent['start_date'],
                                                 $orgEvent['start_time'],
                                                 $event['reminders']);
        }

        //invitation - insert guests
        if (isset($event['guests'])) {
            $this->insertEventGuests($eventId, $orgEvent, $event['guests']);
        }

        $this->commit();

        return $eventId;
    }

    public function deleteEvent($eventId, $invitation, $invitationEventId, $repeatIndexes = null) {
        //invitation owner (delete all invitations)
        if ($invitation == '1' && $invitationEventId == '0') {
            $this->delete("invitation_event_id = $eventId");
            $this->notifyGuests($eventId, 'canceled');
        }

        //invitation guest (delete guest from invitation guest list)
        if ($invitation == '1' && $invitationEventId != '0') {
            $this->delete("user_id = {$this->_userId} AND event_id = $invitationEventId",
                          'spc_calendar_guests');
        }

        //delete repeating event
        if ($repeatIndexes) {
            $this->update(array('repeat_deleted_indexes' => $repeatIndexes), "id = {$eventId}");
            return;
        }

        //delete original event
        $this->delete("id = $eventId");
    }

    /**
     * Updates an event
     *
     * @param type $event
     */
    public function updateEvent($event) {
        $userId = SPC_USERID;
        $orgEvent = $event['event'];
        $eventId = $orgEvent['id'];
        unset($orgEvent['id']);

        $orgEvent['modified_by'] = SPC_USERNAME;
        $orgEvent['updated_on'] = date('Y-m-d H:i:s');

        $reminderModel = new Calendar_Model_Reminder;

        //
        // update event body
        //

        $this->begin();

        //normal update
        if ($orgEvent['invitation'] == 0) {
            $this->update($orgEvent, "id = $eventId");

            //update event reminders
            if (isset($event['reminders'])) {
                $reminderModel->updateEventReminders($eventId,
                                                    $orgEvent['start_date'],
                                                    $orgEvent['start_time'],
                                                    $event['reminders']);
            }

        //if event is an invitation only event creator can update original event
        } else if (($orgEvent['invitation'] == 1)
                        && ($orgEvent['invitation_creator_id'] == SPC_USERID)) {

            //update original invitation
            $this->update($orgEvent, "id = $eventId");

            //update event reminders (original invitation)
            if (isset($event['reminders'])) {
                $reminderModel->updateEventReminders($eventId,
                                                    $orgEvent['start_date'],
                                                    $orgEvent['start_time'],
                                                    $event['reminders']);
            }

            //update guests' invitations
            unset($orgEvent['cal_id']);
            $this->update($orgEvent, "invitation_event_id = $eventId");

            //update guests' invitation reminders
            $select = $this->select('id')
                           ->where("invitation_event_id = $eventId");

            foreach ($this->fetchAll($select) as $guestInvitationEvent) {
                $reminderModel->updateEventReminders($guestInvitationEvent['id'],
                                                    $orgEvent['start_date'],
                                                    $orgEvent['start_time']);
            }

        //if event is an invitation, guests can update their event's invitation response
        //and their own reminders
        } else if (($orgEvent['invitation'] == 1)
                        && ($orgEvent['invitation_creator_id'] != SPC_USERID)) {

            $this->update(
                array('invitation_response' => $orgEvent['invitation_response']),
                "id = $eventId"
            );

            $reminders = isset($event['reminders']) ? $event['reminders'] : array();
            $reminderModel->updateEventReminders($eventId,
                                                 $orgEvent['start_date'],
                                                 $orgEvent['start_time'],
                                                 $reminders);
        }

        //
        // Update Invitation
        //

        //create a new invitation from non-invitation event
        if (isset($event['createNewInvitation'])) {
            $select = $this->select()->where("id = $eventId");
            $cloneEvent = $this->cloneRow($select);
            unset($cloneEvent['id']);
            $this->insertEventGuests($eventId, $cloneEvent, $event['guests']);
        }

        //update existing invitation guests
        if (isset($event['updateGuests'])) {
            $invitationEventId = $event['invitationEventId'];

            //hold guests existing reminders and insert them after inserting new invitations (updated invitations)
            if (isset($event['guests'])) {
                foreach ($event['guests'] as &$guest) {
                    //no reminders for outside invitees
                    if ($guest['username'] == 'spc-invitee-outside') {
                        continue;
                    }

                    $select = $this->select(array('time', 'type', 'time_unit', 'is_repeating_event'), 'spc_calendar_reminders')

                                   ->join('spc_calendar_events', 'spc_calendar_events.id = spc_calendar_reminders.event_id')
                                   ->join('spc_calendar_calendars', 'spc_calendar_calendars.id = spc_calendar_events.cal_id')

                                   ->where("spc_calendar_calendars.user_id = {$guest['user_id']}")
                                   ->where("spc_calendar_events.invitation_event_id = $invitationEventId");
                     $guest['reminders'] = $this->fetchAll($select);
                }
            }

            $this->delete("event_id = $invitationEventId", 'spc_calendar_guests');
            $this->delete("invitation_event_id = $invitationEventId");
            if (isset($event['guests'])) {
                $select = $this->select()->where("id = $invitationEventId");
                $cloneEvent = $this->cloneRow($select);
                unset($cloneEvent['id']);
                $this->insertEventGuests($invitationEventId, $cloneEvent, $event['guests']);
            }
        }

        //update invitation response
        if (isset($orgEvent['invitation_response'])) {
            $invitationEventId = $event['invitationEventId'];
            $invitationResponse = $orgEvent['invitation_response'];
            $this->update(
                array('response' => $invitationResponse),
                "event_id = $invitationEventId AND user_id = $userId",
                'spc_calendar_guests'
            );
        }

        //if event is an invitation send notification to the guests
        if ($orgEvent['invitation'] == 1) {
            $this->notifyGuests($eventId, 'updated');
        }

        $this->commit();
    }

    public function cloneRow($select) {
        $row = $this->fetchRow($select);
        foreach ($row as &$field) {
            if ($field === null) {
                $field = '';
            }
        }

        return $row;
    }

    /**
     * Drag event
     *
     * @param array $event
     */
    public function dragEvent($event) {
        $this->begin();
        //update event
        $eventId = $event['id'];
        $where = "id = $eventId";
        if ($event['invitation'] == '1') {
            $where = "id = $eventId OR invitation_event_id = $eventId";
        }

        $eventType = 'standard';
        if ($event['startDate'] != $event['endDate']) {
            $eventType = 'multi_day';
        }

        $updateData = array(
            'type' => $eventType,
            'start_date' => $event['startDate'],
            'start_time' => $event['startTime'],
            'end_date' => $event['endDate'],
            'end_time' => $event['endTime'],
            'modified_by' => SPC_USERNAME,
            'updated_on' => date('Y-m-d H:i:s')
        );

        if (isset($event['calId'])) {
            $updateData['cal_id'] = $event['calId'];
        }

        //repeating event
        $orgEvent = $this->fetchRow($this->select()->where("id = {$eventId}"));
        if ($orgEvent['repeat_type'] != 'none') {
            $deletedIndexes = $orgEvent['repeat_deleted_indexes'];
            unset($orgEvent['id'], $orgEvent['repeat_data'], $orgEvent['repeat_deleted_indexes']);
            $orgEvent['repeat_type'] = 'none';
            $orgEvent = array_merge($orgEvent, $updateData);
            $this->insert($orgEvent);

            //update deleted indexes
            $repeatOrder = $_POST['spcUserData']['repeatOrder'];
            if (!$deletedIndexes) {
                $deletedIndexes = $repeatOrder;
            } else {
                $deletedIndexes .= ",{$repeatOrder}";
            }
            $this->update(array('repeat_deleted_indexes' => $deletedIndexes), "id = {$eventId}");
        } else {
            $this->update($updateData, $where);
        }

        //update event reminders
        $reminderModel = new Calendar_Model_Reminder;
        $reminderModel->updateEventReminders($eventId,
                                            $event['startDate'],
                                            $event['startTime']);

        if ($event['invitation'] == '1') {
            //update guests' invitation reminders
            $select = $this->select('id')
                           ->where("invitation_event_id = $eventId");

            foreach ($this->fetchAll($select) as $guestInvitationEvent) {
                $reminderModel->updateEventReminders($guestInvitationEvent['id'],
                                                    $event['startDate'],
                                                    $event['startTime']);
            }

            $this->notifyGuests($eventId, 'updated');
        }

        $this->commit();
    }

    /**
     * Resize event
     *
     * @param array $event
     */
    public function resizeEvent($event) {
        $eventId = $event['id'];
        $where = "id = {$eventId}";
        if ($event['invitation'] == '1') {
            $where = "id = {$event['id']} OR invitation_event_id = {$event['id']}";
            $this->notifyGuests($eventId, 'updated');
        }

        $eventType = 'standard';
        if ($event['startDate'] != $event['endDate']) {
            $eventType = 'multi_day';
        }

        $updateData = array(
            'type' => $eventType,
            'start_date' => $event['startDate'],
            'start_time' => $event['startTime'],
            'end_date' => $event['endDate'],
            'end_time' => $event['endTime'],
            'modified_by' => SPC_USERNAME,
            'updated_on' => date('Y-m-d H:i:s')
        );

        //repeating event
        $orgEvent = $this->fetchRow($this->select()->where("id = {$eventId}"));
        if ($orgEvent['repeat_type'] != 'none') {
            $deletedIndexes = $orgEvent['repeat_deleted_indexes'];

            unset($orgEvent['id'], $orgEvent['repeat_data'], $orgEvent['repeat_deleted_indexes']);
            $orgEvent['repeat_type'] = 'none';
            $orgEvent = array_merge($orgEvent, $updateData);
            $this->insert($orgEvent);

            //update deleted indexes
            $repeatOrder = $_POST['spcUserData']['repeatOrder'];
            if (!$deletedIndexes) {
                $deletedIndexes = $repeatOrder;
            } else {
                $deletedIndexes .= ",{$repeatOrder}";
            }
            $this->update(array('repeat_deleted_indexes' => $deletedIndexes), "id = {$eventId}");
        } else {
            $this->update($updateData, $where);
        }
    }

    /**
     * Gets event invitation guest list
     *
     * @param int $eventId
     * @return array
     */
    public function getGuests($eventId) {
        $select = $this->select()
                        ->from('spc_calendar_guests')
                        ->where("spc_calendar_guests.event_id = $eventId");

        return $this->fetchAll($select);
    }

    /**
     * Sends invitation email to the guests
     * Helper method for create, update and delete event methods
     *
     * @param array $event
     * @param array $guests
     * @param string $status
     */
    public function notifyGuests($eventId, $status = 'new') {
        if (!isset($_POST['sendInvitations'])) {
            return;
        }

        $event = $this->getEvent($eventId);
        $guests = $this->getGuests($eventId);
        $guestList = array();
        foreach ($guests as $guest) {
            $guestList[] = $guest['email'];
        }

        $event['guestList'] = join(', ', $guestList);

        foreach ($guests as $guest) {
            $this->sendInvitationEmail($event, $guest, $status);
        }
    }

    /**
     * Sends invitation email to a guest
     *
     * @param array $event
     * @param array $guest
     * @param string $status (new, updated, canceled)
     */
    public function sendInvitationEmail($event, $guest, $status = 'new') {
        $guestId = $guest['id'];
        $guestEmail = $guest['email'];

        $title = $event['title'] ? $event['title'] : Spc::translate('(No title)');
        $location = $event['location'];
        $description = $event['description'];

        $finalDate = $this->_dateHelper->getPublicEventDateTitle($event) . ' (' . Spc::getUserPref('timezone') . ')';

        $subject = 'Event Invitation';
        $guestList = $event['guestList'];

        $digest = md5("spc-invitation-digest: {$guestEmail}");
        $acceptUri = SPC_ROOT . "/accept.php?id={$guestId}&digest={$digest}&response=";
        $goingButtons = "<div style='padding: 10px; background-color: #F0F8FD; border: 1px solid #DCEEF8;'>
                            <h4>" . Spc::translate('going') . "?</h4>
                            <a target='_blank' href='{$acceptUri}yes'>" . Spc::translate('yes') . "</a> |
                            <a target='_blank' href='{$acceptUri}no'>" . Spc::translate('no') . "</a> |
                            <a target='_blank' href='{$acceptUri}maybe'>" . Spc::translate('maybe') . "</a>
                        </div>";

        if ($status == 'updated') {
            $subject = $subject . ' ( ' . Spc::translate('updated') . ' )';
        }

        if ($status == 'canceled') {
            $subject = $subject . ' (' . Spc::translate('canceled') . ' )';
            $goingButtons = "<div style='padding: 10px; background-color: #F0F8FD; border: 1px solid #DCEEF8;'>
                                <h4>" . Spc::translate('canceled') . "</h4>
                            </div>";
        }

        $ownerName = SPC_USERNAME . ' ' . SPC_USER_EMAIL;

        $msg = "<div style='padding: 10px; border: 1px solid #eee; font-size: 12px; font-family: Arial, sans-serif, Verdana; color: #333;'>
                    <h4 style='padding: 10px; background-color: #FEF5C9; border: 1px solid #E8D15C; font-size: 13px;'>
                        {$ownerName} " . Spc::translate('invites you') . "
                    </h4>
                    {$goingButtons}
                    <table style='width: 100%; border-collapse: collapse;' cellpadding='10'>
                        <tbody>
                            <tr>
                                <td style='width: 100px; border: 1px solid #eee;'><strong>" . Spc::translate('title') . "</strong></td>
                                <td style='border: 1px solid #eee;'>{$title}</td>
                            </tr>
                            <tr>
                                <td style='width: 100px; border: 1px solid #eee;'><strong>" . Spc::translate('date') . "</strong></td>
                                <td style='border: 1px solid #eee;'>{$finalDate}</td>
                            </tr>
                            <tr>
                                <td style='width: 100px; border: 1px solid #eee;'><strong>" . Spc::translate('location') . "</strong></td>
                                <td style='border: 1px solid #eee;'>{$location}</td>
                            </tr>
                            <tr>
                                <td style='width: 100px; border: 1px solid #eee;'><strong>" . Spc::translate('description') . "</strong></td>
                                <td style='border: 1px solid #eee;'>{$description}</td>
                            </tr>
                            <tr>
                                <td style='width: 100px; border: 1px solid #eee;'><strong>" . Spc::translate('guests') . "</strong></td>
                                <td style='border: 1px solid #eee;'>{$guestList}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>";

        //create and send an iCalendar file (temporary)
        $iCalModel = new Calendar_Controller_Ical();
        $iCalModel->exportCalendar(NULL, true);
        $iCalFileStr = SpcCalendar::export('exportEvent', array('eventId' => $event['id']));
        $tmpICalFileName = SPC_USERNAME . '_invitation_' . time() . '.ics';
        $tmpICalFilePath = SPC_SYSPATH . '/' . $tmpICalFileName;
        $handle = fopen($tmpICalFilePath, 'w+');
        fwrite($handle, $iCalFileStr);
        fclose($handle);

        $attachment = array(
            'path' => $tmpICalFilePath,
            'unlink' => true,
            'mime' => 'text/calendar'
        );

        $this->_mailer->send(SPC_USER_EMAIL, $guestEmail, $subject, $msg, '', $attachment);
    }

    /**
     * Inserts event guests
     * Also inserts invitation event to each guest's default calendar
     *
     * @param int $eventId (original invitation event id)
     * @param array $event (original invitation event clone)
     * @param array $guests
     */
    public function insertEventGuests($eventId, $event, $guests) {
        #$event['org_invitation_event_id'] = $eventId;
        $coreUserModel = new Core_Model_User;
        $calModel = new Calendar_Model_Calendar;
        $reminderModel = new Calendar_Model_Reminder;
        $defaultInvitationReminders = array(
            array('type' => 'email',
                  'time' => 1,
				  'time_unit' => 'day',
                  'is_repeating_event' => '0'),

            array('type' => 'email',
                  'time' => 2,
				  'time_unit' => 'hour',
                  'is_repeating_event' => '0'),

            array('type' => 'popup',
                  'time' => 1,
				  'time_unit' => 'day',
                  'is_repeating_event' => '0'),

            array('type' => 'popup',
                  'time' => 2,
				  'time_unit' => 'hour',
                  'is_repeating_event' => '0')
        );

        //prepare invitation events and send invitation emails to invitees
        $guestList = array();
        $guestsEvents = array();
        foreach ($guests as &$guest) {
            $guestList[] = $guest['email'];
            $guest['event_id'] = $eventId;

            //if user is not a SPC user don't add invitation-event
            if ($guest['username'] == 'spc-invitee-outside') {
                $guest['user_id'] = 0;
                continue;
            }

            //original invitation event id, saved to update invitation later
            $event['invitation_event_id'] = $eventId;
            $event['cal_id'] = $calModel->getDefaultCal($guest['user_id']);
            $event['invitation_response'] = $guest['response'];

            $event['username'] = $guest['username'];

            //invitation creator's event was already inserted
            if ($guest['user_id'] != $event['invitation_creator_id']) {
                if (!isset($guest['reminders'])) {
                    $guest['reminders'] = $defaultInvitationReminders;
                }
                $event['reminders'] = $guest['reminders'];
                $guestsEvents[] = $event;
            }
            unset($guest['reminders']);
        }

        foreach ($guestsEvents as $event) {
            //if user is not a SPC user don't add invitation-event
            if ($event['username'] == 'spc-invitee-outside') {
                continue;
            }

            $reminders = $event['reminders'];
            unset($event['reminders'], $event['username']);
            //add invitation event for invitees (all user's default calendars)
            $guestInvitationEventId = $this->insert($event);
            $reminderModel->insertEventReminders($guestInvitationEventId,
                                                 $event['start_date'],
                                                 $event['start_time'],
                                                 $reminders);
        }

        $event['id'] = $eventId;
        $event['guestList'] = implode(', ', $guestList);

        foreach ($guests as &$guest) {
            $guest['id'] = $this->insert($guest, 'spc_calendar_guests');
            if (isset($_POST['sendInvitations'])) {
                $this->sendInvitationEmail($event, $guest);
            }
        }
    }

    /**
     * Updates invitee's invitation response (via public invitation page sent with email)
     *
     * @param int $invitationId
     * @param string $response
     */
    public function updateInvitationResponse($invitationId, $response) {
        $this->update(
            array('response' => $response),
            "id = {$invitationId}",
            'spc_calendar_guests'
        );
    }

    /**
     * Deletes event image
     *
     * @param string $image
     */
    public function deleteImage($image) {
        $userImageFolderPath = SPC_SYSPATH . '/apps/calendar/files/user-images/' . SPC_USERNAME;
        unlink($userImageFolderPath . "/org/$image");
        unlink($userImageFolderPath . "/thumb/$image");
    }

    /**
     * Gets user's event thumb images
     */
    public function getUserThumbs() {
        $username = SPC_USERNAME;
		$thumbPath = SPC_SYSPATH . "/apps/calendar/files/user-images/$username/thumb";
		$dirIt = new DirectoryIterator($thumbPath);

		$thumbs = array();
		foreach ($dirIt as $fileInfo) {
			if ($fileInfo->isFile()) {
				$thumbs[] = $fileInfo->getFilename();
			}
		}

        return $thumbs;
	}


    //--------------------------------------------------------------------------
    // Event Repeat Engine
    //--------------------------------------------------------------------------

    public function getEventType($event) {
        $eventType = "standard";
        if (($event['start_date'] == $event['end_date'])
                && ($event['start_time'] == "00:00")
                && ($event['end_time'] == "00:00")) {

            $eventType = "all_day";
        }

        if (($event['start_date'] != $event['end_date'])) {
            $eventType = "multi_day";
        }

        return $eventType;
    }

    public function pushRepeatingEvent($repeatEvent, $eventType, &$events, $multiDayEventSpan) {
        //add generated event
        $repeatEventStartDate = $repeatEvent['start_date'];
        if ($eventType != 'multi_day') {
            $events[$eventType][$repeatEventStartDate][] = $repeatEvent;
            $events['all'][$repeatEventStartDate][] = $repeatEvent;
        } else {
            $events['multi_day'][] = $repeatEvent;
            //split multi-day events by days and add them to all events
            $repeatEventStartDateTs = strtotime($repeatEventStartDate);
            for ($j = 0; $j <= $multiDayEventSpan; $j++) {
                $k = date('Y-m-d', strtotime("+$j days", $repeatEventStartDateTs));
                $events['all'][$k][] = $repeatEvent;
            }
        }
    }

    public function getDayOfMonthByWeek ($startDateOfMonth, $weekIndex, $dayIndex) {
        for ($i = 0, $j = 0; $i < 32; $i++) {
            if (date('w', strtotime("+$i days", $startDateOfMonth)) == $dayIndex) {
                $j++;
            }

            if ($j == $weekIndex) {
                break;
            }
        }

        return $i + 1;
    }

    public function getMultiDayEventSpan($startDate, $endDate) {
        $startDateTs = strtotime($startDate);
        $endDateTs = strtotime($endDate);

        return (($endDateTs - $startDateTs) / (24 * 60 * 60));
    }

    public function addRepeatEvents($repeatEvent, &$events, $rangeStartDate, $rangeEndDate) {
        //repeat type (daily, weekly, monthly, yearly)
        $repeatType = $repeatEvent['repeat_type'];

        //repeat start date
        $repeatStartDate = $repeatEvent['start_date'];
        $repeatStartDateTs = strtotime($repeatStartDate);
        list($y, $m, $d) = explode('-', $repeatStartDate);
        $repeatStartParsedDate['year'] = (int)$y;
        $repeatStartParsedDate['month'] = (int)$m;
        $repeatStartParsedDate['day'] = (int)$d;
        #$repeatStartParsedDate = SPC.Date.parseDate(repeatStartDate),
        //repeat- end date
        $repeatEndDate = $repeatEvent['repeat_end_date'];

        //special repeat data
        $repeatData = $repeatEvent['repeat_data'];

        //repeating event type (standard, all_day, multi_day)
        $eventType = $this->getEventType($repeatEvent);

        //repeat-interval (repeat every 3 days, 3 weeks, etc)
        $repeatInterval = (int)$repeatEvent['repeat_interval'];

        //repeat engine loop counter
        $inc = ($repeatType == 'weekly') ? 1 : $repeatInterval;

        //repeat-count
        $eventRepeatCount = (int)$repeatEvent['repeat_count'];

        //repeating event day count if multi-day
        $multiDayEventSpan = ($eventType == "multi_day")
                          ? $this->getMultiDayEventSpan($repeatStartDate, $repeatEvent['end_date'])
                          : 0;
        //repeat-special-data
        $weeklyRepeatingDays = ($repeatType == 'weekly')
                             ? explode(',', $repeatData)
                             : null;

        //monthly-special-data
        $monthlyRepeatData = ($repeatType == 'monthly')
                           ? explode(',', $repeatData)
                           : null;
        $i = 0; //!TODO start from a closest date to rangeStartDate??

        //week-day index for generated event's start-date 0: Sunday, 1: Monday, ...
        $startDayIndex;

        //repeating event counter
        $repeatCount = 0;

        $repeatMonthCounter;
        $repeatMonthDay;
        $repeatYearCounter;
        $repeatYearDay;
        $endDayOfWeekIndex = SpcCalendar::getEndDayOfWeekIndex();

        while (true) {
            //check repeat count
            if (($eventRepeatCount != 0)
                    && ($repeatCount == $eventRepeatCount)) {

                break;
            }

            //
            //generate new start date for generated repeating event
            //
            //daily or weekly repeating
            if (($repeatType == 'daily') || ($repeatType == "weekly")) {
                $repeatEvent['start_date'] = date('Y-m-d', strtotime("+$i days", $repeatStartDateTs));
            }

            //monthly-repeating
            if ($repeatType == 'monthly') {
                $repeatMonthCounter = ($repeatStartParsedDate['month'] + $i);
                $repeatMonthDay = $repeatStartParsedDate['day'];
                if ($repeatData) {
                    $repeatMonthDay = $this->getDayOfMonthByWeek(
                        mktime(0, 0, 0, $repeatMonthCounter, 1, $repeatStartParsedDate['year']),
                        (int)$monthlyRepeatData[0],
                        (int)$monthlyRepeatData[1]
                    );
                }

                $repeatEvent['start_date'] = date(
                    'Y-m-d',
                    mktime(0, 0, 0,
                           $repeatMonthCounter,
                           $repeatMonthDay,
                           $repeatStartParsedDate['year'])
                );
            }

            //yearly-repeating
            if ($repeatType == 'yearly') {
                $repeatEvent['start_date'] = date('Y-m-d',
                                                  mktime(0, 0, 0,
                                                         $repeatStartParsedDate['month'],
                                                         $repeatStartParsedDate['day'],
                                                         ($repeatStartParsedDate['year'] + $i)));
            }

            $i += $inc;

            if ($repeatType == 'weekly') {
                //week-day index 0: sunday, 1: monday, ...
                $startDayIndex = date('w', strtotime($repeatEvent['start_date']));
                if ($startDayIndex == $endDayOfWeekIndex) {
                    $i += ($repeatInterval - 1) * 7;
                }
            }

            //check start-date is in weekly repeating dates
            if ($repeatType == 'weekly') {
                if (in_array($startDayIndex, $weeklyRepeatingDays) === false) {
                    continue;
                }
            }

            $repeatCount++;

            //generate new end date for generated repeating event
            if (($eventType == "standard")
                    || ($eventType == "all_day")) {

                $repeatEvent['end_date'] = $repeatEvent['start_date'];
            } else if ($eventType == 'multi_day') {
                $repeatEvent['end_date'] = date('Y-m-d', strtotime("+$multiDayEventSpan days", strtotime($repeatEvent['start_date'])));
            }

            //!out of spc-cal-view range
            if ($repeatEvent['start_date'] < $rangeStartDate) {
                 // if there is a multi-day event started before range and continues after range add it
                if ($eventType == "multi_day") {
                    if ($repeatEvent['end_date'] >= $rangeStartDate) {
                        $this->pushRepeatingEvent($repeatEvent, $eventType, $events, $multiDayEventSpan);
                    }
                }

                continue;
            }

            //out of spc-cal-view range or repeat-end-date
            if (($repeatEvent['start_date'] > $repeatEndDate)
                    || ($repeatEvent['start_date'] > $rangeEndDate)) {

                break;
            }

            //push event
            $this->pushRepeatingEvent($repeatEvent, $eventType, $events, $multiDayEventSpan);
        }
    }
}