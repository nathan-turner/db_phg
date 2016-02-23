<?php

class Calendar_Helper_Date {
    public function getClock($timeFormat = null) {
        if (!$timeFormat) {
            $timeFormat = Spc::getUserPref('timeformat', 'calendar');
        }

        if ($timeFormat == 'standard') {
			return date('g:ia');
		}

		return date('G:i');
    }

    public function getTimemarker() {
		$startTime = mktime(0, 0, 0, 1, 1);
		$now = mktime(date('H'), date('i'), 0, 1, 1);
		$timeDiff = ($now - $startTime) / 60;

		return ($timeDiff * 40) / 60;
	}

    public function convertDate($date, $format) {
        if (!$format) {
            $format = Spc::getUserPref('shortdate_format', 'calendar');
        }
        return date($format, strtotime($date));
    }

    public function convertTime($time, $format) {
        if ($format == 'core') {
            return $time;
        }

        list($hour, $min) = explode(':', $time);
        return date('g:ia', mktime($hour, $min));
    }

    public function getEventType($event) {
        $startDate = $event['start_date'];
        $startTime = $event['start_time'];
        $endDate = $event['end_date'];
        $endTime = $event['end_time'];

        //standard-event
        if (($startDate == $endDate) && ($startTime != '00:00')) {
            return 'standard';
        }

        //all-day event
        if (($startDate == $endDate)
                && (($startTime == '00:00') && ($endTime == '00:00'))) {

            return 'all_day';
        }

        //multi-day
        if ($startDate != $endDate) {
            return 'multi_day';
        }
    }

    public function getPublicEventDateTitle($event, $userTimeFormat = null, $userShortDateFormat = null) {
        if (!$userTimeFormat) {
            $userTimeFormat = Spc::getUserPref('timeformat', 'calendar');
        }

        if (!$userShortDateFormat) {
            $userShortDateFormat = Spc::getUserPref('shortdate_format', 'calendar');
        }

        $eventType = $this->getEventType($event);

        $userStartDate = $this->convertDate($event['start_date'], $userShortDateFormat);
        $userStartTime = $this->convertTime($event['start_time'], $userTimeFormat);

        $userEndDate = $this->convertDate($event['end_date'], $userShortDateFormat);
        $userEndTime = $this->convertTime($event['end_time'], $userTimeFormat);

        switch ($eventType) {
            case 'standard':
                return $userStartDate
                       . ', '
                       . $userStartTime . ' - ' . $userEndTime;

            case 'all_day':
                return $userStartDate
                       . ', ' . Spc::translate('All day');

            case 'multi_day':
                if (($event['start_time'] == '00:00')
                        || ($event['end_time'] == '00:00')) {

                    return $userStartDate . ' - ' . $userStartDate;
                }

                return $userStartDate . ', ' . $userStartTime
                       . ' - '
                       . $userEndDate . ', ' . $userEndTime;
        }
    }
}