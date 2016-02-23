<?php
/*require '../vendor/autoload.php';

require 'conn2.php';
//Dotenv::load(__DIR__);
$sendgrid_username = 'mfollowell'; //$_ENV['mfollowell'];
$sendgrid_password = 'Phg3356!'; //$_ENV['Phg3356!'];

$userid = $_COOKIE["phguid"];
$id = urldecode($_GET["id"]);
$limit=1000;
//$limit=10;

echo var_dump($_POST);*/
/*
$fh = fopen('dump.log', 'a+');
if ( $fh )
{
// Dump body
//fwrite($fh, print_r("test", true));
//fwrite($fh, print_r($HTTP_RAW_POST_DATA, true));

fclose($fh);
}*/

$file = "dump.log";

//$json = json_decode(file_get_contents($file));

$json = json_decode($HTTP_RAW_POST_DATA);

//echo var_dump($json);

foreach($json as $obj)
{
	if($obj->event == "bounce" || $obj->event == "dropped")
	{
		$em = $obj->email;
		echo $em."<br/>";
		$fh = fopen('dump.log', 'a+');
if ( $fh )
{
// Dump body
fwrite($fh, print_r($em, true));


fclose($fh);
}
	}
	//echo var_dump($obj);
	//echo $obj->email;
}


//echo "ok";


?>
