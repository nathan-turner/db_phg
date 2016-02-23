<?php
    require_once 'SpcEngine.php';
    Spc::checkLogin();
    Spc::initLanguage();

    $spcDb = new SpcDb();
    $_GET = $spcDb->sanitizeInput($_GET);

    $shortdateFormat = Spc::getUserPref('shortdate_format', 'calendar');

    $startDate =    isset($_GET['startDate'])
                    ? date('Y-m-d', strtotime($_GET['startDate']))
                    : date('Y-m-d');
    $userStartDate = date($shortdateFormat, strtotime($startDate));

    $endDate =  isset($_GET['endDate'])
                ? date('Y-m-d', strtotime($_GET['endDate']))
                : date('Y-m-d');
    $userEndDate = date($shortdateFormat, strtotime($endDate));

    $activeCals = mysql_real_escape_string($_GET['cals']);

    $printCals = array();
    foreach (explode(',', $activeCals) as $calId) {
        $printCals[$calId] = $_SESSION['calendars'][$calId];
    }

    $printApp = mysql_real_escape_string($_GET['app']);
?>
<!doctype html>
<html lang="en">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title>Smart PHP Calendar - Print</title>

   <link rel="stylesheet" href="css/theme/smart-white/jquery-ui.css" />
   <link rel="stylesheet" href="css/print-page.css" />
   <link rel="stylesheet" href="css/print.css" media="print" />

   <script src="js/jquery.js"></script>
   <script src="js/handlebars.js"></script>

    <script src="js/jquery-ui.js"></script>
    <script src="js/ui-i18n/jquery.ui.datepicker-<?php echo Spc::getUserPref('language'); ?>.js"></script>

    <script src="js/spc.core.js?v=<?php echo time(); ?>"></script>
    <script>
        $(function() {
            SPC.printCals = <?php echo Spc::jsonEncode($printCals); ?>;
        SPC.printCalIds = "<?php echo $activeCals ?>";

        SPC.printApp = "<?php echo $printApp; ?>";
        SPC.printAppParams = $.parseJSON('<?php echo json_encode($_GET); ?>');
        })
    </script>

    <script src="js/spc.utils.js?v=<?php echo time(); ?>"></script>
    <script src="js/print.js"></script>
</head>

<body>
    <div id="print-container" class="dialog">
        <div id="print-calendar-wrapper" class="app"  data-app="calendar">
            <div id="print-head">
                Print Range:
                <input type="text" id="print-start-date" value="<?php echo $userStartDate; ?>" />
                - <input type="text" id="print-end-date" value="<?php echo $userEndDate; ?>" />

                <a href="#" id="print-calendar">Print</a>
                <a
                    id="print-calendar-pdf"
                    style="display: none;"
                    href="print-cal-pdf.php?startDate=<?php echo $startDate;?>&endDate=<?php echo $endDate;?>"
                    target="_blank">
                        Save as Pdf
                </a>
            </div>

            <div id="print-body"></div>

            <div id="print-bottom">
                <div style="float: left;" id="print-date"></div>
                <div style='float: right;'>Smart PHP Calendar</div>
            </div>
        </div>

    </div>
</body>
</html>