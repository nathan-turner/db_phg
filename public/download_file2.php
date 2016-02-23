<?php 
require_once("conn.php"); 
$id = urldecode($_GET["id"]);

if($id!="" && $id>0)
{
$result = mysql_query("SELECT cv_ph_id, filesize, contenttype, filename, cv_datemod, filedirectory FROM cvs where cv_id='".$id."' LIMIT 1");
				//fetch tha data from the database
				while ($row = mysql_fetch_array($result)) {
					$type = trim($row{'contenttype'});
					$size = $row{'filesize'};
					$filename = $row{'filename'};
					$file = $row{'filedirectory'};
				}
if($filename!="" && $result)
{
header( "Content-Type: " . $type );
//header( "Content-Length: " . $size );
header( "Content-Disposition: attachment; filename=\"$filename\"" );
$file_to_download = $file;//"XML/images/Old Database documentation.docx";//$file;
readfile($file_to_download);
}
}
?>