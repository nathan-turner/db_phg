<?php 
require_once("conn.php"); 

if(isset($_POST["submitbtn"]))
{
$nut = $_POST["nut"];
$startdate = $_POST["startdate"];
	$spec = trim(substr($_POST["spec"],0,3));
	$spec = str_replace('-','',$spec);
	$spec = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $spec); //need this to really remove whitespaces
	$skill = trim(substr($_POST["spec"],3)); 
	$skill = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $skill);
	$skill = str_replace('-','',$skill);
	$licsql = "";
	$licsqln = "";
	$stadt = substr($_POST["stat"],0,2);
	$skillsql = "";
	
	if($stadt!="" && $stadt!="--")
	{
		$licsql = " and ph_licenses like '%".$stadt."%'";
		$licsqln = " and an_licenses like '%".$stadt."%'";
	}
	if($_POST["locum"]==1)
	{
		$licsql = $licsql. " and isnull(ph_locums) ";
		$licsqln = $licsqln. " and isnull(an_locums) ";
	}
	if($_POST["pcareer"]==1)
	{
		$licsql = $licsql . " and not exists (select * from tphsourcesn where psr_ph_id = ph_id and psr_source = 'Physician Work')";
		$licsqln = $licsqln . " and not exists (select * from tnusourcesn where nsr_an_id = an_id and nsr_source = 'Physician Work')";
	}
	
	if($skill!="" && $skill !="--")
		$skillsql = " and ph_skill = '". trim($skill) . "'";
	
	$sql = "";
	if($nut!="" && $nut!="0000000000" )
	{
		$sql = "select ctct_name,ctct_email from lstalliednurses join lstcontacts on an_ctct_id = ctct_id where NOT ISNULL(ctct_email)  and ctct_email <> ' ' and an_status = 1 and (Isnull(ctct_bounces) or ctct_bounces<>1) and (Isnull(an_nospeclist) or an_nospeclist<>1) and an_date_add>='".$startdate."'  and an_type = '" . $nut . "'" . $licsqln;
	}
	elseif($spec!='')
	{
		$nut = "0000000000";
		$sql = "select ctct_name,ctct_email from lstphysicians join lstcontacts on ph_ctct_id = ctct_id " .
			" where NOT ISNULL(ctct_email) and ctct_email <> ' ' and ph_status = 1 and " .
			"(Isnull(ctct_bounces) or ctct_bounces<>1) and (Isnull(ph_nospeclist) or ph_nospeclist<>1)  " .
			"and ph_date_add>='".$startdate."' and ph_spec_main = '" . $spec . "'" . $licsql . $skillsql;
	}
	else{
		$nut = "0000000000";
	}
	
	//echo $sql;
	//exit();
	
$result = mysql_query($sql);
				//fetch tha data from the database
				/*while ($row = mysql_fetch_array($result)) {
					$type = trim($row{'contenttype'});
					$size = $row{'filesize'};
					$filename = $row{'filename'};
					$file = $row{'filedirectory'};
				}*/
				
$filename="dbspeclist-".date('Y-m-d').".csv";
if($filename!="" )
{
header( "Content-Type: text/x-csv" );
//header( "Content-Length: " . $size );
header( "Content-Disposition: attachment; filename=\"$filename\"" );
//$file_to_download = $file;//"XML/images/Old Database documentation.docx";//$file;
//readfile($file_to_download);
while ($row = mysql_fetch_array($result)) {
	$cn = $row{'ctct_name'};
	$eml = $row{'ctct_email'};
	echo "\"". $cn ."\" <" . $eml . ">\r\n";
}//end while

}
}
?>