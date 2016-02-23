<?php
// module/Pinnacle/src/Pinnacle/Model/CalendarTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class CalendarTable extends Report\ReportTable
{

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vphlookupsmall',
            '`ph_id`, `ctct_name`, `ctct_title`, `st_name`, `phone`, `ctct_addr_c`, `ctct_st_code`, `ph_spec_main`, `ph_status`, `ph_recruiter`, `ph_licenses`, `ph_cv_date`, `ph_src_date`, `ph_pref_state`, `ph_pref_region`, `ph_subspec`, `ph_datasrc`, `ph_citizen`, `ph_practice`, `ph_skill`, `ph_locums`, ctct_id');
    }
	
	/*public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Admin\Users());

        $this->initialize();
    }*/
	
	//new get activities
	public function getUserActivities($id,$date='') {         
            $userid = $_COOKIE["phguid"];			
			if($id=='' || $id==0)
				$id=$userid;
			
			if($date=='')
				$date=date('Y-m-d');
			
			if($id==3) //if mike, also show house
				$result = $this->adapter->query("select * from vschedule1 where (trg_id=? OR trg_id=31) and DATE_FORMAT(aact_trg_dt,'%Y-%m-%d') = ? order by trg_time ",  array($id, $date));
			else
				$result = $this->adapter->query("select * from vschedule1 where trg_id=? and DATE_FORMAT(aact_trg_dt,'%Y-%m-%d') = ? order by trg_time ", array($id, $date));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['aact_id']=$row->aact_id;
					$ar[$i]['aact_trg_dt']=$row->aact_trg_dt;
					$ar[$i]['trg_date']=$row->trg_date;
					$ar[$i]['trg_time']=$row->trg_time;
					$ar[$i]['trg_id']=$row->trg_id;
					$ar[$i]['aact_name']=$row->aact_name;
					$ar[$i]['aact_descr']=$row->aact_descr;
					$ar[$i]['act_need_ref']=$row->act_need_ref;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['aact_ref1']=$row->aact_ref1;
					$ar[$i]['aact_ref_type1']=$row->aact_ref_type1;
					$ar[$i]['aact_snoozed']=$row->aact_snoozed;
					$ar[$i]['aact_accepted']=$row->aact_accepted;
					$ar[$i]['ctct_asp']=$row->ctct_asp;
					$ar[$i]['ctct_param']=$row->ctct_param;
					$ar[$i]['aact_shortnote']=$row->aact_shortnote;
					$ar[$i]['aact_act_code']=$row->aact_act_code;
					
					$note = $row->aact_shortnote;
					if(strpos($note, 'http://testdb.phg.com/public/physician/view/')!==false)
					{
						$note = str_replace('http://testdb.phg.com/public/physician/view/','<a target="_blank" href="http://testdb.phg.com/public/physician/view/',$note);
						$note .= '">View</a>';
					}
					
					if($row->aact_accepted==1)
						$status = "&check;";
					if(($row->ctct_asp!='' && $row->ctct_asp!='calendar' )&& ($row->aact_ref1!='0' && $row->aact_ref1!=''))
						$html .="<tr><td>".$status."</td><td>".$row->trg_time."</td><td><a href='/public/".$row->ctct_asp."/view/".$row->aact_ref1."'>".$row->aact_name." ".$row->aact_descr."</a><br/> ".$note."</td><td><a onclick='removeAct(".$row->aact_id.")' href='#'>Delete</a> / <a onclick='editAct(".$row->aact_id.")' href='#'>Edit</a></tr>";
					else
						$html .="<tr><td>".$status."</td><td>".$row->trg_time."</td><td>".$row->aact_name." ".$row->aact_descr."<br/> ".$note."</td><td><a onclick='removeAct(".$row->aact_id.")' href='#'>Delete</a> / <a onclick='editAct(".$row->aact_id.")' href='#'>Edit</a></tr>";
					
					$i+=1;
					
				}
			}
			if($i<=0)
			{
				$html = "<p>There are no activities for this date.</p>"; //."select * from vschedule1 where trg_id=".$id." and DATE_FORMAT(aact_trg_dt,'%Y-%m-%d') = ".$date." order by trg_time "
			}
			else{
				$html = "<table class='acttable'><tr><th>?</th><th>Time</th><th>Note</th><th>Delete?</th></tr>".$html."</table>";
			}
			
			return $html; //$ar;
    }
	
	//new get activities
	public function getMonthActivities($id,$date='') {         
            $userid = $_COOKIE["phguid"];			
			if($id=='' || $id==0)
				$id=$userid;
			
			if($date==''){
				$header_date = date('F, Y');
				$date=date('m');				
			}
			else{
				$header_date = date('F, Y',strtotime($date));
				$date=date('m',strtotime($date));				
			}
			
			if($id==3) //if mike, also show house
				$result = $this->adapter->query("select * from vschedule1 where (trg_id=? OR trg_id=31) and DATE_FORMAT(aact_trg_dt,'%m') = ? and DATE_FORMAT(aact_trg_dt,'%Y')=DATE_FORMAT(NOW(),'%Y')  order by aact_trg_dt asc ",
				array($id, $date));
			else			
				$result = $this->adapter->query("select * from vschedule1 where trg_id=? and DATE_FORMAT(aact_trg_dt,'%m') = ? and DATE_FORMAT(aact_trg_dt,'%Y')=DATE_FORMAT(NOW(),'%Y')  order by aact_trg_dt asc ",
				array($id, $date));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['aact_id']=$row->aact_id;
					$ar[$i]['aact_trg_dt']=$row->aact_trg_dt;
					$ar[$i]['trg_date']=$row->trg_date;
					$ar[$i]['trg_time']=$row->trg_time;
					$ar[$i]['trg_id']=$row->trg_id;
					$ar[$i]['aact_name']=$row->aact_name;
					$ar[$i]['aact_descr']=$row->aact_descr;
					$ar[$i]['act_need_ref']=$row->act_need_ref;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['aact_ref1']=$row->aact_ref1;
					$ar[$i]['aact_ref_type1']=$row->aact_ref_type1;
					$ar[$i]['aact_snoozed']=$row->aact_snoozed;
					$ar[$i]['aact_accepted']=$row->aact_accepted;
					$ar[$i]['ctct_asp']=$row->ctct_asp;
					$ar[$i]['ctct_param']=$row->ctct_param;
					$ar[$i]['aact_shortnote']=$row->aact_shortnote;
					$ar[$i]['aact_act_code']=$row->aact_act_code;
					
					$note = $row->aact_shortnote;
					if(strpos($note, 'http://testdb.phg.com/public/physician/view/')!==false)
					{
						$note = str_replace('http://testdb.phg.com/public/physician/view/','<a target="_blank" href="http://testdb.phg.com/public/physician/view/',$note);
						$note .= '">View</a>';
					}
					
					if($row->aact_accepted==1)
						$status = "&check;";
					if(($row->ctct_asp!='' && $row->ctct_asp!='calendar' )&& ($row->aact_ref1!='0' && $row->aact_ref1!=''))
						$html .="<tr><td>".$status."</td><td>".$row->aact_trg_dt."</td><td><a href='/public/".$row->ctct_asp."/view/".$row->aact_ref1."'>".$row->aact_name." ".$row->aact_descr."</a><br/> ".$note."</td><td><a onclick='removeAct(".$row->aact_id.")' href='#'>Delete</a> / <a onclick='editAct(".$row->aact_id.")' href='#'>Edit</a></tr>";
					else
						$html .="<tr><td>".$status."</td><td>".$row->aact_trg_dt."</td><td>".$note."</td><td><a onclick='removeAct(".$row->aact_id.")' href='#'>Delete</a> / <a onclick='editAct(".$row->aact_id.")' href='#'>Edit</a></tr>";
					$i+=1;
					
				}
			}
			if($i<=0)
			{
				$html = "<p>There are no activities for this month.</p>";
			}
			else{
				$html = "<table class='acttable'><tr><th>?</th><th>Date / Time</th><th>Note</th><th>Delete?</th></tr>".$html."</table>";
			}
			$arr["html"] = $html;
			$arr["date"] = $header_date;
			return $arr; //$ar;
    }
	
	//new get activities
	public function getMonthDates($id,$date='') {         
            $userid = $_COOKIE["phguid"];			
			if($id=='' || $id==0)
				$id=$userid;
			
			if($date==''){
				$header_date = date('F, Y');
				$date=date('m');				
			}
			else{
				$header_date = date('F, Y',strtotime($date));
				$date=date('m',strtotime($date));				
			}
			
			if($id==3) //if mike, also show house
				$result = $this->adapter->query("select DATE_FORMAT(aact_trg_dt, '%Y/%c/%e') as scheddate, aact_shortnote from vschedule1 where (trg_id=? OR trg_id=31) and DATE_FORMAT(aact_trg_dt,'%m') = ? order by trg_time ", array($id, $date));
			else
				$result = $this->adapter->query("select DATE_FORMAT(aact_trg_dt, '%Y/%c/%e') as scheddate, aact_shortnote from vschedule1 where trg_id=? and DATE_FORMAT(aact_trg_dt,'%m') = ? order by trg_time ", array($id, $date));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					/*$ar[$i]['scheddate']=$row->scheddate;			
					$ar[$i]['aact_shortnote']=$row->aact_shortnote;*/
					//$ar[$row->scheddate] = 	$row->aact_shortnote;

					//$arr_string .= "'".$row->scheddate."':'".(json_encode($row->aact_shortnote))."',";
					$arr_string .= "'".$row->scheddate."':'',";
					
					$i+=1;
					
				}
			}
			
			return rtrim($arr_string,","); 
    }
	
	//new get activities
	public function getActivities() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query('select act_code,act_name,act_need_note from dctactivity where act_need_ref=0 and act_hidden=0 ',
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['act_code']=$row->act_code;
					$ar[$i]['act_name']=$row->act_name;
					$ar[$i]['act_need_note']=$row->act_need_note;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	
	public function addActivity($post, $identity) {         
            $userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];			
			$username = $_COOKIE["username"];
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
				$id = $post["id"];
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$post["act_user"]);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					//$note_user=$row->emp_user_mod;
					//$dept=$row->emp_dept;		
					$to_email = $row->emp_uname;	
				}
			}
			
			//echo $post["activ_type"];
			//echo $note_user;
			$activ_type = $post["activ_type"]%1000;
			
			$act_date = $post["schedact_date"]." ".$post["schedact_hrtxt"].":".$post["schedact_mintxt"].":00";
			$result = $this->adapter->query('call AddAnActivity (?,?,?,?,?,?,?,?,?) '
			,
            array(
                $username, //user_mod, 
				$activ_type, 
				$userid, //src_emp, 
				$post["act_user"], //, //trg_emp, 
				$act_date,	//trg_date, 
				0, //$post["act_ref"], //db_ref, 0 for default calendar action
				0, //$post["act_ret"], //ref_type, 0 default
				$post["sched_act_notes"], //notes, 
				$post["act_ct"]
				               
			));
			
			
			
			$hour = sprintf('%02d', ltrim($post["schedact_hrtxt"],'0')+5); //fix 3 hours behind...
			$startdate = str_replace("-","",$post["schedact_date"])."T".$hour.$post["schedact_mintxt"]."00Z";
			//echo $startdate;
			
			$message="BEGIN:VCALENDAR
CALSCALE:GREGORIAN
METHOD:REQUEST
PRODID:Microsoft Exchange Server 2010
VERSION:2.0
BEGIN:VEVENT
DTSTART:".$startdate."
DTEND:".$startdate."
DTSTAMP:".date('Ymd')."T".date('his')."Z
ORGANIZER;CN=".$realname.":mailto:".$useremail."
UID:".rand (1000000, 9999999)."
ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= TRUE;CN=".$realname.":mailto:".$useremail."
DESCRIPTION: ".$post["sched_act_notes"]." 
LOCATION: US
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:".$post["activ_type"]." Scheduled
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR";

/*Setting the header part, this is important */
$headers = "From:info@phg.com\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/calendar; method=REQUEST;\n";
$headers .= '        charset="UTF-8"';
$headers .= "\n";
$headers .= "Content-Transfer-Encoding: 7bit";

/*mail content , attaching the ics detail in the mail as content*/
$subject = "[DB] - Scheduling Notification from ".$realname;
$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

//echo $subject;
/*mail send*/
//$useremail='nturner@phg.com';
mail($to_email, $subject, $message, $headers);
				
			return true;
    }
	
	
	public function removeActivity($id) {         
            $userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];			
			$username = $_COOKIE["username"];
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
				
			if($id!='' && $id>=0){
			
				$result = $this->adapter->query('DELETE FROM allactivities WHERE aact_id=? LIMIT 1', array($id));
			}
			return true;
	}
	
	public function editActivity($id) {         
            $userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];			
			$username = $_COOKIE["username"];
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
				
			if($id!='' && $id>=0){
			
				$result = $this->adapter->query("SELECT *, DATE_FORMAT(aact_trg_dt, '%m/%d/%Y') AS tdate2, DATE_FORMAT(aact_trg_dt, '%Y-%m-%d') AS tdate, DATE_FORMAT(aact_trg_dt, '%H') AS hour, DATE_FORMAT(aact_trg_dt, '%i') AS min FROM allactivities WHERE aact_id=? LIMIT 1", array($id));
				foreach ($result as $row) {
					$ar['hour']=$row->hour;
					$ar['min']=$row->min;
					$ar['tdate']=$row->tdate;
					$ar['tdate2']=$row->tdate2;
					$ar['aact_act_code']=$row->aact_act_code; //activ_type
					$ar['act_need_note']=$row->act_need_note;
					$ar['aact_shortnote']=$row->aact_shortnote;
					$ar['trg_id']=$row->aact_src_emp_id; //user
					
					
				}
			}
			return $ar;
	}
	
	//updates activity
	public function saveActivity($post, $identity) {         
            $userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];			
			$username = $_COOKIE["username"];
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			$id=$post["act_id"];	
			if($id!='' && $id>=0){
				
			if($post["activ_type"]=="")
				$post["activ_type"]=1001;
			
			$act_date = $post["schedact_date"]." ".$post["schedact_hrtxt"].":".$post["schedact_mintxt"].":00";
			
			/*$result = $this->adapter->query('update allactivities SET aact_date_mod = NOW(),aact_user_mod =?,
			aact_src_emp_id =?, aact_trg_emp_id=?, aact_trg_dt=?,
			aact_ref1=?, aact_ref_type1=?, aact_shortnote=?, aact_ctct_id=? WHERE aact_id=? LIMIT 1'
			,
            array(
                $username, //user_mod, 
				//$post["activ_type"], //removed activity type
				$userid, //src_emp, 
				$post["act_user"], //, //trg_emp, 
				$act_date,	//trg_date, 
				0, //$post["act_ref"], //db_ref, 0 for default calendar action //**removed
				0, //$post["act_ret"], //ref_type, 0 default
				$post["sched_act_notes"], //notes, 
				$post["act_ct"],
				$id
				               
			));*/
			$result = $this->adapter->query('update allactivities SET aact_date_mod = NOW(),aact_user_mod =?,
			aact_src_emp_id =?, aact_trg_emp_id=?, aact_trg_dt=?,
			aact_shortnote=? WHERE aact_id=? LIMIT 1'
			,
            array(
                $username, //user_mod, 
				//$post["activ_type"], //removed activity type
				$userid, //src_emp, 
				$post["act_user"], //, //trg_emp, 
				$act_date,	//trg_date, 
				//0, //$post["act_ref"], //db_ref, 0 for default calendar action //**removed
				//0, //$post["act_ret"], //ref_type, 0 default
				$post["sched_act_notes"], //notes, 
				//$post["act_ct"],//removed
				$id
				               
			));
			}
			return true;;
	}
	
		public function getHotLists($market, $filter) {
           
			$sempid = $_COOKIE["phguid"];
			if($_COOKIE["dept"]=="A")
				$allctr = true;		

		if($market==true){ 
	
	$table='<table><thead><TR ALIGN="center">
		<TD><B>City, State</B></TD>
		<TD><B>Facility</B></TD>
		</TR></thead>
		<tbody>';


			$sql = "select * from vcalaccmarketer where ";
			
			if($sempid>0)
			{
				$sql .= "ch_emp_id = " . $sempid . " ";
			}
		
		switch($filter){
			case "p":
				$sql .=  " AND ch_pending = 1 ";
				$vis[1]="(selected)";
				break;
			case "h":
				$sql .=  " AND ch_hot_prospect = 1";
				$vis[2]="(selected)";
				break;
			case "1":
				$sql .=  "AND ch_lead_1 = 1";
				$vis[3]="(selected)";
				break;
			case "2":
				$sql .=  "AND ch_lead_2 = 1";
				$vis[3]="(selected)";
				break;
			default:
				$sql .=  "AND ch_pending = 1";
				$vis[1]="(selected)";
				break;
		}
		
			$sql .= " order by ctct_st_code, ctct_addr_c";
		$prevu="";
		//$table="";
			//echo $sql;
			
			$result = $this->adapter->query($sql, array());
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					
					$table.='<tr><td>'.$row->ctct_addr_c.', '.$row->ctct_st_code.'</td><td><a href="/public/client/view/'.$row->cli_id.'" target="_parent">'.$row->ctct_name.'</a></td></tr>';

				}//end foreach
			}
			
			$table.=' </tbody></table>';
		
		$links = '<a href="?filter=p">Show My Pendings</a> '.$vis[1].'<br/>
<a href="?filter=h">Show My Long-term Pendings</a> '.$vis[2].'<br/>
<a href="?filter=1">Show My List 1</a> '.$vis[3].'<br/>
<a href="?filter=2">Show My List 2</a> '.$vis[4].'<br/>';
		}//end if market	
		else //recruiting
		{
			$sql = "select * from vcalaccrecruiter";
			$flt2 = true;
			/*if($sempid>0)
			{
				$sql .= "ch_emp_id = " . $sempid . " ";
			}*/
		
		switch($filter){
			case "p":
				$sql = "select * from vrecpendings where phh_emp_id = " . $sempid . "  union select * from vrecpendings3 where phh_emp_id = " . $sempid . "  ORDER BY emp_uname, ctct_name, ph_id";
				$vis[1]="(selected)";
				break;
			case "h":
				$sql .=  "2 where phh_emp_id = " . $sempid . " AND phh_hot_doc = 1";
				$vis[2]="(selected)";
				break;
			case "1":
				$sql .=  "2 where phh_emp_id = " . $sempid . " AND phh_lead_1 = 1";
				$vis[3]="(selected)";
				break;
			case "2":
				$sql .=  "2 where phh_emp_id = " . $sempid . " AND phh_lead_2 = 1";
				$vis[4]="(selected)";
				break;
			case "s":
				$sql =  "select * from vphpassesrpt where emp_id = " . $sempid . " union select * from vanpassesrpt where emp_id = " . $sempid . " order by date_ desc";
				$vis[5]="(selected)";
				$flt2 = false;
                $flt3 = true;
				break;
			default:
				$sql .=  " where ctr_type <> 'CP' ";
				if(!$allctr)
					$sql .=  " and (ctr_recruiter = " . $sempid . " or ctr_manager = " . $sempid . ")";
				$vis[0]="(selected)";
				$sql.=" order by ctr_spec, ctct_st_code";
				$flt2 = false;
                $flt3 = false;
				break;
		}
		
			
		$table="<table><thead><tr>";
		
		if($flt2){
			$table.="<th>Name</th><th>Spec</th>";
		}
		elseif($flt3){
			$table.="<th>Name</th><th>Spec</th><th>Pass Date</th><th>To</th>";
		}
		else{
			$table.="<th>Spec.</th><th>City, State</th><th>Contract #</th>";
		}
		
		$table.="</tr></thead><tbody>";
		
			//echo $sql;
			
			$result = $this->adapter->query($sql, array());
			
			if($result)
			{			
				foreach ($result as $row) {
				
					if($flt2){
						//if($row->at_abbr!='')
						if($row->at_abbr!='' && $row->at_abbr!='DOC' )
							$link='midlevel';
						else
							$link='physician';
						$table.='<tr>
						<td><a href="/public/'.$link.'/view/'.$row->ph_id.'" target="phgbott">'.$row->ctct_name.'</a></td><td>';
						if($row->at_abbr!='')
							$table.=$row->at_abbr;
						else
							$table.=$row->ph_spec_main;
						$table.='</td></tr>';

					}
					elseif($flt3){
						if($row->at_abbr!='' && $row->at_abbr!='DOC' )
							$link='midlevel';
						else
							$link='physician';
						$table.='<tr>
						<td><a href="/public/'.$link.'/view/'.$row->ph_id.'" target="phgbott">'.$row->physician.'</a></td>
						<td>';
						if($row->at_abbr!='')
							$table.=$row->at_abbr;
						else
							$table.=$row->ph_spec_main;
						$table.='</td>
						<td>'.date('Y-m-d',strtotime($row->date_)).'</td>
						<td>'.str_replace('@phg.com','',$row->rec_to).'</td>
						</tr>';
						//$table.="<th>Name</th><th>Spec</th><th>Pass Date</th><th>To</th>";
					}
					else{
						$table.='<tr>
						<td>';
						if($row->ctr_nurse==1)
							$table.= $row->at_abbr;
						else
							$table.= $row->ctr_spec;
						$table.='</td><td>'.$row->ctct_addr_c.',  '.$row->ctct_st_code.'</td>';
						$table.='<td><a href="/public/contract/view/'.$row->ctr_id.'" target="_parent">'.$row->ctr_no.'</a></td></tr>';					
						
					}
					
					//$table.='<tr><td>'.$row->ctct_addr_c.', '.$row->ctct_st_code.'</td><td><a href="/public/client/view/'.$row->cli_id.'">'.$row->ctct_name.'</a></td></tr>';

				}//end foreach
				
				$links = '<a href="">Show My Contracts</a> '.$vis[0].'<br/>
<a href="?filter=p">Show My Pendings</a> '.$vis[1].'<br/>
<a href="?filter=h">Show My Long-term Pendings</a> '.$vis[2].'<br/>
<a href="?filter=1">Show My List 1</a> '.$vis[3].'<br/>
<a href="?filter=2">Show My List 2</a> '.$vis[4].'<br/>
<a href="?filter=s">Show My Passes</a> '.$vis[5].'<br/>';

			}
			
			
			$table.="</tbody></table>";
		}//recruiting
			return $table.$links;
    }//end function
	
	
	
    
}
