<?php

/**
 * Inserts a simple text in bold Arial font.
 *
 * @category   Phpdocx
 * @package    examples
 * @subpackage easy
 * @copyright  Copyright (c) 2009-2013 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2012.12.26
 * @link       http://www.phpdocx.com
 * @since      File available since Release 1.8
 */

require_once '../../classes/CreateDocx.inc';

$docx = new CreateDocx();

$text = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
    'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut ' .
    'enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut' .
    'aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit ' .
    'in voluptate velit esse cillum dolore eu fugiat nulla pariatur. ' .
    'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui ' .
    'officia deserunt mollit anim id est laborum.';

$paramsText = array(
    'b' => 'on',
    'font' => 'Arial'
);

$docx->addText($text, $paramsText);

$docx->addText('Now we are going to insert the text in two different table cells:');

$paramsText = array(
    'font' => 'Arial',
    'rawWordML' => true
    
);

$textData = $docx->addText($text, $paramsText);

$fragment = $docx->createWordMLFragment($textData);

$valuesTable = array(
    array(
        11,
        $fragment
    ),
    array(
        $fragment,
        22
    ),
);

$paramsTable = array(
    'border' => 'single',
    'border_sz' => 5,
);

$test = $docx->addTable($valuesTable, $paramsTable);

$docx->createDocx('../docx/example_text');