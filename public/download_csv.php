<?php 
				
$filename="emaillist-".date('Y-m-d').".csv";
if($filename!="" )
{
header( "Content-Type: text/x-csv" );
header( "Content-Disposition: attachment; filename=\"$filename\"" );
//$file_to_download = $file;//"XML/images/Old Database documentation.docx";//$file;
//readfile($file_to_download);
echo $_POST["list"];
}

?>