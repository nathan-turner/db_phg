<?php
session_start();
error_reporting(E_ALL | E_STRICT);

set_time_limit(0);

if ( ! isset($_SESSION['spcUserLoggedIn'])) {
	header('Location: login.php');
	exit('<a href="login.php">please login</a>');
}


include_once 'smart.user.php';
include_once 'smart.decorator.php';
include_once 'smart.calendar.class.php';

SmartUser::connectDb();

//user preferences | $date is used both retrieving status date and jQuery datepicker initialization
$_SESSION['spcUserPrefs'] = SmartUser::getUserPrefs($_SESSION['spcUserPrefs']['username']);
extract($_SESSION['spcUserPrefs']);
SmartUser::initUserCalendars($id);
date_default_timezone_set($timezone);
$date = isset($_GET['d']) ? SmartUser::protectVariable($_GET['d']) : date('Y-m-d');

//Smart PHP Calendar i18n file
//GLOBAL: $smartI18n
include_once 'i18n/smart.i18n-' . $language . '.php';

//Smart Decorator is just used for retrieving status calendar see: <div id="user_bar">
$smartPHPCalendar = new SmartPHPCalendar();
$smartPHPCalendar->setUser($_SESSION['spcUserPrefs']['id']);
$smartPHPCalendar->setLanguage($language);
$smartDecorator = new SmartPHPCalendar_Decorator($smartPHPCalendar);
$smartDecorator->setDate($date);

//used in JavaScript Engine for getting user-specific operations, converting date formats, time format, etc
$userPrefs = $_SESSION['spcUserPrefs'];
unset($userPrefs['password']);

$startDate =    isset($_GET['startDate'])
                ? date('Y-m-d', strtotime($_GET['startDate']))
                : date('Y-m-d');

$endDate =  isset($_GET['endDate'])
            ? date('Y-m-d', strtotime($_GET['endDate']))
            : date('Y-m-d');

$smartEventCRUD = new SmartEventCRUD();
$events = $smartEventCRUD->getEvents('private', $startDate, $endDate, 'print');
$events = $events['all'];

$eventList = '<h4>Smart PHP Calendar - Events</h4>';
$eventList .= '<h4>'
                . date($shortdate_format, strtotime($startDate))
                . ' - '
                . date($shortdate_format, strtotime($endDate))
            . '</h4>';

$eventList .= '<ul>';
foreach ($events as $date => $dateEvents) {
    $date = date($shortdate_format, strtotime($date));
    
    $eventList .=   "<li>
                        $date
                    <ul>";
    
    foreach($dateEvents as $event) {
        
        $calName = $_SESSION['calendars'][$event['calendar_id']]['name'];
        
        $startDate = date($shortdate_format, strtotime($event['start_date']));
        $startTime = $smartPHPCalendar->convertTime($event['start_time']);
        
        $endDate = date($shortdate_format, strtotime($event['end_date']));
        $endTime = $smartPHPCalendar->convertTime($event['end_time']);
        
        $eventDate = $startDate;
        if ($event['type'] == 'multi_day') {
            $eventDate = $startDate . ' - ' . $endDate;
        }
        
        $eventTime = $startTime . ' - ' . $endTime;
        if (($event['start_time'] == '00:00') && ($event['end_time'] == '00:00')) {
            $eventTime = 'all day';
        }
        
        $title = $event['title'];
        $location = $event['location'];
        $description = $event['description'];
        $repeat = $event['repeat_type'];        
        
        $eventList .= "<li style='list-style-type: none;'>
                            calendar: $calName <br />
                            date: $eventDate $eventTime <br />
                            title: $title <br />
                            location: $location <br />
                            description: $description <br />
                            repeat: $repeat
                        </li> <br />";
    }
    
    $eventList .= '</ul> </li>';
}

$eventList .= '</ul>';

$eventList .= '<p>Printed on ' . date($shortdate_format) . '</p>';

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
/*
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 008');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 008', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
*/

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
/**
 * By default TCPDF enables font subsetting to reduce the size of embedded Unicode TTF fonts,
 * this process, that is very slow and requires a lot of memory, can be turned off using setFontSubsetting(false) method;
 */ 
$pdf->setFontSubsetting(false);

// set font
$pdf->SetFont('freeserif', '', 12);
#$pdf->SetFont('times', '', 12);

// add a page
$pdf->AddPage();

// get esternal file content
//$utf8text = file_get_contents('../cache/utf8test.txt', false);

// set color for text
$pdf->SetTextColor(0, 63, 127);

//Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0)

$pdf->writeHTML($eventList, true, false, false, false, '');

// write the text
//$pdf->Write(5, $table, '', 0, '', false, 0, false, false, 0);


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('smartphpcalendar-events.pdf', 'I');