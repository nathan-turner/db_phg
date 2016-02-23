<?php
/*
 * PHPDocX Configuration test
 */

error_reporting(0);

$output = '';

$break = isset($_SERVER['HTTP_USER_AGENT']) ? '<br />' : PHP_EOL;
$isWeb = isset($_SERVER['HTTP_USER_AGENT']) ? true : false;

if ($isWeb) {
	$output .= '<html>';
	$output .= '<head>';
	$output .= '<title>PHPDocX configuration test</title>';
	$output .= '<style type="text/css">';
	$output .= 'body {
		background: #fcfcfc;
		font-family: Arial, Sans-Serif;;
	}';
	$output .= '#page {
		border: 1px solid #ababab;
		margin: 15px auto 0 auto;
		width: 900px;
		background: #F3F3F3;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
	}';
	$output .= '#info {
		margin: 10px;
		background: white;
		padding: 15px 20px 10px 20px;
	}';
	$output .= '#header {
		background: url("http://www.phpdocx.com/sites/all/themes/zen_phpdocx/images/logo_cabecera.gif") no-repeat;
		border-bottom: 1px solid #EC008C;
		height: 100px;
		padding: 0 10px 10px 150px;
	}';
	$output .= '#header h1 {
		margin: 0px;
		color: #EC008C;
	}';
	$output .= '#sidebar {
		float: left;
		width: 90px;
	}';
	$output .= '#content {
		
	}';
	$output .= 'ul {
		list-style-type: none;
		margin:20px;
		padding:0;
	}';
	$output .= 'li {
		padding:10px;
	}';
	$output .= 'li.odd {
		background: #fcfcfc;
		border-top: 1px solid #EEE;
	}';
	$output .= 'li.even {
		border-top: 1px solid #EEE;
	}';
	$output .= '.testok {
		padding:5px;
		margin: 0 10px 0 0;
		color: #FFFFFF;
		background-color: #008000;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		display: inline-block;
		width:50px;
		font-size: 11px;
		text-align:center;
	}';
	$output .= '.testko {
		padding:5px;
		margin: 0 10px 0 0;
		color: #FFFFFF;
		background-color: #FE2E2E;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		display: inline-block;
		width:50px;
		font-size: 11px;
		text-align:center;
	}';
	$output .= '.testwarn {
		padding:5px;
		margin: 0 10px 0 0;
		color: #FFFFFF;
		background-color: #dd9118;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		display: inline-block;
		width:50px;
		font-size: 11px;
		text-align:center;
	}';
	$output .= '.clear {clear: both;}';
	$output .= '</style>';
	$output .= '</head>';
	$output .= '<body>';
	$output .= '<div id="page">';
	$output .= '<div id="info">';
	$output .= '<div id="header">';
	$output .= '<h1>PHPDocX configuration test</h1>';
	$output .= '<span>Welcome to PHPDocX checker</span>';
	$output .= '</div>';
	$output .= '<div id="content">';
	$output .= '<ul class="checks">';
}

$version = explode('.', PHP_VERSION);

$iPhpVersion = $version[0] * 10000 + $version[1] * 100 + $version[2];

if ($isWeb) {
	$output .= '<li class="odd">';
}
if ($iPhpVersion < 50000) {
	if ($isWeb) {
		$output .= '<span class="testko">';
	}
	$output .= 'Error';
	if ($isWeb) {
		$output .= '</span>';
	}
    $output .= 'Your PHP version (' . PHP_VERSION . '), is too old, please update to PHP 5' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'PHP version is ' . PHP_VERSION . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="even">';
}
if (!class_exists('ZipArchive')) {
	if ($isWeb) {
		$output .= '<span class="testko">';
	}
	$output .= 'Error';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'You must install ZIP support for PHP' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'Zip support is enabled.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="odd">';
}
if (!class_exists('XSLTProcessor')) {
	if ($isWeb) {
		$output .= '<span class="testko">';
	}
	$output .= 'Error';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'You must install XSL support for PHP' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'XSL support is enabled.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="even">';
}
if (!class_exists('DOMDocument')) {
	if ($isWeb) {
		$output .= '<span class="testko">';
	}
	$output .= 'Error';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'You must install DOM support for PHP' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'DOM support is enabled.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="odd">';
}
if (!class_exists('SimpleXMLElement')) {
	if ($isWeb) {
		$output .= '<span class="testko">';
	}
	$output .= 'Error';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'You must install XML support for PHP' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'XML support is enabled.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="even">';
}
if (!class_exists('Tidy')) {
	if ($isWeb) {
		$output .= '<span class="testwarn">';
	}
	$output .= 'Warning';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'You must install Tidy support for PHP if you want use embedHTML in your Word documents.' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'Tidy support is enabled.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="odd">';
}
if (!is_writable('examples/docx')) {
	if ($isWeb) {
		$output .= '<span class="testwarn">';
	}
	$output .= 'Warning';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'The path examples/docx used by the examples isn\'t writable.' . $break;
} else {
	if ($isWeb) {
		$output .= '<span class="testok">';
	}
	$output .= 'OK';
	if ($isWeb) {
		$output .= '</span>';
	}
	$output .= 'The path examples/docx used by the examples is writable.' . $break;
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="even">';
}
if (file_exists('classes/TransformDocAdv.inc')) {
	$fp = fsockopen('localhost', 8100, $errno, $errstr, 30);
	if (!$fp) {
		if ($isWeb) {
			$output .= '<span class="testwarn">';
		}
		$output .= 'Warning';
		if ($isWeb) {
			$output .= '</span>';
		}
   		$output .= 'You\'re not running OpenOffice in headless mode on port 8100.' . $break;
	} else {
		if ($isWeb) {
			$output .= '<span class="testok">';
		}
		$output .= 'OK';
		if ($isWeb) {
			$output .= '</span>';
		}
		$output .= 'OpenOffice is running in headless mode on port 8100.' . $break;
	}
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '<li class="odd">';
}
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
	if (file_exists('classes/TransformDocAdv.inc')) {
	}
} elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'LIN') {
	if (file_exists('classes/TransformDocAdv.inc')) {
	}
}
if ($isWeb) {
	$output .= '</li>';
}

if ($isWeb) {
	$output .= '</ul>';
	$output .= '</div>';
	$output .= '<div class="clear" />';
	$output .= '</div>';
	$output .= '</div>';
	$output .= '</body>';
	$output .= '</html>';
}

echo $output;