<?php
// Pull in PHP Simple HTML DOM Parser
include("includes/simple_html_dom.php");

	$page_limit=1000;
	$url = "http://practicewithus.com/opportunities/current-opportunities.dot?sspecialty=all&location=all&type=search";
	
	
	
	// Get the URL's current page content
	//$html = file_get_html($url);
	/*$html = file_get_contents($url);
	
	$html = str_replace( 'perPage = 10;','perPage = '.$page_limit.';',$html);
	$html = str_replace( 'perPage = null;','perPage = '.$page_limit.';',$html);
	$html = str_replace( '"/dwr', '"http://practicewithus.com/dwr',$html);
	//$html = str_replace( '/design', 'http://practicewithus.com/design',$html);
	$html = str_replace( '<option value="50">50</option>', '<option value="1000">1000</option>',$html);
	
	$html = str_replace( 'function searchOpportunitiesResults() {', 'function searchOpportunitiesResults() { alert("test");',$html);
	//perPage = parseInt(numresults.options[numresults.options.selectedIndex].value);
	
	echo $html;*/

	
	// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// grab URL and pass it to the browser
$urlContent = curl_exec($ch);

	$urlContent = str_replace( 'perPage = 10;','perPage = '.$page_limit.';',$urlContent);
	$urlContent = str_replace( 'perPage = null;','perPage = '.$page_limit.';',$urlContent);
	$urlContent = str_replace( '"/dwr', '"http://practicewithus.com/dwr',$urlContent);	
	$urlContent = str_replace( '<option value="50">50</option>', '<option value="1000">1000</option>',$urlContent);
	
// Check if any error occured
if(!curl_errno($ch))
{
   $info = curl_getinfo($ch);
   header('Content-type: '.$info['content_type']);
   echo $urlContent;
}

// close cURL resource, and free up system resources
curl_close($ch);


?>