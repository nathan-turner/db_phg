<?php

class SpcCalendar {

    /**
     * Calendar views
     *
     * @var array
     */
    private static $_views = array('day', 'agenda');

    /**
     * Shortdate formats
     *
     * @var type
     */
    private static $_shortdateFormats = array(
        'n/j/Y',
        'j/n/Y',
        'Y-n-j'
    );

    /**
     * Longdate formats
     *
     * @var array
     */
    private static $_longdateFormats = array(
        'lit_end' => '',
        'mid_end' => '',
        'big_end' => ''
    );

    /**
     * Calendar (event) Colors
     *
     * @var array
     */
    private static $_colors = array(
        '#25a338',
        '#7BD148',
        '#8cd734',
        '#9CC089',
        '#B3DC6C',
        '#D8F3C9',
        '#4986E7',
        '#47AFFF',
        '#9FC6E7',
        '#ACD1E9',
        '#c8e0f0',
        '#D6E4E1',
        '#B58AA5',
        '#CCA6AC',
        '#BDAEC6',
        '#CABDBF',
        '#B99AFF',
        '#9A9CFF',
        '#EFEBD6',
        '#FBE983',
        '#E7DF9C',
        '#C4C4A8',
        '#CCCC99',
        '#DEE79C',
        '#CABDBF',
        '#C1DAD6',
        '#85AAA5',
        '#94A2BE',
        '#DDDDDD',
        '#C2C2C2',
        '#CC0000',
        '#FA573C',
        '#FF7537',
        '#D06B64',
        '#AC725E',
        '#FFAD46',
    );

    /**
     * Weekdays
     *
     * @var array
     */
    private static $_weekDays = array('Sunday', 'Monday', 'Tuesday',
                                      'Wednesday', 'Thursday', 'Friday', 'Saturday');

    /**
     * Gets user's end day of week
     *
     * @return string
     */
    public static function getEndDayOfWeek() {
        $startDayOfWeek = Spc::getUserPref('start_day', 'calendar');

        $endDayOfWeek = 'Sunday';
        if ($startDayOfWeek == 'Sunday') {
            $endDayOfWeek = 'Saturday';
        }

        if ($startDayOfWeek == 'Saturday') {
            $endDayOfWeek = 'Friday';
        }

        return $endDayOfWeek;
    }

    public static function getEndDayOfWeekIndex() {
        $endDayOfWeek = self::getEndDayOfWeek();
        return array_search($endDayOfWeek, self::$_weekDays);
    }

    /**
     * Starts foreach with the $start and continues iterating from the beginning
     * if end of the document is reached
     *
     * @param array $arr
     * @param int $start
     * @return array
     */
    public static function circleArrayForeach($arr, $start) {
        $arrCount = count($arr);
        $firstSlice = array_slice($arr, $start, ($arrCount - $start), true);
        $secondSlice = array_slice($arr, 0, $start, true);

        $newArr = $firstSlice + $secondSlice;
        #$newArr = array_merge($firstSlice, $secondSlice);
        return $newArr;
	}

    /**
     * Gets Weekdays by $startDay
     *
     * Used for generating weekdays for repeat event dialog
     * e.g. M T W T F S S
     *
     * @param string $startDay
     * @return array
     */
    public static function getWeekDays() {
        $startDay = Spc::getUserPref('start_day', 'calendar');
        $startDayIndex = array_search($startDay, self::$_weekDays);
        return self::circleArrayForeach(self::$_weekDays, $startDayIndex);
    }

    /**
     * Calendar Application views (day, week, month, ...)
     *
     * @return array
     */
    public static function getCalendarViews() {
        return self::$_views;
    }

    /**
     * Gets user's short date formats
     *
     * @return array
     */
    public static function getShortdateFormats() {
        return self::$_shortdateFormats;
    }

    /**
     * Gets user's longdate formats
     *
     * @return array
     */
    public static function getLongdateFormats() {
        $monday = Spc::translate('Monday');
        $march = Spc::translate('March');

        self::$_longdateFormats['lit_end'] = $monday . ', ' . '1 ' . substr($march, 0, 3) . ' ' . date('Y');
        self::$_longdateFormats['mid_end'] = $monday . ', ' . substr($march, 0, 3) . ' 1, ' . date('Y');
        self::$_longdateFormats['big_end'] = date('Y') . '-' . substr($march, 0, 3) . '-1, ' . $monday;

        return self::$_longdateFormats;
    }

    /**
     * Calendar colors
     *
     * @param bool $getColorBoxes
     * @param bool $getRandomColor
     * @return mixed
     */
    public static function getColors($getColorBoxes = false, $getRandomColor = false) {
        if ($getColorBoxes) {
            $colorBoxes = '<ul class="spc-colorboxes">';
            foreach (self::$_colors as $color) {
                $colorBoxes .= '<li class="ui-corner-all" title="' . $color . '" style="background-color: ' . $color . '"></li>';
            }
            $colorBoxes .= '</ul>';

            return $colorBoxes;
        }

        if ($getRandomColor) {
            return self::$_colors[array_rand(self::$_colors)];
        }

        return self::$_colors;
    }

    public static function initUserCalendars($getUsersCalendars = false) {
        $userId = SPC_USERID;
		$userRole = SPC_USER_ROLE;

        $calCtrl = new Calendar_Controller_Calendar();

        //user's all calendars (own, shared, group)
		$_SESSION['calendars'] = array();

        //user's own calendars
        $_SESSION['spcUserCalendars'] = array();

        //user's shared calendars (other users share their calendars with this user)
        $_SESSION['spcUserSharedCalendars'] = array();

        //user's group calendars
        $_SESSION['spcUserGroupCalendars'] = array();

        //admin's users' active calendars
		$_SESSION['activeCalendars'] = array();

		if (($userRole == 'super') || ($userRole == 'admin')) {
			$_SESSION['activeUserCalendars'] = self::getActiveUserCals($userId);
		}

        //
		// get user calendars
        //
		$sql = "SELECT
					spc_calendar_calendars.*, spc_users.username
				FROM
					spc_calendar_calendars
				INNER JOIN
					spc_users ON spc_calendar_calendars.user_id = spc_users.id
				WHERE
					spc_calendar_calendars.user_id = $userId
				ORDER BY
					spc_calendar_calendars.name";

		if ($userRole == 'super') {
			$sql = "SELECT
					spc_calendar_calendars.*, spc_users.username
				FROM
					spc_calendar_calendars
				INNER JOIN
					spc_users ON spc_calendar_calendars.user_id = spc_users.id
				ORDER BY
					spc_calendar_calendars.name";
		}

		if ($userRole == 'admin') {
            $sql = "SELECT
                        spc_calendar_calendars.*, spc_users.username
                    FROM
                        spc_calendar_calendars
                    INNER JOIN
                        spc_users ON spc_calendar_calendars.user_id = spc_users.id
                    WHERE
                        spc_calendar_calendars.admin_id = $userId OR spc_calendar_calendars.user_id = $userId
                    ORDER BY
                        spc_calendar_calendars.name";

            //staff-mode enabled
            if ($getUsersCalendars) {
                $sql = "SELECT
                            spc_calendar_calendars.*, spc_users.username
                        FROM
                            spc_calendar_calendars
                        INNER JOIN
                            spc_users ON spc_calendar_calendars.user_id = spc_users.id
                        WHERE
                            spc_calendar_calendars.admin_id = $userId
                        GROUP BY spc_calendar_calendars.user_id
                        ORDER BY
                            spc_calendar_calendars.name";
            }
		}

		if (!$rs = mysql_query($sql)) {
			return '{"success":false, "errorMsg":"' . mysql_error() . '"}';
		}

		while ($cal = mysql_fetch_assoc($rs)) {
			$eventColor 		= $cal['color'];
			list($r, $g, $b) 	= self::getRGB($eventColor);
			$r -= 65;
			$g -= 65;
			$b -= 65;
			$eventTimerangeColor = "rgb($r, $g, $b)";

			$calId = $cal['id'];

            //admins see their users (staff) default calendars with their usernames
            if ($getUsersCalendars) {
                $cal['name'] = $cal['username'];
            }

			$_SESSION['calendars'][$calId] = $cal;
			$_SESSION['calendars'][$calId]['eventTimerangeColor'] = $eventTimerangeColor;
			$_SESSION['calendars'][$calId]['permission'] = 'change';

			//admin and super users can see other users calendars
			$owner = 'self';
			if ($cal['user_id'] != $userId && !$getUsersCalendars) {
				$owner = 'user';
			}

			$_SESSION['calendars'][$calId]['owner'] = $owner;
			if ($cal['type'] == 'group') {
				$_SESSION['calendars'][$calId]['owner'] = 'group';
                $_SESSION['spcUserGroupCalendars'][$calId] = $cal;
			}

            if ($owner == 'self') {
                $_SESSION['spcUserCalendars'][$calId] = $cal;
            }
		}

        //
		// get shared calendars
        //
		$sql = "SELECT
					spc_calendar_shared_calendars.*, spc_users.username, spc_users.email AS owner_email
				FROM
					spc_calendar_shared_calendars
				INNER JOIN
					spc_users ON spc_calendar_shared_calendars.user_id = spc_users.id
				WHERE
					spc_calendar_shared_calendars.shared_user_id = $userId
				ORDER BY
					spc_calendar_shared_calendars.name";

		if ( ! $rs = mysql_query($sql)) {
			return '{"success":false, "errorMsg":"' . mysql_error() . '"}';
		}

		if (mysql_num_rows($rs) != 0) {
			while ($cal = mysql_fetch_assoc($rs)) {
				$cal['id'] = $cal['cal_id'];

				$eventColor 		= $cal['color'];
				list($r, $g, $b) 	= self::getRGB($eventColor);
				$r -= 65;
				$g -= 65;
				$b -= 65;
				$eventTimerangeColor = "rgb($r, $g, $b)";

				$sharedCalId = $cal['cal_id'];

				$_SESSION['calendars'][$sharedCalId] = $cal;
				$_SESSION['calendars'][$sharedCalId]['eventTimerangeColor'] = $eventTimerangeColor;

				$_SESSION['calendars'][$sharedCalId]['owner'] = 'other';
				if ($cal['type'] == 'group') {
					$_SESSION['calendars'][$sharedCalId]['owner'] = 'group';
                    $_SESSION['spcUserGroupCalendars'][$cal['id']] = $cal;

                    //get the admin's cal permission for group cal public property
                    $orgCal = $calCtrl->getCalendar($cal['id']);
                    $_SESSION['calendars'][$sharedCalId]['public'] = $orgCal['public'];
				} else {
                    //shared cals are never public
                    $_SESSION['spcUserSharedCalendars'][$cal['id']] = $cal;
                    $_SESSION['calendars'][$sharedCalId]['public'] = 0;
                }
			}
		}
	}

    public static function getUserCals($type = 'personal') {
        switch ($type) {
            case 'personal':
                return $_SESSION['spcUserCalendars'];
            case 'shared':
                return $_SESSION['spcUserSharedCalendars'];
            case 'group':
                return $_SESSION['spcUserGroupCalendars'];
            default:
                throw new Exception('unkown calendar type: ' . $type);
        }
    }

    /**
     * Gets group manager's users' calendars that toggled on by the manager
     *
     * @param int           $adminId
     * @return mixed
     */
    public static function getActiveUserCals($adminId) {
        $db = new SpcDb;
        $select = $db->select(array('cal_id'), 'spc_calendar_admin_user_cals')
                     ->where("admin_id = $adminId");

        $userCals = array();
        foreach ($db->fetchAll($select, 'row') as $row) {
            $userCals[] = $row[0];
        }

		return $userCals;
	}

    /**
	 * Converts hex colors to RGB values, used for creating event timerange color
	 *
	 * @param string $color (#eee, #eeeeee)
	 * @return array
	 */
	public static function getRGB($color) {
		$color = preg_replace('/#/', '', $color);
		if (strlen($color) == 3) {
			$color .= $color;
		}

		list($r, $g, $b) = str_split($color, 2);
		return array(hexdec($r), hexdec($g), hexdec($b));
	}

    public static function generateExportAccessKey($userId, $calId) {
        return $userId * $calId * 195329946;
    }

    public static function checkExportAccessKey($calId, $accessKey, $exportType = 'calendar') {
        $calModel = new Calendar_Model_Calendar;
        $select = $calModel->select("access_key")->where("id = $calId");
        $calAccessKey = $calModel->fetchColumn($select);

        if ($accessKey !== $calAccessKey) {
            throw new Exception('Wrong access key!');
        }
    }

    public static function export($action, $params) {
        $coreUserModel = new Core_Model_User;
        $coreUserModel->initAppConstants();
        $icalController = new Calendar_Controller_Ical;
        if ($action == 'exportCalendar') {
            $calId = (int)$params['calId'];
            $accessKey = $params['accessKey'];
            self::checkExportAccessKey($calId, $accessKey);

            return $icalController->exportCalendar($calId);
        } else if ($action == 'exportEvent') {
            $eventId = $params['eventId'];
            return $icalController->exportCalendar(null, $eventId);
        }
    }
}