<?php 
require_once("conn.php"); 

if(isset($_POST["submitbtn"]))
{
$sus = $_POST["sys"];   
	$spec = trim(substr($_POST["spec"],0,3));	
	$spec = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $spec); //need this to really remove whitespaces
	
if($spec!="")
{
	$statstr = "and cli_status in (";
	$statn = "";
	$ctrjoin = "";
	$statoth = "";
	$statqq = "";
	
	//$emp_id = $_COOKIE["phguid"];
	$emp_id = $_POST["emp_id"];
	if($emp_id>0)
	{
		$sempid=$emp_id;	
	}
	else{
		$sempid=0;
		if($_POST["stat"]!="")
		{
			$statqq = " and ctct_st_code = '".$_POST["stat"]."' ";
			$statn =  $statn .$_POST["stat"]. "-";
		}		
	}
	if($_POST["stactive"]=="1")
	{
		$statstr = $statstr . "1,";
		$statn =  statn . "A";
		$ctrjoin = " exists (select * from allcontracts where ctr_status = 1 and ctr_type not in ('S','C','CP','A') and (cli_id = ctr_cli_id or cli_id = ctr_cli_bill)) ";
	
	}
	if($_POST["stpast"]=="1")
	{
		$statstr = $statstr . "10,";
		$statn = $statn . "P";
		$sn = " exists (select * from allcontracts where ctr_status <> 1 and ctr_type not in ('S','C','CP','A') and (cli_id = ctr_cli_id or cli_id = ctr_cli_bill)) ";
		if( $ctrjoin == "" ){ $ctrjoin = $sn; } else { $ctrjoin = $ctrjoin . " or " . $sn; }
	
	}
	if($_POST["stother"]=="1")
	{
		$statstr = $statstr . "0,";
		$statn = $statn . "O";	
	}
	if($_POST["stfuzion"]=="1")
	{
		$fujoin = " exists (select * from allfuzion where cli_fuzion = fu_id and fu_status = 1) ";
		if ($ctrjoin == "") { $ctrjoin = $fujoin; } else { $ctrjoin = $ctrjoin . " or " . $fujoin; }
		$statstr = $statstr . "0,1,10,";
		$statn = $statn . "F";	
	}
	if($_POST["stfupast"]=="1")
	{
		$fujoin = " exists (select * from allfuzion where cli_fuzion = fu_id and fu_status <> 1) ";
		if( $ctrjoin == "") { $ctrjoin = $fujoin; } else { $ctrjoin = $ctrjoin . " or " . $fujoin; }
		$statstr = $statstr . "0,1,10,";
		$statn = $statn . "f";	
	}
	if($_POST["stall"]=="1")
	{
		$statstr = $statstr . "0,1,10,";
		$statn = "ALL";
		$ctrjoin = "";	
	}
	elseif($_POST["ctct7"]=="1")
	{
		$statstr = $statstr . "0,1,10,";
		$statn = $statn . "S";
		$cj = " cli_type=4 ";
		if ($ctrjoin == ""){ $ctrjoin = $cj; } else { $ctrjoin = "(" . $ctrjoin . ") and " . $cj; }		
	}
	else
	{
		$statn = $statn . "S";
		$cj = " cli_type<>4 ";
		if ($ctrjoin == ""){ $ctrjoin = $cj; } else { $ctrjoin = "(" . $ctrjoin . ") and " . $cj; }
	}
				
	if($ctrjoin!="")
	{	
		$ctrjoin = " and (" . $ctrjoin . ") ";
	}
	$statstr = $statstr . "99)"; // ' none by default 
	if( $sempid > 0 )
		$statstr = $statstr . " and cli_emp_id=" . $sempid;		
	if($sus!="")
		$statstr = $statstr . " and cli_sys like '%".$sus."%'";
	$ctctj = "";
	$ctctn = 0;
	if($_POST["ctct1"]=="1")
	{
		$statn = $statn . "M";
		$ctctn = $ctctn + 1;
		$ctctj = " ctct_marketing=1 ";
	}
	if($_POST["ctct2"]=="1")
	{
		$statn = $statn . "m";
		$ctctn = $ctctn + 1;
		$cj = " ctct_secondary=1 ";
		if ($ctctj == "") 
			$ctctj = $cj;
		else 
			$ctctj = $ctctj . " or " . $cj;
	}
	if($_POST["ctct3"]=="1")
	{
		$statn = $statn . "Z";
		$ctctn = $ctctn + 1;
		$cj = " ctct_fuzion1=1 ";
		if ($ctctj == "")
			$ctctj = $cj;
		else 
			$ctctj = $ctctj . " or " . $cj;
	}
	if($_POST["ctct4"]=="1")
	{
		$statn = $statn . "z";
		$ctctn = $ctctn + 1;
		$cj = " ctct_fuzion2=1 ";
		if ($ctctj == "")
			$ctctj = $cj;
		else 
			$ctctj = $ctctj . " or " . $cj;
	}
	if($_POST["ctct6"]=="1")
	{
		$statn = $statn . "R";
		$ctctn = $ctctn + 1;
		$cj = " ctct_recruiting=1 ";
		if ($ctctj == "")
			$ctctj = $cj;
		else 
			$ctctj = $ctctj . " or " . $cj;
	}
	if($_POST["ctct5"]=="1")
	{
		$ctctn = $ctctn + 1;
		$ctctj = "";
	}	
	$campstr = "";	
	if($_POST["camp"]=="2")			
		$campstr = "2";	
	if($_POST["camp"]=="3")			
		$campstr = "3";		
	if($ctctj!="" && $ctctn!=6)
		$statstr = $statstr . " and (".$ctctj.")";	
		
	$sql = "select ctct_email as ema, max(ctct_name) as cna from lstcontacts join lstclients on (ctct_type in (2,5) and ctct_backref = cli_id) " .
			" where NOT ISNULL(ctct_email) and ctct_email <> ' ' and " .
			"Isnull(ctct_bounces)  and Isnull(ctct_unsub" . $campstr . ")  " . $ctrjoin . $statstr . $statqq . " group by ctct_email"	;

}
	
	
	//echo $sql;
	
	
$result = mysql_query($sql);//fetch tha data from the database

				
$filename="clientlist-".date('Y-m-d').".csv";
if($filename!="" && $result)
{
header( "Content-Type: text/x-csv" );
header( "Content-Disposition: attachment; filename=\"$filename\"" );
//$file_to_download = $file;//"XML/images/Old Database documentation.docx";//$file;
//readfile($file_to_download);
while ($row = mysql_fetch_array($result)) {
	$cn = $row{'cna'};
	$eml = $row{'ema'};
	echo "\"". $cn ."\" <" . $eml . ">\r\n";
}//end while

}
}
?>