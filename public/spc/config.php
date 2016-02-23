<?php

/**
 * Smart PHP Calendar 2.5
 *
 * @copyright  Copyright (c) 2008-2012 Yasin Dagli (http://www.smartphpcalendar.com)
 * @license    http://www.smartphpcalendar.com/license
 */

//------------------------------------------------------------------------------
// Smart PHP Calendar - System Settings
//------------------------------------------------------------------------------

define('SPC_VERSION', 'PREMIUM');

/**
 * Application environment
 * values: development | production
 */
define('SPC_ENV', 'production');

//------------------------------------------------------------------------------
// Modules
//------------------------------------------------------------------------------

//write your multiple modules by separating comma
//do not touch this
define('SPC_MODULES', 'calendar');

//------------------------------------------------------------------------------
// Plugins
//------------------------------------------------------------------------------

//write your multiple plugins by separating comma
//do not touch this
define('SPC_PLUGINS', 'events_calendar,mobile_calendar');

//------------------------------------------------------------------------------
// Domain Name and License Key
//------------------------------------------------------------------------------

// Exact calendar address
// example: http://yourhost.com/your-calendar-directory (without trailing slash)
define('SPC_ROOT', 'http://db.localhost/public/spc');

// domain name | without protocol (http, https), www and subdomain
// example: yourhost.com
define('SPC_DOMAIN', 'localhost');

define('SPC_LICENSE_KEY', 'SPC-TRIAL');

//------------------------------------------------------------------------------
// Database Configuration
//------------------------------------------------------------------------------

define('SPC_DB_HOST', '127.0.0.1');
define('SPC_DB_USERNAME', 'root');
define('SPC_DB_PASSWORD', '');
define('SPC_DB_DBNAME', 'dbo2');

//use Smart PHP Calendar as Wordpress Plugin
#define('WP_PLUGIN', true);

//------------------------------------------------------------------------------
// Default timezone
//------------------------------------------------------------------------------

// when superuser or admin create user this will be user's default timezone
// users can change it in their calendar settings
define('SPC_DEFAULT_TIMEZONE', 'Europe/Istanbul');

#define('EVENTS_CAL_USER', 'Jenny');

//------------------------------------------------------------------------------
// Calendar Sharing
//------------------------------------------------------------------------------

//show input box or usernames dropdown menu in calendar sharing dialog

// none: text input box, you have to type username
// group: usernames dropdown menu that shows all usernames in a group
// all: usernames dropdown menu that shows all usernames

define('CAL_SHARE_SHOW_USERNAME', 'all');

//------------------------------------------------------------------------------
// Calendar Import/Export
//------------------------------------------------------------------------------

//ical default event privacy
// private: all imported events will be private
// public: all imported events will be public
define('ICAL_IMPORT_EVENT_PRIVACY', 'private');

//------------------------------------------------------------------------------
// Email and Popup Reminders
//------------------------------------------------------------------------------

//reminder count for each event
define('EVENT_REMINDER_COUNT', 10);

// use phpmailer for sending email notifications
// if false native PHP mail() function will be used
define('USE_PHP_MAILER', true);

define('EMAIL_REMINDER_FROM', 'noreply@smartphpcalendar.com');
define('EMAIL_REMINDER_SUBJECT', 'Smart PHP Calendar');

//PHP Mailer Configurations
//default configured for Gmail
define('PHP_MAILER_PROTOCOL', 'smtp');
define('PHP_MAILER_SECURITY', 'ssl');
define('PHP_MAILER_HOST', 'smtp.gmail.com');
define('PHP_MAILER_PORT', 465);
define('PHP_MAILER_USERNAME', '');
define('PHP_MAILER_PASSWORD', '');

//------------------------------------------------------------------------------
// Public View
//------------------------------------------------------------------------------

//show calendars on the left side
define('PUBLIC_VIEW_SHOW_CALS', true);