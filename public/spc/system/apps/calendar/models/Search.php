<?php

class Calendar_Model_Search extends SpcDbTable {

    protected $_name = 'calendar_events';

    public function search($word) {
        $fields = array(
                    'id',
                    'cal_id',
                    'start_date',
                    'start_time',
                    'end_date',
                    'end_time',
                    'title',
                    'repeat_type',
                    'repeat_interval',
                    'repeat_count',
                    'repeat_data',
                    'repeat_end_date');

        $select = $this->select($fields)
                  ->leftJoin('spc_calendar_calendars', 'spc_calendar_calendars.id = spc_calendar_events.cal_id')
                  ->leftJoin('spc_calendar_shared_calendars', 'spc_calendar_shared_calendars.cal_id = spc_calendar_events.cal_id')
                  ->where("spc_calendar_events.title LIKE '%$word%'
                          OR spc_calendar_events.description LIKE '%$word%'
                          OR spc_calendar_events.location LIKE '%$word%'");

        $sender = Spc::getSender();

        //if sender is public (spc-public-calendar or events-calendar) get only public events
        if (strstr($sender, 'public')) {
            $select->where("spc_calendar_events.public = '1'");
        }

        if (strstr($sender, 'spc-public-calendar')) {
            $userId = $_SESSION['spcPluginUserPrefs']['id'];
            $select->where("spc_calendar_calendars.user_id = {$userId}");
        }

        if (strstr($sender, 'events-calendar-plugin')) {
            $userId = $_SESSION['spcEventsCalUserPrefs']['id'];
            $select->where("spc_calendar_calendars.user_id = {$userId}");
        }

        if (strstr($sender, 'private')) {
            $select->where("spc_calendar_calendars.user_id = {$this->_userId}
                            OR
                            spc_calendar_shared_calendars.shared_user_id = {$this->_userId}");
        }

        return $this->fetchAll($select);
    }
}