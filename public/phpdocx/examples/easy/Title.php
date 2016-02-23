<?php

/**
 * Inserts a title in a Word document
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

$paramsTitle = array(
    'font' => 'Blackadder ITC',
    'sz' => 22
);

$docx->addTitle('Lorem ipsum dolor sit amet.', $paramsTitle);

$docx->addText('Now we are going to insert twice the same title in different table cells:');

$paramsTitle = array(
    'font' => 'Blackadder ITC',
    'sz' => 22,
    'rawWordML' => true
);

$titleData = $docx->addTitle('Lorem ipsum dolor sit amet.', $paramsTitle);

$fragment = $docx->createWordMLFragment($titleData);

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

$docx->createDocx('../docx/example_title');