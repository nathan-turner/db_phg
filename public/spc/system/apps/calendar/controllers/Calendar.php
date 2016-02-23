<?php

class Calendar_Controller_Calendar extends SpcController {

    /**
     * Calendar Calendar Model
     *
     * @var type
     */
    public $calendar;

    /**
     * Constructor
     *
     * Inits db and calendar model
     */
    public function __construct() {
        parent::__construct();
        $this->calendar = new Calendar_Model_Calendar();
    }

    public function createCalendar($calendar) {
        $this->calendar->createCalendar($calendar);
    }

    public function updateCalendar($calendar) {
        $this->calendar->updateCalendar($calendar);
    }

    public function deleteCalendar($calId, $owner, $type) {
        $this->calendar->deleteCalendar($calId, $owner, $type);
    }

    public function getCalendar($calId) {
        return $this->calendar->getCalendar($calId);
    }

    public function shareCalendar($calendar) {
        $this->calendar->shareCalendar($calendar);
    }

    public function deleteSharedCalendar($sharedCalendarId) {
         $this->calendar->delete("id = $sharedCalendarId", 'spc_calendar_shared_calendars');
    }

    public function getCalendarShareList($calendarId) {
        //get new shared users list
		$sql = "SELECT
					spc_calendar_shared_calendars.*, spc_users.username
				FROM
					spc_calendar_shared_calendars
				INNER JOIN
					spc_users ON spc_calendar_shared_calendars.shared_user_id = spc_users.id
				WHERE
					spc_calendar_shared_calendars.cal_id = $calendarId";

        $rs = $this->_db->query($sql);
		$sharedUsers = $this->_db->fetchAll($sql);

        echo Spc::jsonEncode(array('sharedUsers' => $sharedUsers));
    }

    public function changeSharedCalendarPermission($options) {
		$sharedCalendarId = $options['sharedCalendarId'];
		$permission = $options['permission'];

		$sql = "UPDATE spc_calendar_shared_calendars SET permission='$permission' WHERE id=$sharedCalendarId";
        $this->_db->query($sql);
	}

    public function changeCalendarPublicStatus($calendar) {
		$calendarId = $calendar['calendarId'];
		$status 	= $calendar['status'];

		$sql = "UPDATE spc_calendar_calendars SET public = '$status' WHERE id = $calendarId";
        $this->_db->query($sql);

		echo '{"success":true}';
	}

   public function changeCalendarListStatus($calendar) {
		$calendarId = $calendar['calendarId'];
		$owner 		= $calendar['owner'];
		$status 	= $calendar['status'];
		$showEvents = ($status == '1') ? 'on' : 'off';

		$sql = "UPDATE
					spc_calendar_shared_calendars
				SET
					show_in_list = '$status',
					status = '$showEvents'
				WHERE
					cal_id = $calendarId";

		if ($calendar["owner"] == "self") {
			$sql = "UPDATE
						spc_calendar_calendars
					SET
						show_in_list = '$status',
						status = '$showEvents'
					WHERE
						id = $calendarId";
		}

        $this->_db->query($sql);

		SpcCalendar::initUserCalendars();
	}

    /**
     * Gets users calendars (self, shared, group)
     */
    public function getUserCalendars($userId = null) {
        $cals = array();
        if ($userId) {
            $cals = $this->calendar->getUserCals($userId, true);
        } else {
            $cals = $_SESSION['calendars'];
        }

        echo Spc::jsonEncode(array('userCalendars' => $cals));
    }

    public function setCalendarStatus($calId, $status, $shared, $userCal = false) {
        if (Spc::getSender() == 'public') {
            return;
        }

        $db = new SpcDb();

        if ($userCal) {
			$adminId = SPC_USERID;

			if ($status == 'on') {
				$sql = "REPLACE INTO
							spc_calendar_admin_user_cals
						VALUES
							($adminId, $calId)";

				$db->query($sql);
			} else {
				$sql = "DELETE FROM
							spc_calendar_admin_user_cals
						WHERE
							admin_id = $adminId AND cal_id = $calId";

				$db->query($sql);
			}

			$_SESSION['activeUserCalendars'] = SpcCalendar::getActiveUserCals($adminId);
            return;
		}

        $userId = SPC_USERID;
		$table = $shared ? 'spc_calendar_shared_calendars' : 'spc_calendar_calendars';
		$where = $shared ? "shared_user_id = {$userId} AND cal_id = {$calId}" : "id = $calId";

		$sql = "UPDATE $table SET status = '$status' WHERE $where";

		$db->query($sql);
    }

    public function initCalReminders() {
        $this->calendar->initCalReminders();
    }

    /**
	 * Inits and Gets User Calendars
	 * Used for drawing calendar left sidebar My Calendars and Other Calendars
	 *
	 * @param void
	 * @return string JSON
	 */
	public function getCalendars($getUsersCalendars = false) {
        if (SPC_USER_ROLE === 'admin' && Spc::getSender() === 'private') {
            $this->calendar->updateStaffMode($getUsersCalendars);
        }

		SpcCalendar::initUserCalendars((int)$getUsersCalendars);
        echo Spc::jsonEncode(array('calendars' => $_SESSION['calendars']));
	}

    /**
     * Get all users with their calendar belong to an Administrator
     * Gets all users (with admins and their users) if user-role is super
     *
     * @param void
     * @return string
     */
    public function getUsersCalendars() {
        $userId = SPC_USERID;
		$role = SPC_USER_ROLE;
		$adminId = SPC_ADMINID;

        $db = new SpcDb();
        $activeUserCalendars = SpcCalendar::getActiveUserCals($adminId);

		//get this admin's users' spc_calendar_calendars
		if ($role == 'admin') {
			$sql = "SELECT
						spc_users.id AS user_id,
						spc_users.username,
						spc_calendar_calendars.id AS cal_id,
						spc_calendar_calendars.name AS cal_name,
						spc_calendar_calendars.color,
						spc_calendar_calendars.status
					FROM
						spc_users
					LEFT JOIN
						spc_calendar_calendars ON spc_users.id = spc_calendar_calendars.user_id
					WHERE
						spc_users.admin_id = $userId
                        AND
                        spc_users.role = 'user'
					ORDER BY
						spc_users.username, spc_calendar_calendars.name";

			$rs = $db->query($sql);

			$users = array();
			while ($row = mysql_fetch_assoc($rs)) {
				$row['status'] = 'off';
				if (in_array($row['cal_id'], $activeUserCalendars)) {
					$row['status'] = 'on';
				}

				$users[$row['username']][] = $row;
			}

			echo Spc::jsonEncode(array('users' => $users));

		} else if ($role == 'super') {
			//get admins
			$sql = "SELECT
						spc_users.id,
						spc_users.username,
						spc_calendar_calendars.id AS cal_id,
						spc_calendar_calendars.name AS cal_name,
						spc_calendar_calendars.color,
						spc_calendar_calendars.status
					FROM
						spc_users
					LEFT JOIN
						spc_calendar_calendars ON spc_users.id = spc_calendar_calendars.user_id
					WHERE
						spc_users.role = 'admin'
					ORDER BY
						spc_users.username, spc_calendar_calendars.name";

			$rs = $db->query($sql);

			$admins = array();
			while ($row = mysql_fetch_assoc($rs)) {
				$row['status'] = 'off';
				if (in_array($row['cal_id'], $activeUserCalendars)) {
					$row['status'] = 'on';
				}

				$admins[$row['id']]['calendars'][] = $row;
				$admins[$row['id']]['username'] = $row['username'];
			}

			//add users
			$sql = "SELECT
						spc_users.id AS user_id,
						spc_users.admin_id,
						spc_users.username,
						spc_calendar_calendars.id AS cal_id,
						spc_calendar_calendars.name AS cal_name,
						spc_calendar_calendars.color,
						spc_calendar_calendars.status
					FROM
						spc_users
					LEFT JOIN
						spc_calendar_calendars ON spc_users.id = spc_calendar_calendars.user_id
					WHERE
						spc_users.role = 'user'
					ORDER BY
						spc_users.username, spc_calendar_calendars.name";

			$rs = $db->query($sql);

			while ($row = mysql_fetch_assoc($rs)) {
				$row['status'] = 'off';
				if (in_array($row['cal_id'], $activeUserCalendars)) {
					$row['status'] = 'on';
				}

				$admins[$row['admin_id']]['users'][$row['username']][] = $row;
			}

			echo Spc::jsonEncode(array('admins' => $admins));

		} else {
			echo Spc::jsonEncode(array('users' => '{}'));
		}
    }

    public function refreshSubscribedCal($calId = null) {
        $this->calendar->refreshSubscribedCal($calId);
    }
}