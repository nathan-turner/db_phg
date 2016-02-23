<?php

class Calendar_Controller_Event extends SpcController {

    /**
     * Event model
     *
     * @var object
     */
    public $event;

    public function __construct() {
        parent::__construct();
        $this->event = new Calendar_Model_Event;
    }

    /**
	 * Gets single calendar event
	 *
	 * @param int           $eventId
	 * @return string       (JSON)
	 */
	public function getEvent($eventId) {
        $event = $this->event->getEvent($eventId);
        echo Spc::jsonEncode(array('event' => $event));
	}

    public function getEvents($calendarView, $calendars, $startDate, $endDate, $public = false) {
        $calendars = ($calendars == '0') ? array() : explode(',', $calendars);
        $events = $this->event->getEvents($calendarView, $calendars, $startDate, $endDate, $public);
        echo Spc::jsonEncode(array('events' => $events));
    }

    public function insertEvent($event) {
        echo Spc::jsonEncode(array('eventId' => $this->event->insertEvent($event)));
    }

    public function updateEvent($event) {
        $this->event->updateEvent($event);
    }

    public function deleteEvent($eventId, $invitation, $invitationEventId, $repeatIndexes = null) {
        $this->event->deleteEvent($eventId, $invitation, $invitationEventId, $repeatIndexes);
    }

    public function dragEvent($event) {
        $this->event->dragEvent($event);
    }

    public function resizeEvent($event) {
        $this->event->resizeEvent($event);
    }

    public function getGuests($eventId) {
        echo Spc::jsonEncode(array(
            'guests' => $this->event->getGuests($eventId)
        ));
    }

    public function updateInvitationResponse($inviteeId, $response) {
        $this->event->updateInvitationResponse($inviteeId, $response);
    }

    public function deleteImage($image) {
        $this->event->deleteImage($image);
    }

    public function getUserThumbs() {
        echo Spc::jsonEncode(array(
            'thumbs' => $this->event->getUserThumbs()
        ));
	}

    /**
     * Sample event fetch method
     * You can implement your own logic here: get events from other databases (Oracle, SQL Server, etc.)
     * or other webservices
     *
     * @param $view string (calendar view: day, week, month, agenda, year, staff (resources), custom (x-days))
     * @param $viewDates array (contains current views all dates)
     * @param $activeCals string (comma concatenation of active calendar id e.g. 134,567,92)
     */
    public function getMyEvents($view = null, $viewDates = null, $activeCals = null) {
        $events = array(
            array(
                'title' => 'event1',
                'start_date'=> '2013-03-14',
                'start_time'=> '13:30',
                'end_date'=> '2013-03-14',
                'end_time'=> '13:50'
            ),
            array(
                'title' => 'event2',
                'start_date'=> '2013-03-25',
                'start_time'=> '12:30',
                'end_date'=> '2013-03-25',
                'end_time'=> '14:50'
            ),
            array(
                'title' => 'event3',
                'start_date'=> '2013-03-16',
                'start_time'=> '00:00',
                'end_date'=> '2013-03-16',
                'end_time'=> '00:00'
            ),
            array(
                'title' => 'event4',
                'start_date'=> '2013-03-17',
                'start_time'=> '00:00',
                'end_date'=> '2013-03-29',
                'end_time'=> '00:00'
            )
        );

        echo Spc::jsonEncode(array('events' => $events));
    }
}