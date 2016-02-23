<?php
//echo $_SERVER['DOCUMENT_ROOT'];
$path = "New Database";
/*$list = glob($path.'/*');

foreach($list as $key=>$filename)
{
	echo $filename."<br/>";
}*/
require_once("conn.php"); 

$dir=urldecode($_GET["dir"]);
$folder=urldecode($_GET["folder"]);

  
$folder = "../../cvstorage"; //change

function getDirectories($folder){
$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
if (is_dir($folder)) {
    if ($dh = opendir($folder)) {
        while (($file = readdir($dh)) !== false) {
			//if ($file != '.' && $file != '..')
            //echo "filename: ".$file."<br />";
			
			if ($file != '.' && $file != '..' && is_dir($folder."/".$file) ){ 
				//echo "&nbsp;&nbsp; - ".$file."/<br/>";				
				echo '<a href="list_files.php?dir='.urlencode($folder."/".$file).'" target="_blank">'.$folder."/".$file."</a><br />";
				getDirectories($folder."/".$file);	
			}
			
        }
        closedir($dh);
    }
}
}
if($dir=='')
getDirectories($folder);


//$dir = "XML";
function readFolders($dir){
$files_arr = array();
$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {			
			
			if ($file != '.' && $file != '..' && is_dir($dir."/".$file) ){ //if folder								
				//echo $dir."/".$file."<br />";
				readFolders($dir."/".$file);	
			}
			elseif ($file != '.' && $file != '..'){
				//echo "filename: ".$file." - filesize: ".filesize($dir."/".$file)." - filetype: ".finfo_file($finfo, $dir."/".$file)." - dir: ".$dir."<br />";
				$fullpath = $dir."/".$file;
				$searchpath = str_replace('../../cvstorage','',$fullpath);
				$searchpath = str_replace('/A-I','',$searchpath);
				$searchpath = str_replace('/J-R','',$searchpath);
				$searchpath = str_replace('/S-Z','',$searchpath);
				$searchpath = str_replace('/','\\\\\\\\',$searchpath);
				//$sql[]="SELECT ph_cv_url, ph_id FROM lstPhysicians where NOT ISNULL(ph_cv_url)  AND ph_cv_url LIKE '%".$file."' LIMIT 1";
				$sql.=' ph_cv_url LIKE "%'.$searchpath.'" OR';
				/*$result = mysql_query("SELECT ph_cv_url, ph_id FROM lstPhysicians where NOT ISNULL(ph_cv_url)  AND ph_cv_url LIKE '%".$file."' LIMIT 1");
				//fetch tha data from the database
				while ($row = mysql_fetch_array($result)) {
					echo "ID:".$row{'ph_cv_url'}."<br>";
				}*/
				$files_arr[] = array('fullpath'=>$fullpath, 'size'=>filesize($dir."/".$file), 'type'=>finfo_file($finfo, $dir."/".$file), 'name'=>$file);
			}
        }
		$sql = rtrim($sql, "OR");
        closedir($dh);
    }
	return array($files_arr, $sql);
}
}

if($dir!=''){
echo $dir;
$returnval = readFolders($dir);
$sql = $returnval[1];
$files_arr = $returnval[0];
//echo $sql;
//echo "SELECT ph_cv_url, ph_id FROM lstPhysicians where NOT ISNULL(ph_cv_url) AND (".$sql.") ";
/*foreach($sql as $key=>$val)
{
	//echo $val."<br/>";
	
}*/
//$csv = implode(",", $sql);
//echo $csv."<br/>";
//echo "SELECT ph_cv_url, ph_id FROM lstPhysicians where NOT ISNULL(ph_cv_url) AND (".$sql.") ";
//echo var_dump($files_arr);

//$sql2 = "SELECT ph_cv_url, ph_id FROM lstphysicians where NOT ISNULL(ph_cv_url) AND (".$sql.") ";
//echo $sql2;

$result = mysql_query("SELECT ph_cv_url, ph_id FROM lstphysicians where NOT ISNULL(ph_cv_url) AND (".$sql.") ");
				//fetch tha data from the database
				while ($row = mysql_fetch_array($result)) {				
					echo "ID:".$row['ph_cv_url']."<br>";
					//echo $files_arr[0]['name']."<br>";
					//build insert query get filename to compare to array...
					foreach($files_arr as $key=>$val)
					{
						if(strstr($row['ph_cv_url'],$val['name'])!==false){
							//$name=str_replace('(','{',$val['name']);
							//$name=str_replace(')','}',$name);
							echo "TRUE";
							echo $val['name']."<br>";
							echo $row['ph_id']."<br>";
							if($row['ph_id']!="")
								$insertsql .= "('".$row['ph_id']."', '".$val['size']."', '".$val['type']."', \"".$val['name']."\", NOW(), \"".$val['fullpath']."\"),";
						}
					}
				}
	if($insertsql!=""){
		$insertsql= "INSERT IGNORE INTO cvs (cv_ph_id, filesize, contenttype, filename, cv_datemod, filedirectory) VALUES ".rtrim($insertsql, ",");
		$result = mysql_query($insertsql);
		if(!$result)
			echo "there was a problem";		
	}
	echo $insertsql;
}


//close the connection
mysql_close($dbhandle);

?>