<?php 
require_once("conn.php"); 
?>
<table>
<tr>
<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
</tr>
<?php
if(isset($_POST["submit"]))
{

$states=array();
$sql="select st_code,st_name from dctstates";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$st = $row{'st_name'};
	$states[$st] = $row{'st_code'};	
}
//echo var_dump($states);

/*my $st = $RS->{'st_name'}->{Value};
		$st =~ s/ *$//;
		$States{ $st } = $RS->{'st_code'}->{Value};
		$RS->MoveNext;*/

/*
set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include 'PHPExcel/IOFactory.php';
$inputFileName = 'ResumeEmails.xlsx';
try {
    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
    die('Error loading file :' . $e->getMessage());
}

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
print_r($sheetData);*/

//echo $_POST["rawdata"];
$arr = explode(PHP_EOL, $_POST["rawdata"]);
//echo var_dump($arr);
foreach($arr as $key=>$row)
{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	/*echo $ar[0]."<br/>";
	echo $ar[1]."<br/>";
	echo $ar[2]."<br/>";
	echo $ar[3]."<br/>";*/
	
	$bad = 0;
	
	$src_date = $ar[0];
	if(trim($src_date)!=""){
		$src_date = date('Y-m-d', strtotime($src_date));
	}
	else{
		$src_date="NULL";
	}
	
	$cspec = $ar[1];
	$cskill = "NULL";
	if($cspec!="")
	{
	
	}
	else{
		$bad = 2;
	}
	
	$cname = trim($ar[2]);
	if($cname=='' || strpos($cname, "anonymous")!==false){
		$bad = 3;
		$cname = "NULL";
	}
	else{
		//$cname = "NULL";
	}
	
	$status = $ar[3]; //not used
	
	$caddrc = trim($ar[4]);
	if(strpos($caddrc, "Confidential")!==false){
		$bad = 5;
	}
		
	
	$cstate = trim($ar[5]);
	if( $cstate == "Washington D.C." ) { $cstate = 'DC'; }
	else { $cstate = $states[$cstate]; }
	if($cstate==''){
		$bad=6;
		$cstate=="NULL";
	}
	
	$caddrz = trim($ar[6]);
	if($caddrz==''){		
		$caddrz=="NULL";
	}
	
	$country = $ar[7];
	
	//7 not used? $cspec = $ar[7];
	$cphone = trim($ar[8]);
	if($cphone=='')		
		$cphone=="NULL";
	
	$cemail = $ar[9];
	if($cemail=='')		
		$cemail=="NULL";
	
	$note = "";
	if($ar[10]!='')
		$note .= ' Career Level: '.$ar[10];
		
	if($ar[11]!='')
		$note .= ' Primary Specialty: '.$ar[11];
	if($ar[12]!='')	
		$note .= ' Secondary Specialty: '.$ar[12];
	
	
	//start date 13;
	//travel $ar[14];
	//Willing to Relocate (unused) $ar[15];
	if($ar[16]!='')	
		$note .= ' Ideal Locations: '.$ar[16];
	
	$clic =  $ar[17];
	//echo $clic."<br/>";
	$lics = explode(",", $clic);
	$lics2 = array();
	foreach($lics as $key=>$lic){
	//echo $lic."<br/>";
		$lic=trim($lic);
		if( $lic == "Washington D.C." ) { $lic = 'DC'; }
		else { $lic = $states[$lic];}
		//echo $lic."<br/>";
		$lics2[] = $lic;
	}
	$clic = join(',',$lics2);
	if($clic=="")
		$clic="NULL";
	
	/*
	my $clic = $rowq[17]; #	State Licenses: split, translate, join
		   $clic =~ tr(A-Za-z, )( )cd; # Letters and ,
		my @lics = split /, *//*, $clic;
		for my $lic ( @lics ) {
			if( $lic eq "Washington DC" ) { $lic = "DC"; }
			else { $lic = $States{$lic}; }
		}
		$clic = join( ',', @lics );
		   $clic = $clic?"\'$clic\'":"NULL";
	*/
	//echo $clic;
	if(!$bad){
		echo "GOOD";
		
		$sql = "call AddImportPhys( $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $_COOKIE["username"] . "','$ctme',$csub,$csubspec,$cskill,$note )";
		
		

		
		echo $sql;
		
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) {
	
		}
		
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
}


				


}

?>
</table>