<?php
    require_once 'SpcEngine.php';
    Spc::checkLogin();
?>
<!doctype html>
<html lang="en">
<head>
	<title>Smart PHP Calendar</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<meta http-equiv="Expires" content="-1" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />

    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />

    <link rel="stylesheet" href="css/theme/<?php echo Spc::getUserPref('theme'); ?>/jquery-ui.css" />

    <link rel="stylesheet" href="css/smartphpcalendar.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin" />

    <!--[if ie 8]><link rel="stylesheet" href="css/spc.ie8.css" /><![endif]-->

	<script src="js/jquery.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/ui-i18n/jquery.ui.datepicker-<?php echo Spc::getUserPref('language'); ?>.js"></script>
    <script src="js/handlebars.js"></script>

	<script src="js/spc.core.js?v=<?php echo time(); ?>"></script>
	<script src="js/spc.utils.js?v=<?php echo time(); ?>"></script>

    <script src="js/spc.calendar.js?v=<?php echo time(); ?>"></script>

    <?php if (Spc::getUserPref('wysiwyg', 'calendar') == '1'): ?>
    <script src="js/ckeditor/ckeditor.js"></script>
    <script src="js/ckeditor/adapters/jquery.js"></script>
    <?php endif; ?>

    <script>
        $(function() {
            SPC
                .on("eventClick", function() {
                    //console.log("e1");
                })
                .on("eventClick", function(e) {
                    //console.log(e);
                })
                .on("editEvent", function() {
                    //console.log("canceling update");
                    //return false;
                });
        });
    </script>
</head>
<body>
    <div id="status" class=""></div>
    <div id="core-app" class="ui-widget-content hidden">
        <div id="core-app-nav">
            <!-- application navigation | status -->
            <table id="user-bar" class="spc-widget-shadow <?php if (Spc::getUserPref('big_icons') == 0) echo 'ui-widget-header ui-widget-content'; ?>">
                <tbody>
                    <tr>
                        <td id="spc-app-nav">
                            <form id="search-form">
                                <input
                                    type="text"
                                    id="search"
                                    class="ui-corner-all ui-widget-content"
                                    placeholder="<?php echo Spc::translate('search'); ?>" />
                            </form>
                        </td>

                        <td></td>

                        <td>
                            <div id="user-status" class="ui-buttonset">
                                <?php Spc::getAppIcons(); ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- /application navigation | status -->
        </div>

        <div id="app-container">
            <?php Spc::requireFile('files/app-content.php', 'calendar'); ?>
        </div>
    </div>
</body>
</html>