<?php

class Calendar_Helper_Event {
    public function getTypePrecedence($type) {
		if ($type == 'multi_day') {
			return -1;
		}
		if ($type == 'all_day') {
			return 0;
		}
		return 1;
	}

	public function sortTime($event1, $event2)
	{
		$t1 = $event1['type'];
		$t2 = $event2['type'];

		if ($t1 == 'standard' && $t2 == 'standard') {
			if ($event1['start_time'] == $event2['start_time']) {
				return 0;
			}

			return $event1['start_time'] < $event2['start_time'] ? -1 : 1;
		}

		$t1 = $this->getTypePrecedence($t1);
		$t2 = $this->getTypePrecedence($t2);

		if ($t1 == $t2) {
			return 0;
		}

		return $t1 < $t2 ? -1 : 1;
	}

    public function sortDate($x, $y)
	{
		$time1 = strtotime($x['start_date'] . ' ' . $x['start_time']);
		$time2 = strtotime($y['start_date'] . ' ' . $y['start_time']);

		if ($time1 == $time2) {
			return 0;
		}

		return $time1 < $time2 ? -1 : 1;
	}
}