<?php
require_once("conn.php");
$basefolder = "../../cvstorage"; //change
//$basefolder = "XML";
//$folder = "xsl";

if(isset($_POST["submit"]) && isset($_FILES["userfile"]))
{
if( is_uploaded_file($_FILES["userfile"]['tmp_name']) ) {
	//echo "submit";
	//echo var_dump($_FILES["userfile"]);
	$name = $_FILES["userfile"]["name"];
	if($_POST["path"]!="")
		$path = $_POST["path"]."/".$name;
	else
		$path = $basefolder."/".$name;
	$uploadfile = "$basefolder/$folder/$name";
	
	if( !move_uploaded_file($_FILES["userfile"]['tmp_name'], $path) ) throw new Exception('Can not save CV File',__LINE__);

	if(isset($_POST["ph_id"]) && $_POST["ph_id"]!='')
	{
	$insertsql="INSERT INTO cvs2 (cv_ph_id, filesize, contenttype, filename, cv_datemod, filedirectory) 
	VALUES ('".$_POST["ph_id"]."', '".$_FILES["userfile"]["size"]."', '".$_FILES["userfile"]["type"]."', '".$name."', NOW(), '".$path."')";
		$result = mysql_query($insertsql);
	}
	if(isset($_POST["an_id"]) && $_POST["an_id"]!='')
	{
	$insertsql="INSERT INTO cvs2 (an_id, filesize, contenttype, filename, cv_datemod, filedirectory) 
	VALUES ('".$_POST["an_id"]."', '".$_FILES["userfile"]["size"]."', '".$_FILES["userfile"]["type"]."', '".$name."', NOW(), '".$path."')";
		$result = mysql_query($insertsql);
	}
	
		if(!$result)
			echo "There was a problem";		
		else
			echo "<p style='color:red'>Successfully Uploaded</p>";
		//echo $insertsql;
}
$_GET["an_id"] = $_POST["an_id"];
$_GET["ph_id"] = $_POST["ph_id"];
}

function getDirectories($folder,$basefolder=''){
$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
if (is_dir($folder)) {
    if ($dh = opendir($folder)) {
        /*while (($file = readdir($dh)) !== false) {						
			if ($file != '.' && $file != '..' && is_dir($folder."/".$file) ){ 
				//echo "&nbsp;&nbsp; - ".$file."/<br/>";				
				echo '<option value="'.$folder."/".$file.'" >'.$folder."/".$file."</option>";
				getDirectories($folder."/".$file);	
			}			
        }
        closedir($dh);*/
		$arr = scandir($folder); //switched to use scan for sorting
		natcasesort($arr);
		foreach($arr as $key=>$file)
		{
			if ($file != '.' && $file != '..' && is_dir($folder."/".$file) ){ 
				//echo "&nbsp;&nbsp; - ".$file."/<br/>";
				/*if($folder==$basefolder)
					echo '<option value="'.$folder."/".$file.'" >'.$file."</option>";	
				else
					echo '<option value="'.$folder."/".$file.'" >'.$folder."/".$file."</option>";*/
				echo '<option value="'.$folder."/".$file.'" >'.str_replace($basefolder,"",$folder)."/".$file."</option>";
				getDirectories($folder."/".$file, $basefolder);	
			}
		}
    }
}
}


?>

<html>
<head>
<title>CV File Upload</title>
</head>
<body>
<h1>CV File Upload</h1>

<form enctype="multipart/form-data" action="upload.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
	<input type="hidden" name="ph_id" value="<?php echo $_GET["ph_id"]; ?>" />
	<input type="hidden" name="an_id" value="<?php echo $_GET["an_id"]; ?>" />
	<select name="path" >
	<option value="">Select subfolder</option>
	<?php echo getDirectories($basefolder,$basefolder); ?>
	</select>(Optional)<br/><br/>
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="userfile" type="file" /><br/>
    <input type="submit" value="Send File" name="submit" />
</form>
</body>
</html>