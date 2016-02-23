<?php

/**
 * Smart PHP Calendar
 *
 * @category   Spc
 * @package    Core
 * @copyright  Copyright (c) 2008-2011 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */

//------------------------------------------------------------------------------
// Smart PHP Calendar - Ajax Engine
//------------------------------------------------------------------------------

#set_time_limit(0);

/**
 * Application path
 */
$appDir = dirname(__FILE__);
$appDir = str_replace('\\', '/', $appDir);
define('SPC_APP_DIR', $appDir);

/**
 * System Path
 */
define('SPC_SYSPATH', SPC_APP_DIR . '/system');

/**
 * User config file
 */
require_once SPC_APP_DIR . '/config.php';

/**
 * Default timezone for whole application
 */
date_default_timezone_set(SPC_DEFAULT_TIMEZONE);

require_once SPC_APP_DIR . '/system/core/Spc.php';

Spc::initAutoLoad();

$spcError = new SpcError();

Spc::initApp();