<?php

class Calendar_Model_Calendar extends SpcDbTable {

    protected $_name = 'calendar_calendars';

    /**
     * Gets calendar by id
     * 
     * @param type $calId
     * @return type
     */
    public function getCalendar($calId) {
        $select = $this->select()->where("id = {$calId}");
        return $this->fetchRow($select);
    }

    public function createCalendar($calendar) {
        $userId 		= SPC_USERID;
		$adminId 		= SPC_ADMINID;

        $type 			= $calendar['type'];
		$name 			= $calendar['name'];
		$description	= $calendar['description'];
		$color 			= $calendar['color'] ? $calendar['color'] : SpcCalendar::getColors(false, true);
        $createDate     = date('Y-m-d H:i:s');

        //ical subscribe access key
        $accessKey = md5($userId . microtime(true));

		$sql = "INSERT INTO
					spc_calendar_calendars
				VALUES
					(NULL,
					'$type',
					$userId,
					'$name',
					'$description',
					'$color',
					$adminId,
					'on',
					'1',
					'0',
					'%calendar% - %start-date% - %start-time%, %title%',
					'%calendar% - %start-date% - %start-time%, %title%',
                    '$accessKey',
                    '$createDate',
                     NULL)";

		$rs = $this->query($sql);

		$insertCalId = mysql_insert_id();

        //if group calendar share it with all group users
		if ($type == 'group') {
			$sql = "SELECT id FROM spc_users WHERE admin_id = $userId";
			$rs = $this->query($sql);

			while (list($sharedUserId) = mysql_fetch_row($rs)) {
				$sql = "INSERT INTO
							spc_calendar_shared_calendars
						VALUES
							(NULL, 'group', $userId, $insertCalId, $sharedUserId, 'see', '$name', '$description', '$color', 'on', '1', '', '{$createDate}', NULL)";

				$this->query($sql);
			}
		}

        if ($type == 'url') {
            $url = $calendar['url'];

            $sql = "INSERT INTO
						spc_calendar_shared_calendars
                    VALUES
                        (NULL, 'url', $userId, $insertCalId, $userId, 'see', '$name', '$description', '$color', 'on', '1', '{$url}', '{$createDate}', '{$createDate}')";

            $this->query($sql);

            //import calendar
            $iCalModel = new Calendar_Controller_Ical();
            $iCalModel->import($insertCalId, $url);
        }

		SpcCalendar::initUserCalendars();
    }

    public function updateCalendar($calendar) {
        $userId = SPC_USERID;

		$calendarId 	= $calendar['calendarId'];
		$name 			= $calendar['name'];
		$description	= $calendar['description'];
		$color 			= $calendar['color'];
        $updateDate = date('Y-m-d H:i:s');

		$sql = "UPDATE
					spc_calendar_shared_calendars
				SET
					name='$name',
					description='$description',
					color='$color',
                    updated_on = '$updateDate'
				WHERE
					shared_user_id = $userId
					AND
					cal_id = $calendarId";

		if ($calendar["owner"] == "self") {
			$sql = "UPDATE
						spc_calendar_calendars
					SET
						name='$name',
						description='$description',
						color='$color',
                        updated_on = '$updateDate'
					WHERE
						id = $calendarId";
		}

		$this->query($sql);

		SpcCalendar::initUserCalendars();
    }

    /**
     * check if the calendar is default calendar if so do not delete,
     * delete all events belong to this calendar
     */
    public function deleteCalendar($calId, $owner, $type) {

        $select = $this->select(array('id'))
                       ->where("user_id = $this->_userId");
        $defaultCalendarId = $this->fetchColumn($select);

		if ($calId == $defaultCalendarId) {
            $this->delete("cal_id = $calId", 'spc_calendar_events');
            return;
		}

        //delete user calendar
		if ($owner == 'self') {
            $this->delete("id = $calId");

        //delete shared calendar
		} else if ($owner == 'other') {
            $this->delete("shared_user_id = {$this->_userId} AND cal_id = $calId",
                          'spc_calendar_shared_calendars');
        }

        //delete group calendar (only group-managers can do)
        if (($owner == 'self') && ($type == 'group')) {
            $this->delete("cal_id = $calId",
                          'spc_calendar_shared_calendars');
        }

        //delete subscribed calendar
        //when a user subscribes a calendar by URL two calendars created
        //original in spc_calendar_calendars and shared in spc_calendar_shared_calendars
        if ($type == 'url') {
            $this->delete("id = {$calId}");
        }

		SpcCalendar::initUserCalendars();
    }

    public function shareCalendar($calendar) {
		$username 		= SPC_USERNAME;
		$ownerUserId 	= SPC_USERID;
		$calendarId 	= $calendar['calendarId'];
		$permission 	= $calendar['permission'];
		$name 			= $calendar['name'];
		$description 	= $calendar['description'];
		$color 			= SpcCalendar::getColors(false, true);

		//get shared user id
		$sharedUsername	= $calendar['sharedUsername'];

		if ($sharedUsername == $username) {
            throw new Exception('You cannot share this calendar with yourself.');
		}

        //check if there is a user shared with
        $select = $this->select(array('id'), 'spc_users')
                        ->where("username = '$sharedUsername'");
        $sharedUserId = $this->fetchColumn($select);
        if (!$sharedUserId) {
            throw new Exception('User account could not found!');
        }

		//check if this calendar already shared
        $select = $this->select(array('id'), 'spc_calendar_shared_calendars')
                        ->where("cal_id = $calendarId AND shared_user_id = $sharedUserId");

        if ($this->numRows($select)) {
            throw new Exception('This calendar have already been shared with this user!');
        }

        $sharedCalData = array(
            'type' => 'user',
            'user_id' => $ownerUserId,
            'cal_id' => $calendarId,
            'shared_user_id' => $sharedUserId,
            'permission' => $permission,
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'status' => 'on',
            'show_in_list' => '1'
        );

        $this->insert($sharedCalData, 'spc_calendar_shared_calendars');
	}

    /**
     * Gets user's default calendar information as associative array
     *
     * @param int $userId
     * @return array
     */
    public function getDefaultCal($userId, $withCalInfo = false) {
        if ($withCalInfo) {
            $select = $this->select()
                        ->where("user_id = $userId");
            return $this->fetchRow($select);
        }

        $select = $this->select(array('id'))
                        ->where("user_id = $userId");

        return $this->fetchColumn($select);
    }

    public function getEventBorderColor($eventColor) {
        list($r, $g, $b) 	= SpcCalendar::getRGB($eventColor);
        $r -= 65;
        $g -= 65;
        $b -= 65;
        return "rgb($r, $g, $b)";
    }

    /**
     * Gets user calendars (array of calendar ids or calId => calDataArray key value pairs)
     *
     * @param int $userId
     * @param bool $getCalInfo
     * @param bool $puclic
     * @param bool $getSelfCals
     *
     * @return array
     */
    public function getUserCals($userId, $getCalInfo = false, $public = false, $getSelfCals = false) {
		//own calendars
        $fields = 'id';
        if ($getCalInfo) {
            $fields = "*";
        }

        $select = $this->select($fields)
                       ->where("user_id = $userId")
                       ->order('name');

        //get only public calendars
        if ($public) {
            $select->where('public = 1');
        }

        $cals = array();
		foreach ($this->fetchAll($select) as $cal) {
            $cal['eventTimerangeColor'] = $this->getEventBorderColor($cal['color']);
            $cal['owner'] = 'self';
            if ($getCalInfo) {
                $cals[$cal['id']] = $cal;
            } else {
                $cals[] = $cal['id'];
            }
		}

        if ($public || $getSelfCals) {
            return $cals;
        }

		//shared calendars
        $select = $this->select($fields, 'spc_calendar_shared_calendars')
                       ->join('spc_calendar_calendars',
                              'spc_calendar_calendars.id = spc_calendar_shared_calendars.cal_id',
                              array('*', 'id AS cal_id'))

                       ->where("spc_calendar_shared_calendars.shared_user_id = $userId")
                       ->order('spc_calendar_shared_calendars.name');

        $sharedCals = $this->fetchAll($select);
		foreach ($sharedCals as $cal) {
            $cal['eventTimerangeColor'] = $this->getEventBorderColor($cal['color']);
            $cal['owner'] = 'other';
            if ($getCalInfo) {
                $cals[$cal['cal_id']] = $cal;
            } else {
                $cals[] = $cal['cal_id'];
            }
		}

		return $cals;
	}

    /**
     * Updates staff_mode (toggle '1' <=> '0')
     * Admins can view their own calendars or their users (staff) calendars
     * Admins need to set their staff_mode = '1' to see their staff calendars
     *
     * @param mixed (string) $staffMode ('0', '1')
     */
    public function updateStaffMode($staffMode) {
        $this->update(
            array('staff_mode' => $staffMode),
            "user_id = {$this->_userId}",
            'spc_calendar_settings'
        );

        //refresh session
        $_SESSION['spcUserPrefs']['calendar']['staff_mode'] = $staffMode;
    }

    /**
     * Refreshes (deletes all events belong to calendar and import it from its URL again)
     * subscribed iCalendars (with URL)
     *
     * (Refreshes all if no argument given)
     *
     * @param mixed $calId
     */
    public function refreshSubscribedCal($calId) {
        if (!$calId || $calId == 'daily-sync') {
            $select = $this->select(array('cal_id', 'url', 'updated_on'))
                            ->from('spc_calendar_shared_calendars')
                            ->where("user_id = {$this->_userId}")
                            ->where('type = "url"');
        } else {
            $select = $this->select(array('cal_id', 'url', 'updated_on'))
                            ->from('spc_calendar_shared_calendars')
                            ->where("cal_id = {$calId}")
                            ->where('type = "url"');
        }

        $calendars = $this->fetchAll($select);
        $icalModel = new Calendar_Model_Ical();
        $syncDate = date('Y-m-d H:i:s');
        $time = time();
        $oneDaySec = (24 * 60 * 60);

        foreach ($calendars as $cal) {
            if ($calId == 'daily-sync') {
                $lastSyncDate = $cal['updated_on'];
                if ($time - strtotime($lastSyncDate) < $oneDaySec) {
                    continue;
                }
            }

            $this->delete("cal_id = {$cal['cal_id']}", 'spc_calendar_events');
            $this->update(array(
                'updated_on' => $syncDate
            ), "cal_id = {$cal['cal_id']}", 'spc_calendar_shared_calendars');

            $icalModel->import($cal['cal_id'], $cal['url']);
        }
    }
}