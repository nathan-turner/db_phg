<?php
/**
 * Smart PHP Calendar
 *
 * @category   Spc
 * @package    Core
 * @copyright  Copyright (c) 2008-2011 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */

/**
 * Core Application Class
 *
 * Holds global application methods and initializers
 */
class Spc {

    private static $_sender;

    protected static $_dbConn;

    private static $_user;

    private static $_modules;

    private static $_plugins;

    private static $_translate;

    private static $_timezones;

    private static $_languages;

    private static $_themes;

    /**
     * Hold old system permissions list
     *
     * @var array
     */
    private static $_systemPermissions;

    /**
     * Requires a file from modules
     *
     * @param string $filepath
     * @param string $module
     * @return mixed
     */
    public static function requireFile($filepath, $module) {
        return require SPC_SYSPATH . '/apps/' . $module . '/' . $filepath;
    }

    public static function getDomain() {
        return SPC_DOMAIN;
    }

    public static function getLicenseKey() {
        return SPC_LICENSE_KEY;
    }

    public static function startSession() {
        if (!isset($_SESSION)) {
            $sessionName = session_name();
            if (isset($_POST[$sessionName])) {
                session_id($_POST[$sessionName]);
            }
            session_start();
        }
    }

    public static function initApp() {
        if (!defined('SPC_OUTPUT_BUFFER_OFF')) {
            ob_start();
        }

        self::startSession();

        //application modules (calendar, contacts, tasks, file-manager, etc.)
        self::$_modules = SPC_MODULES ? explode(',', SPC_MODULES) : array();

        //application plugins (events_calendar, mobile_calendar, etc.)
        self::$_plugins = SPC_PLUGINS ? explode(',', SPC_PLUGINS) : array();

        //user permissions
        self::$_systemPermissions = array(
            'create-users',
            'edit-users',
            'delete-users',

            'create-calendars',
            'edit-calendars',
            'delete-calendars',
            'share-calendars',

            'create-events',
            'edit-events',
            'delete-events'
        );

        //run
        self::run();
    }

    public static function initAutoload() {
        require SPC_SYSPATH . '/core/SpcAutoLoader.php';
        $spcAutoLoader = new SpcAutoLoader;
        spl_autoload_register(array($spcAutoLoader, 'autoloader'));
    }

    public static function run() {
        //init database to sanitize post
        new SpcDb;

        //ajax engine run
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {

            self::setSender(isset($_POST['sender']) ? $_POST['sender'] : $_GET['sender']);
            if (self::getSender() === 'private') {
                self::checkLogin();
            }

            //if user is set define app global constants
            if (isset($_SESSION['spcUserPrefs'])) {
                $userModel = new Core_Model_User;
                $userModel->initAppConstants();
            }

            if (self::getSender() == 'event-calendar') {
                self::setEventsCalUserPrefs($_POST['accessKey']);
            }

            $afc = new SpcAjaxFrontController();
            $afc->dispatch();
        }
    }

    public static function getSystemPermissions() {
        return self::$_systemPermissions;
    }

    public static function checkLogin() {
        if (!isset($_SESSION['spcUserLoggedIn'])
                || (isset($_SESSION['SPC_VERSION']) && ($_SESSION['SPC_VERSION'] != SPC_VERSION))) {

            unset($_SESSION['spcUserLoggedIn']);
            header('Location: ' . SPC_ROOT . '/login.php');
            exit;
        }

        $userModel = new Core_Model_User;
        $_SESSION['spcUserPrefs'] = $userModel->getUserPrefs($_SESSION['spcUserPrefs']['id']);
        $userModel->initAppConstants();
        Spc::initLanguage();
        Spc::initUserPermissions(SPC_USERID);
    }

    public static function getLanguages() {
        //init languages
        if (!self::$_languages) {
            $langTitles = array(
                'de' => 'Deutsch',
                'en' => 'English',
                'es' => 'Español',
                'fr' => 'Français',
                'el' => 'Greek',
                'it' => 'Italiano',
                'nl' => 'Nederlands',
                'tr' => 'Türkçe',
            );

            $dirIt = new DirectoryIterator(SPC_SYSPATH . '/i18n');
            foreach ($dirIt as $file) {
                if ($file->isFile()) {
                    list($langId, $ext) = explode('.', $file->getFileName());
                    if (isset($langTitles[$langId])) {
                        self::$_languages[$langId] = $langTitles[$langId];
                    } else {
                        self::$_languages[$langId] = $langId;
                    }
                }
            }
        }

        return self::$_languages;
    }

    public static function getTimezones() {
        global $t_z;
        require_once SPC_SYSPATH . '/apps/calendar/files/timezones.php';
        self::$_timezones = $t_z;
        return self::$_timezones;
    }

    /**
     * Gets application modules
     *
     * @return array
     */
    public static function getModules() {
        return self::$_modules;
    }

    /**
     * Gets application plugins
     *
     * @return array
     */
    public static function getPlugins() {
        return self::$_plugins;
    }

    public static function getSender() {
        return self::$_sender;
    }

    public static function setSender($sender) {
        self::$_sender = $sender;
    }

    public static function getUserPrefs() {
        return $_SESSION['spcUserPrefs'];
    }

    public static function getUserPref($pref, $module = null) {
        //get module's preference
        if ($module) {
            if (isset($_SESSION['spcUserPrefs'])) {
                return isset($_SESSION['spcUserPrefs'][$module][$pref])
                       ? $_SESSION['spcUserPrefs'][$module][$pref]
                       : false;
            }

            if (isset($_SESSION['spcEventsCalUserPrefs'])) {
                return isset($_SESSION['spcEventsCalUserPrefs'][$module][$pref])
                       ? $_SESSION['spcEventsCalUserPrefs'][$module][$pref]
                       : false;
            }
        }

        //get core application preference
        if (isset($_SESSION['spcUserPrefs'])) {
            return $_SESSION['spcUserPrefs'][$pref];
        }

        if (isset($_SESSION['spcEventsCalUserPrefs'])) {
            return $_SESSION['spcEventsCalUserPrefs'][$pref];
        }
    }

    /**
     * Get application themes
     * (theme folder names in css/theme folder)
     *
     * @return array
     */
    public static function getAppThemes() {
        if (self::$_themes) {
            return self::$_themes;
        }

        $themeDir = SPC_APP_DIR . '/css/theme';
        $dirIt = new DirectoryIterator($themeDir);
        $themes = array();
        foreach ($dirIt as $f) {
            if ($f->isDir() && !$f->isDot()) {
                $themes[] = $f->getFilename();
            }
        }

        self::$_themes = $themes;

        return $themes;
    }

    /**
     * Get application icons directory
     * (global app icons placed in img folder)
     *
     * @return string
     */
    public static function getGlobalAppIconsDirName() {
        $userTheme = self::getUserPref('theme');
        $blackIconsDir = 'black-icons';
        $blueIconsDir = 'blue-icons';
        $blueThemes = array('smart-blue', 'flick', 'cupertino', 'hot-sneaks', 'redmond', 'smart-space');
        if (array_search($userTheme, $blueThemes) !== false) {
            return $blueIconsDir;
        }

        return $blackIconsDir;
    }

    /**
     * Gets Main App Icons (top-right)
     */
    public static function getAppIcons() {
        if (self::getUserPref('big_icons') == 1) {
            return require SPC_SYSPATH . '/apps/core/files/big-icons.php';
        }

        return require SPC_SYSPATH . '/apps/core/files/small-icons.php';
    }

    /**
     * Encodes variables to JSON string
     *
     * @param mixed         $v value
     * @param bool          $success
     * @return string       encoded json string
     */
    public static function jsonEncode($v = array(), $success = true, $addSuccessMsg = true) {
        if ($addSuccessMsg) {
            $v['success'] = $success;
        }

        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            require_once SPC_SYSPATH . '/libs/JSON.php';
			$j = new Services_JSON();
            $jsonRes = $j->encode($v);
		}

		$jsonRes = json_encode($v);

        //jsonp request
        if (isset($_GET['callback'])) {
            $callback = $_GET['callback'];
            $jsonRes = "$callback($jsonRes)";
        }

        return $jsonRes;
    }

    public static function initLanguage($lang = null) {
        //system language
        global $spcI18n;
        if ($lang === null) {
            require_once SPC_SYSPATH . '/i18n/' . self::getUserPref('language') . '.php';
        } else {
            require_once SPC_SYSPATH . "/i18n/$lang.php";
        }

        self::$_translate = $spcI18n;
    }

    public static function initUserPermissions($userId) {
        $userModel = new Core_Model_User;
        $userPermissions = $userModel->getUserPermissions($userId);
        $_SESSION['spcUserPrefs']['permissions'] = $userPermissions;
    }

    public static function getLanguageTranslationArray() {
        return self::$_translate;
    }

    public static function translate($key, $capitalize = true) {
        if ($capitalize) {
            return self::mb_ucfirst(self::$_translate[$key]);
        }

        return self::$_translate[$key];
    }

    public static function t($key, $capitalize = true) {
        return self::translate($key, $capitalize);
    }

    public static function mb_ucfirst($str) {
		if (function_exists('mb_strtoupper')) {
			return mb_strtoupper(mb_substr($str, 0, 1, 'utf-8'), 'utf-8')
                   . mb_substr($str, 1, (mb_strlen($str, 'utf-8') - 1), 'utf-8');
		}

		return ucfirst($str);
	}

    public static function substr($str, $start, $lenght) {
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $lenght, 'utf-8');
        }

        return substr($str, $start, $lenght);
    }

    public static function getEventsCalUserPrefs() {

    }

    public static function setEventsCalUserPrefs($accessKey) {
        $userModel = new Core_Model_User;
        $userPrefs = $userModel->getUserPrefs(1);

        $_SESSION['spcEventCalUserPrefs'] = $userPrefs;

        $calModel = new Calendar_Model_Calendar;
        $_SESSION['spcEventCalUserCals'] = $calModel->getUserCals($userPrefs['id'], true);

        return $userPrefs;
    }

    public static function initEventsCal() {
        $userPrefs = Spc::getEventsCalUserPrefs();
        Spc::initLanguage($userPrefs['language']);

        echo "<link rel='stylesheet' href='" . SPC_ROOT . "/css/spc.event-calendar.css' />
              <link rel='stylesheet' href='" . SPC_ROOT . "/css/theme/{$userPrefs['theme']}/jquery-ui.css' />

              <script type='text/javascript'>
                    var SPC_DOMAIN = '" . Spc::getDomain() . "',
                        SPC_LICENSE_KEY = '" . Spc::getLicenseKey() . "';

                    window.SPC = {};
                    SPC.ROOT = '" . SPC_ROOT . "';
                    SPC.ENGINE_PATH = '" . SPC_ENGINE_PATH . "';
                    SPC.sender = 'public|events-calendar-plugin';
                    SPC.currentDate = '" . date('Y-m-d') . "';
                    SPC.userPrefs = $.parseJSON('" . Spc::jsonEncode($userPrefs) . "');
                    SPC.i18n = $.parseJSON('" . Spc::jsonEncode(Spc::getLanguageTranslationArray()) . "');

                    if (!$.datepicker) {
                        $.datepicker = {
                            regional: {},
                            setDefaults: function() {}
                        };
                    }
                </script>
            <script src='" . SPC_ROOT . "/js/ui-i18n/jquery.ui.datepicker-{$userPrefs['language']}.js'></script>
            <script src='" . SPC_ROOT . "/js/spc.core.js'></script>
            <script src='" . SPC_ROOT . "/js/spc.utils.js'></script>
            <script src='" . SPC_ROOT . "/js/spc.events-calendar.js'></script>";
    }
}