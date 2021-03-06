<?php

/**
 * Inserts a comment with HTML content.
 *
 * @category   Phpdocx
 * @package    examples
 * @subpackage intermediate
 * @copyright  Copyright (c) 2009-2013 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2013.08.26
 * @link       http://www.phpdocx.com
 * @since      2013.08.26
 */

require_once('../../classes/CreateDocx.inc');

$docx = new CreateDocx();
$html = '<p>This is some HTML code with a link to <a href="http://www.2mdc.com">2mdc.com</a> and a random image: <img src="image.jpg" width="35px" height="35px" style="vertical-align: -25px"></p>';
$HTMLFragment = $docx->embedHTML($html, array('rawWordML' => true, 'target' => 'comment', 'downloadImages' => true));
$fragment = $docx->createWordMLFragment($HTMLFragment);
$comment = array(
        'textDocument' => 'Another Comment example.',
        'textComment' => $fragment,
        'initials' => 'PT',
        'author' => 'PHPDocX Team',
        'date' => '10 September 2000',
);
$docx->addComment($comment);

$docx->createDocx('../docx/example_HTML_comment');