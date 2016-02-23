<?php
session_start();
error_reporting(E_ALL | E_STRICT);

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
$userStartDate = date($shortdate_format, strtotime($startDate));

$endDate =  isset($_GET['endDate'])
            ? date('Y-m-d', strtotime($_GET['endDate']))
            : date('Y-m-d');
$userEndDate = date($shortdate_format, strtotime($endDate));

?>
<!doctype html>
<html lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title>Smart PHP Calendar - Print Calendar</title>
      
   <link rel="stylesheet" type="text/css" href="css/theme/Smart-Silver/jquery-ui.css" />
   <link rel="stylesheet" type="text/css" href="css/print-page.css" />
   <link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
   
   <script type="text/javascript" src="js/jquery.js"></script>
   
   <script type="text/javascript">
        window.SMART = {};
        SMART.spcUserPrefs = $.parseJSON('<?php echo SmartUser::jsonEncode($userPrefs); ?>');
        SMART.Calendars = $.parseJSON('<?php echo SmartUser::jsonEncode($_SESSION['calendars']); ?>');
   </script>
   
   <script type="text/javascript" src="js/jquery-ui.js"></script>
   <script type="text/javascript" src="js/ui-i18n/jquery.ui.datepicker-<?php echo $language ?>.js"></script>
   <script type="text/javascript" src="js/smart-i18n/smart.i18n-<?php echo $language ?>.js?v=<?php echo time(); ?>"></script>
   <script type="text/javascript" src="js/print.js"></script>   
</head>

<body>
    <div id="print-container" class="dialog">
        <div id="print-head">
            Print Range: <input type="text" id="print-start-date" value="<?php echo $userStartDate; ?>" />
            - <input type="text" id="print-end-date" value="<?php echo $userEndDate; ?>" />
            
            <a href="#" id="print-calendar">Print</a>
            <a
                id="print-calendar-pdf"
                href="print-cal-pdf.php?startDate=<?php echo $startDate;?>&endDate=<?php echo $endDate;?>"
                target="_blank">
                    Save as Pdf
            </a>
        </div>
        
        <div id="print-body"></div>
        
        <div id="print-bottom">Printed on <?php echo date($shortdate_format); ?></div>
    </div>
</body>
</html>