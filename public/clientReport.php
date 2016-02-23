<?php 
header( "Content-Type: application/msword" );

header( "Content-Disposition: attachment; filename=\"ClientReport_".$id.".doc\"" );
?>
<html>
<head>
<style>
body {  }
table { width: 90%; border: 1px solid black; border-collapse: collapse}
table td { border: 1px solid black; }
.conttable { width: 45%; border: 0px solid black; border-collapse: collapse}
.conttable td { border: 0px solid black; }

@media print {
    .break {page-break-after: always;}
}
</style>
</head>
<body>
<?php
require_once("conn.php"); 
$id = urldecode($_GET["id"]);


if($id!="" && $id>0)
{
	$result = mysql_query("select * from vclient where cli_id = '".$id."' LIMIT 1");
				//fetch tha data from the database
				while ($row = mysql_fetch_array($result)) {
					$ar["cli_id"] = $row{'cli_id'};
					$ar["cli_ctct_id"] = $row{'cli_ctct_id'};
					$ar["ctct_name"] = $row{'ctct_name'};
					$ar["ctct_title"] = $row{'ctct_title'};
					$ar["ctct_company"] = $row{'ctct_company'};
					$ar["ctct_phone"] = $row{'ctct_phone'};
					$ar["ctct_ext1"] = $row{'ctct_ext1'};
					$ar["ctct_fax"] = $row{'ctct_fax'};
					$ar["ctct_ext2"] = $row{'ctct_ext2'};
					$ar["ctct_email"] = $row{'ctct_email'};
					$ar["ctct_addr_1"] = $row{'ctct_addr_1'};
					$ar["ctct_addr_2"] = $row{'ctct_addr_2'};
					$ar["ctct_addr_c"] = $row{'ctct_addr_c'};
					$ar["ctct_addr_z"] = $row{'ctct_addr_z'};
					$ar["ctct_st_code"] = $row{'ctct_st_code'};
					$ar["ctct_url"] = $row{'ctct_url'};
					$ar["cli_sys"] = $row{'cli_sys'};
					$ar["cli_beds"] = $row{'cli_beds'};
					$ar["cli_grp"] = $row{'cli_grp'};
					$ar["st_name"] = $row{'st_name'};
					$ar["cli_status"] = $row{'cli_status'};
					$ar["cli_emp_id"] = $row{'cli_emp_id'};
					$ar["cli_rating"] = $row{'cli_rating'};
					$ar["cs_name"] = $row{'cs_name'};
					$ar["cli_population"] = $row{'cli_population'};
					$ar["cli_specialty"] = $row{'cli_specialty'};
					$ar["cli_fuzion"] = $row{'cli_fuzion'};
					$ar["cli_locumactive"] = $row{'cli_locumactive'};
					$cli_emp_id = $row{"cli_emp_id"};
					/*$ar[""] = $row{''};
					$ar[""] = $row{''};
					$ar[""] = $row{''};
					$ar[""] = $row{''};
					$ar[""] = $row{''};
					$ar[""] = $row{''};
					$ar[""] = $row{''};*/
				}
}

//header( "Content-Type: application/msword" );

//header( "Content-Disposition: attachment; filename=\"ClientReport_".$id.".doc\"" );

//echo "<h1>test</h1>";
//ob_flush();
?>

<h1><?php echo $ar["ctct_name"]; ?></h1>

<table>
<tr><td><strong>ID Number:</strong></td><td><?php echo $ar["cli_id"]; ?></td><td><strong>Status:</strong></td><td><?php echo $ar["cli_status"]; ?></td></tr>
<tr><td><strong>City, State:</strong></td><td><?php echo $ar["ctct_addr_c"].", ".$ar["ctct_st_code"]; ?></td><td><strong>Zip:</strong></td><td><?php echo $ar["ctct_addr_z"]; ?></td></tr>
<tr><td><strong>Address:</strong></td><td colspan="3"><?php echo $ar["ctct_addr_1"]; ?></td></tr>
<tr><td><strong>Main Phone:</strong></td><td><?php echo $ar["ctct_phone"]; ?></td><td>Ext:</td><td><?php echo $ar["ctct_ext1"]; ?></td></tr>
<tr><td><strong>Acctg Fax:</strong></td><td><?php echo $ar["ctct_fax"]; ?></td><td>Ext:</td><td><?php echo $ar["ctct_ext2"]; ?></td></tr>
<tr><td><strong>Website:</strong></td><td colspan="3"><?php echo $ar["ctct_url"]; ?></td></tr>
<tr><td><strong>System:</strong></td><td><?php echo $ar["cli_sys"]; ?></td><td><strong>Beds/Docs:</strong></td><td><?php echo $ar["cli_beds"]; ?></td></tr>
<tr><td><strong>Type:</strong></td><td><?php echo $ar["cli_grp"]; ?></td><td><strong>Population:</strong></td><td><?php echo $ar["cli_population"]; ?></td></tr>
<tr><td><strong>Source:</strong></td><td><?php echo $ar["cs_name"]; ?></td><td><strong>Group Specialties:</strong></td><td><?php echo $ar["cli_specialty"]; ?></td></tr>
<tr><td><strong>Main Email:</strong></td><td colspan="3"><a href="mailto:<?php echo $ar["ctct_email"]; ?>"><?php echo $ar["ctct_email"]; ?></a></td></tr>
</table>


<h2>Contacts:</h2>

<?php

$result = mysql_query("select * from lstcontacts where ctct_type = 5 and ctct_backref = " .$id. " order by ctct_name ");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
		echo "<p>".$row{"ctct_name"}."";
		if($row{"ctct_title"}!='')
			echo ",".$row{"ctct_title"};
		if($row{"ctct_reserved2"}!='')
			echo " (".$row{"ctct_reserved2"}.")" ;
		if($row{"ctct_marketing"}==1 || $row{"ctct_secondary"}==1)
			echo ", <em>MKTG</em> " ;
		if($row{"ctct_recruiting"}==1 )
			echo ", <em>RECR</em> " ;
		if($row{"ctct_phone"}!='')
			echo ", Phone: ".$row{"ctct_phone"};
		if($row{"ctct_cell"}!='')
			echo ", Cell: ".$row{"ctct_cell"};
		if($row{"ctct_fax"}!='')
			echo ", Fax: ".$row{"ctct_fax"};
		if($row{"ctct_email"}!='')
			echo ", Email: <a href='mailto:".$row{"ctct_email"}."'>".$row{"ctct_email"}."</a>";
		
		echo "</p>";		
		//$cli_emp_id = $row{"cli_emp_id"};
}


$result = mysql_query("select ctct_name from vemplist where emp_id = " .$cli_emp_id. "  ");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
	$market = $row{"ctct_name"};
}	
				
$result = mysql_query("select * from tclihotlist where ch_cli_id = " .$id. " and ch_emp_id = " .$cli_emp_id. "");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
	if($row{"ch_pending"}==1)
		$hot[0]="Yes";
	if($row{"ch_hot_prospect"}==1)
		$hot[1]="Yes";
	if($row{"ch_lead_1"}==1)
		$hot[2]="Yes";
	if($row{"ch_lead_2"}==1)
		$hot[3]="Yes";
	if(trim($row{"ch_spec"})!='')
		$hot[4]=$row{"ch_spec"};
		
}

?>
<br/>
<p><strong>Marketer: <?php echo $market; ?>(assigned)</strong></p>
<p>
Pending: <?php echo $hot[0]; ?>&nbsp;&nbsp; Long-Term Pending: <?php echo $hot[1]; ?><br/>
<strong>Pending Specialty</strong>: <?php echo $hot[4]; ?>&nbsp;&nbsp; <br/>
List 1: <?php echo $hot[2]; ?>&nbsp;&nbsp; List 2: <?php echo $hot[3]; ?></p>


<h2>Notes (12 Latest)</h2>
<table>
<tr>
<th>User</th><th>Date</th><th>Notes</th>
</tr>
<?php
$result = mysql_query("select note_user,note_dt,note_text,ctr_no from allnotes left outer join allcontracts on note_reserved = ctr_id where note_type = 2 and note_ref_id = " .$id. " order by note_dt desc LIMIT 12");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
	echo "<tr><td>".$row{"note_user"}."</td><td>".date('Y-m-d',strtotime($row{"note_dt"}))."</td><td>".$row{"note_text"}."</td></tr>";
}
?>
</table>

<br/>
<h2>Contracts</h2>
<table class="conttable">
<?php
$result = mysql_query("select ctr_id,ctr_no,ctr_type,ctr_spec,st_name from vctr4clients where ctr_cli_id = " .$id. " or ctr_cli_bill = " .$id. " order by st_name, ctr_date desc");
//fetch tha data from the database
$i=0; //maybe split into two columns/tables later
while ($row = mysql_fetch_array($result)) {
	echo "<tr><td>".$row{"ctr_no"}." (".$row{"ctr_type"}.")</td><td>".$row{"ctr_spec"}."</td><td>".$row{"st_name"}."</td></tr>";
}
?>
</table>
<p class="break"></p>
<br/>

<?php
$result = mysql_query("select * from vcontracts1 where cli_id = " .$id. " order by ctr_status, ctr_date desc");
//fetch tha data from the database
while ($row = mysql_fetch_array($result)) {
	//echo "<tr><td>".$row{"ctr_no"}." (".$row{"ctr_type"}.")</td><td>".$row{"ctr_spec"}."</td><td>".$row{"st_name"}."</td></tr>";
?>
<h2><?php echo $row{"ctr_no"}." (".$row{"ctr_type"}.")</td><td>".$row{"sp_name"}." - ".$row{"ctr_location_c"}.", ".$row{"ctr_location_s"}; ?></h2>
<table style="width: 60%">
<tr>
<th>Date</th><th>Recruiter</th><th>Amount</th><th>Monthly</th><th>Guarantee</th>
</tr>
<?php echo "<tr><td>".date('Y-m-d',strtotime($row{"ctr_date"}))."</td><td>".$row{"emp_uname"}."</td><td>$".number_format($row{"ctr_amount"},2)."</td><td>".$row{"ctr_monthly"}."</td><td>".$row{"ctr_guarantee"}."</td></tr>"; ?>
</table>	

<h3>Presents / Interviews / Placements</h3>
<table >
<tr>
<th>Doctor ID#</th><th>Name</th><th>Date</th><th>Status</th>
</tr>
<?php
$result2 = mysql_query("select * from vpiplins where pipl_ctr_id = " .$row{"ctr_id"}. " order by pipl_date");
//echo "select * from vpiplins where pipl_ctr_id = " .$id. " order by pipl_date";
//fetch tha data from the database
while ($row2 = mysql_fetch_array($result2)) {
	echo "<tr><td>".$row2{"ph_id"}."</td><td>".$row2{"ctct_name"}.", ".$row2{"ctct_title"}." (".$row2{"ph_spec_main"}.")</td><td>".date('Y-m-d',strtotime($row2{"pipl_date"}))."</td><td>".$row2{"pist_name"};
	if($row2{"pipl_cancel"}==1 )
		echo "<br/>(canceled)";
	echo "</td></tr>";
}
?>
</table>

<h3>Sourcing Responses: <?php echo $row{"ctr_responses"}; ?></h3>

<h3>Direct Mail</h3>

<table style="width: 30%">
<tr><th></th><th>Date</th></tr>
<tr><th>Counts Requested:</th><td><?php if($row{"ctr_src_cnt"}!='' && $row{"ctr_src_dmdate"}!='0000-00-00 00:00:00'){echo date('Y-m-d',strtotime($row{"ctr_src_cnt"}));} ?></td></tr>
<tr><th>Counts Requested:</th><td><?php if($row{"ctr_src_appr"}!='' && $row{"ctr_src_dmdate"}!='0000-00-00 00:00:00'){echo date('Y-m-d',strtotime($row{"ctr_src_appr"}));} ?></td></tr>
<tr><th>List Ordered:</th><td><?php if($row{"ctr_src_list"}!='' && $row{"ctr_src_dmdate"}!='0000-00-00 00:00:00'){echo date('Y-m-d',strtotime($row{"ctr_src_list"}));} ?></td></tr>
<tr><th>Printing Ordered:</th><td><?php if($row{"ctr_src_print"}!='' && $row{"ctr_src_dmdate"}!='0000-00-00 00:00:00'){echo date('Y-m-d',strtotime($row{"ctr_src_print"}));} ?></td></tr>
<tr><th>DM Copy Mailed:</th><td><?php if($row{"ctr_src_dmdate"}!='' && $row{"ctr_src_dmdate"}!='0000-00-00 00:00:00'){echo date('Y-m-d',strtotime($row{"ctr_src_dmdate"})); } ?></td></tr>
</table>

<br/>
<h3>Sourcing History</h3>

<table >
<tr>
<th>Source</th><th>Date Added</th><th>Date Approved</th><th>Status</th>
</tr>
<?php
$sst[0] = "Pending";
$sst[1] = "Active";
$sst[2] = "Inactive";
$sst[3] = "Completed";
$sst[4] = "Canceled";

$result3 = mysql_query("select * from vctrsrchistory where csr_ctr_id = " .$row{"ctr_id"}. " order by csr_add_date");
//fetch tha data from the database
while ($row3 = mysql_fetch_array($result3)) {
	
	if($row3{"csr_appr_date"}!='' && $row3{"csr_appr_date"}!='0000-00-00 00:00:00')
		$appr_date = date('Y-m-d',strtotime($row3{"csr_appr_date"}));
	echo "<tr><td>".$row3{"src_name"}."</td><td>".$row3{"csr_add_date"}."</td><td>".$appr_date."</td><td>".$sst[$row3{"csr_status"}]."</td></tr>";;
	
}
?>
</table>
<p class="break"></p>

<?php
}//end contracts loop
?>


<?php //echo date('F', strtotime(date('Y')."-".$mn."-1")); ?>





</body>
</html>