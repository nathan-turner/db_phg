<?php

/**
 * Smart PHP Calendar
 *
 * Copyright (c) 2011 Yasin Dagli, Smart PHP Calendar, All rights reserved.
 *
 * This file is protected by international laws. Reverse engineering this file is strictly prohibited.
 */

//------------------------------------------------------------------------------
// Smart PHP Calendar - Application Bootstrap
//------------------------------------------------------------------------------

/**
 * Default timezone for whole application
 */
date_default_timezone_set(SPC_TIMEZONE);

require SPC_APP_DIR . '/system/core/Spc.class.php';

Spc::initErrorHandler();

spl_autoload_register(array('Spc', 'initAutoLoad'));

Spc::initApp();