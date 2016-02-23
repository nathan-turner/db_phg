<?php
// module/Pinnacle/src/Pinnacle/Model/Report/ReportingTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ReportingTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vplacmarketrpt2',
                            '`pl_id`, `pl_date`, `ctr_id`, `ctr_no`, `ctr_date`, `ctr_spec`, `st_name`, `ctr_location_c`, `ctr_location_s`, `cli_sys`, `mark_uname`, `mark_id`, `emp_uname`, `ctct_name`, `ctct_phone`, `cli_id`, `s2pl`, `pl_annual`, `pl_split_emp`, `split_uname`, `ctr_nurse`, `at_abbr`');
    }

    /**
     * @param array $ar     keys: date1, date2, spec (0=All,'---'=Midlevels), st_code (0=All)
    */
    public function fetchAll(array $ar = null) {
        $select = new Select($this->table);
        if( is_array($ar) ) {
            $where = new Where();
            if( $ar['spec'] === '---' ) $where->equalTo('ctr_nurse',1);
            elseif( $ar['spec'] ) $where->equalTo('ctr_spec',$ar['spec']);
            
            if( $ar['st_code'] ) $where->equalTo('ctr_location_s', $ar['st_code'] );
            
            if( $ar['date1'] && $ar['date2'] )
                $where->between('pl_date',$ar['date1'],$ar['date2']);
            elseif( $ar['date1'] ) $where->greaterThanOrEqualTo('pl_date',$ar['date1']);
            elseif( $ar['date2'] ) $where->lessThanOrEqualTo('pl_date',$ar['date2']);
            
            $select->where($where);
        }
        $select->order('ctr_location_s, ctct_name, ctr_spec, pl_date DESC');
        $resultSet = $this->selectWith($select);
        return $resultSet;
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
	
	//new get placement report
	/*public function getPlacementReport($pl_id) {         
            $userid = $_COOKIE["phguid"];			
			//get is nurse
			$result = $this->adapter->query('select pipl_nurse from tctrpipl where pipl_id = ? ',
            array($pl_id));
			if($result)
			{			
				foreach ($result as $row) {
					$pipl_nurse=$row->pipl_nurse;				
				}
			}
			
			if($pipl_nurse==1 || $pipl_nurse=="1")
				$result = $this->adapter->query('select * from vplacereport3 where pl_id = ? ', array($pl_id));
			else
				$result = $this->adapter->query('select * from vplacereport where pl_id =  ? ', array($pl_id));
			$ar = array();
			$ar["pipl_nurse"]=$pipl_nurse;
			
			if($result)
			{
			
				foreach ($result as $row) {
					$ar['req_name']=$row->req_name;
					$ar['cli_name']=$row->cli_name;
					$ar['cli_city']=$row->cli_city;
					$ar['cli_state']=$row->cli_state;
					$ar['ctr_no']=$row->ctr_no;
					$ar['sp_name']=$row->sp_name;
					$ar['ctr_pro_date']=$row->ctr_pro_date;
					$ar['pl_date']=$row->pl_date;
					$ar['s2pl']=$row->s2pl;
					$ar['ph_name']=$row->ph_name;
					$ar['ph_city']=$row->ph_city;
					$ar['ph_state']=$row->ph_state;
					$ar['ph_DOB']=$row->ph_DOB;
					$ar['ph_sex']=$row->ph_sex;
					$ar['ph_citizen']=$row->ph_citizen;
					$ar['ph_spm_bc']=$row->ph_spm_bc;
					$ar['ph_md']=$row->ph_md;
					$ar['pl_term']=$row->pl_term;
					$ar['pl_annual']=$row->pl_annual;
					$ar['pl_guar_net']=$row->pl_guar_net;
					$ar['pl_guar_gross']=$row->pl_guar_gross;
					$ar['pl_guar']=$row->pl_guar;
					$ar['pl_incent']=$row->pl_incent;
					$ar['pl_met_coll']=$row->pl_met_coll;
					$ar['pl_met_pro']=$row->pl_met_pro;
					$ar['pl_met_num']=$row->pl_met_num;
					$ar['pl_met_oth']=$row->pl_met_oth;
					$ar['pl_partner']=$row->pl_partner;
					$ar['pl_partner_yrs']=$row->pl_partner_yrs;
					$ar['pl_buyin']=$row->pl_buyin;
					$ar['pl_based_ass']=$row->pl_based_ass;
					$ar['pl_based_rec']=$row->pl_based_rec;
					$ar['pl_based_sto']=$row->pl_based_sto;
					$ar['pl_based_oth']=$row->pl_based_oth;
					$ar['pl_loan']=$row->pl_loan;
					$ar['pl_vacat']=$row->pl_vacat;
					$ar['pl_cme_wks']=$row->pl_cme_wks;
					$ar['pl_cme']=$row->pl_cme;
					$ar['pl_reloc']=$row->pl_reloc;
					$ar['pl_health']=$row->pl_health;
					$ar['pl_dental']=$row->pl_dental;
					$ar['pl_fam_health']=$row->pl_fam_health;
					$ar['pl_signing']=$row->pl_signing;
					$ar['pl_fam_dental']=$row->pl_fam_dental;
					$ar['pl_st_dis']=$row->pl_st_dis;
					$ar['pl_lt_dis']=$row->pl_lt_dis;
					$ar['pl_oth_ben']=$row->pl_oth_ben;
					$ar['pl_replacement']=$row->pl_replacement;
					$ar['ref_name']=$row->ref_name;
					$ar['pl_ref_emp']=$row->pl_ref_emp;
					$ar['pl_source']=$row->pl_source;
					$ar['ph_id']=$row->ph_id;
					$ar['cli_population']=$row->cli_population;
					$ar['cli_type']=$row->cli_type;
					$ar['ct_name']=$row->ct_name;
					$ar['pl_exp_years']=$row->pl_exp_years;
					$ar['split_name']=$row->split_name;
					$ar['pl_split_emp']=$row->pl_split_emp;
					$ar['ctr_cli_bill']=$row->ctr_cli_bill;
					$ar['cli_id']=$row->cli_id;
					$ar['src_name']=$row->src_name;
					$ar['pl_text1']=$row->pl_text1;
					$ar['pl_text2']=$row->pl_text2;
					$ar['pl_text3']=$row->pl_text3;
					$ar['pl_text4']=$row->pl_text4;										
					
				}
			}
			
			return $ar;
    }*/
	
	//new get calls for usersumst report
	public function getCalls() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("select call_emp_id, sum(call_numin+call_numout) as sumout, avg(call_timein+call_timeout) as timout from tcalls where (MONTH(call_date)=MONTH(NOW()) AND YEAR(call_date)=YEAR(NOW())) and WEEKDAY(call_date) not in (5,6) group by call_emp_id",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['call_emp_id']=$row->call_emp_id;
					$ar[$i]['sumout']=$row->sumout;
					$ar[$i]['timout']=$row->timout;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get calls for usersumst report
	public function getGoals() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("call GetGoalsActualsNew (DATE_FORMAT(NOW(),'%Y-01-01'),NOW())",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['category']=$row->category;
					$ar[$i]['uname']=$row->uname;
					$ar[$i]['emp_id']=$row->emp_id;
					$ar[$i]['goal1']=$row->goal1;
					$ar[$i]['goal2']=$row->goal2;
					$ar[$i]['goal3']=$row->goal3;
					$ar[$i]['goal4']=$row->goal4;
					$ar[$i]['act1']=$row->act1;
					$ar[$i]['act2']=$row->act2;
					$ar[$i]['act3']=$row->act3;
					$ar[$i]['act4']=$row->act4;
					$ar[$i]['moal1']=$row->moal1;
					$ar[$i]['moal2']=$row->moal2;
					$ar[$i]['moal3']=$row->moal3;
					$ar[$i]['moal4']=$row->moal4;
					$ar[$i]['mact1']=$row->mact1;
					$ar[$i]['mact2']=$row->mact2;
					$ar[$i]['mact3']=$row->mact3;
					$ar[$i]['mact4']=$row->mact4;
					$ar[$i]['yoal1']=$row->yoal1;
					$ar[$i]['yoal2']=$row->yoal2;
					$ar[$i]['yoal3']=$row->yoal3;
					$ar[$i]['yoal4']=$row->yoal4;
					$ar[$i]['emp_status']=$row->emp_status;
					
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get goals for usersumst report
	public function getGoalsTotal() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("call GetGoalsTotal (DATE_FORMAT(NOW(),'%Y-01-01'),NOW())",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['mon']=$row->mon;
					$ar[$i]['utype']=$row->utype;
					$ar[$i]['v1']=$row->v1;
					$ar[$i]['v2']=$row->v2;										
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getDataEntryStats($post) {         
            $userid = $_COOKIE["phguid"];			
			
			if($post["enddate"]=="" || $post["startdate"]=="")
			{
				$start  = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));	
				$end = time();
				
				$post["enddate"]=date('Y-m-d', $end);
				$post["startdate"]=date('Y-m-d', $start);
			}
			
			//echo $post["enddate"];
			
			$result = $this->adapter->query("call DataEntryStats (?,?)",
            array($post["startdate"],$post["enddate"]));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['notes']=$row->notes;
					$ar[$i]['cli_add']=$row->cli_add;
					$ar[$i]['cli_mod']=$row->cli_mod;
					$ar[$i]['ph_add']=$row->ph_add;					
					$ar[$i]['ph_mod']=$row->ph_mod;
					$ar[$i]['ctct_mod']=$row->ctct_mod;
					$ar[$i]['an_add']=$row->an_add;
					$ar[$i]['an_mod']=$row->an_mod;	
					$ar[$i]['src_mod']=$row->src_mod;					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getFuzionExportList($version) {         
            $userid = $_COOKIE["phguid"];			
			
			
			//echo $post["startdate"];
			
			$view="vfuzionexport";
			if($version!=''&& $version=='no_email')
				$view="vfuzionexportv3";
			elseif($version!='')
				$view="vfuzionexportv2";
			
			//echo "select * from ".$view." order by ph_date_add desc, ctct_name limit 20";
			$result = $this->adapter->query("select * from ".$view." order by ph_date_add desc, ctct_name limit 20",  array());
			//$result = $this->adapter->query("select * from lstphysicians order by ph_date_add desc limit 8",  array());
			
			///change query back on test db - too slow on my pc!!!!!!!!!!
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ph_id']=$row->ph_id;
					$ar[$i]['ph_spec_main']=$row->ph_spec_main;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ph_date_add']=$row->ph_date_add;
								
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function updateExportList($post) {         
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
			$username = str_replace('@yahoo.com', '', $username);
			$username = str_replace('@gmail.com', '', $username);
			$username = str_replace('@hotmail.com', '', $username);
			
			//echo $post["startdate"];
			
			foreach ($post["wh"] as $key=>$val)
			{
				//echo $val."-<br/>";
				$id = $val;
				$reason = $post["sel_".$id];
				$other = $post["oth_".$id];
				
				//echo "insert into tmp4 (phid, reason, other) values ('".$id."','".$reason."','".$other."')";
				$result = $this->adapter->query("insert into tmp4 (phid, date_add, reason, other, encoder) values (?, NOW(), ?,?,?) ",  array($id,$reason,$other,$username));
			}				
			
			return true;
    }
	
	public function getAccountSummary($post) {         
            $userid = $_COOKIE["phguid"];			
			
			$req = trim($post["req"]);
			$sSpec=substr($post["spec"],0,3);
			if($sSpec=="---")
				 $sSpec = "ctr_nurse = 1 ";
			elseif (trim($sSpec)!='')
				$sSpec = " ctr_spec = '".$sSpec."' ";
				
			if($req!=""){
				$sReq = "ctr_recruiter = '".$req."' ";
				if($sSpec!="")
					$sReq = " and ".$sReq;
			}
			
			if($sSpec.$sReq!="" )
				$sSpec = " where ".$sSpec.$sReq;
			
				//if sSpec & sReq <> "" then sSpec = " where " & sSpec & sReq
			
			//CHANGE BACK TO THIS ONE
			$result = $this->adapter->query("select * from vctraccountsummary" . $sSpec . " union select * from vctraccountsummary3" . $sSpec . " ORDER BY x, cli_city, cli_state, ctr_no, ph_name",  array());
			
			//$result = $this->adapter->query("select * from vctraccountsummary" . $sSpec . "  ORDER BY x, cli_city, cli_state, ctr_no, ph_name",  array());
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_date']=$row->ctr_date;
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['ctr_recruiter']=$row->ctr_recruiter;
					$ar[$i]['ctr_guarantee']=$row->ctr_guarantee;
					$ar[$i]['ctr_pro_date']=$row->ctr_pro_date;
					$ar[$i]['cli_name']=$row->cli_name;
					$ar[$i]['cli_city']=$row->cli_city;
					$ar[$i]['cli_state']=$row->cli_state;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['pipl_status']=$row->pipl_status;
					$ar[$i]['ph_name']=$row->ph_name;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['ph_id']=$row->ph_id;
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['ctr_type']=$row->ctr_type;
					$ar[$i]['x']=$row->x;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['pipl_nurse']=$row->pipl_nurse;
					$ar[$i]['at_abbr']=$row->at_abbr;
								
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getPresentsReport($post) {         
            $userid = $_COOKIE["phguid"];			
			
			$mon = trim($post["mon"]);
			if($mon=='')
				$mon = date('m');
				
			$year = trim($post["year"]);
			if($year=='')
				$year = date('Y');
			
			$start = mktime(0, 0, 0, $mon  , 1, $year);
			
			$end = mktime(0, 0, 0, $mon+1  , 0, $year);
			
			$start = date('Y-m-d',$start);
			$end = date('Y-m-d',$end);
			
			//echo $end;
			
			//CHANGE BACK TO THIS ONE
			//$result = $this->adapter->query("select * from vctraccountsummary" . $sSpec . " union select * from vCtrAccountSummary3" . $sSpec . " ORDER BY x, cli_city, cli_state, ctr_no, ph_name",  array());
			//"select * from vpresentsrpt where pipl_date between '" & FormatDT(D1) & "' and '" & FormatDT(D2) &" 23:59' union select * from vpresentsrpt3 where pipl_date between '" & FormatDT(D1) & "' and '" & FormatDT(D2) &" 23:59' order by emp_uname, pipl_date, ctr_no"
			$result = $this->adapter->query("select * from vpresentsrpt where pipl_date>= ?  AND pipl_date<= ? union select * from vpresentsrpt3 where pipl_date>= ?  AND pipl_date<= ? order by emp_uname, pipl_date, ctr_no",  array($start, $end, $start, $end));
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['ph_id']=$row->ph_id;
					$ar[$i]['ctct_name']=$row->ctct_name;					
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ph_spec_main']=$row->ph_spec_main;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['pipl_nurse']=$row->pipl_nurse;
					$ar[$i]['at_abbr']=$row->at_abbr;
									
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getMonthAcctReview($sort) {         
            $userid = $_COOKIE["phguid"];			
			
			if($sort=="")
				$sort = "recruiter, ctr_location_c";
			if($sort=="recruiter")
				$sort = "recruiter, ctr_location_c";
						
			$result = $this->adapter->query("select * from vacctsummary  order by ".$sort,  array());
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_date']=$row->ctr_date;
					$ar[$i]['ctr_spec']=$row->ctr_spec;					
					$ar[$i]['st_name']=$row->st_name;
					$ar[$i]['recruiter']=$row->recruiter;
					$ar[$i]['marketer']=$row->marketer;
					$ar[$i]['ctr_amount']=$row->ctr_amount;
					$ar[$i]['ctr_monthly']=$row->ctr_monthly;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ctr_user_mod']=$row->ctr_user_mod;
					$ar[$i]['ctr_date_mod']=$row->ctr_date_mod;
					$ar[$i]['ctr_type']=$row->ctr_type;
					$ar[$i]['ctr_retain_date']=$row->ctr_retain_date;
					$ar[$i]['ctr_shortnote']=$row->ctr_shortnote;
					$ar[$i]['ctr_src_term']=$row->ctr_src_term;
					$ar[$i]['ctr_src_termdt']=$row->ctr_src_termdt;
					$ar[$i]['ctr_locumactive']=$row->ctr_locumactive;
					$ar[$i]['activesourcing']=$row->activesourcing;
					$ar[$i]['sincepresent']=$row->sincepresent;
					$ar[$i]['present_date']=$row->present_date;														
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getPltPresents($sort) {         
            $userid = $_COOKIE["phguid"];			
			
			if($sort=="")
				$sort = "present_date, spec";
						
			$result = $this->adapter->query("select * from ltpresents as l left join lstphysicians as p ON p.ph_id=l.ph_id left join lstcontacts as c ON c.ctct_id = p.ph_ctct_id  order by ".$sort,  array());
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ph_id']=$row->ph_id;
					$ar[$i]['location']=$row->location;
					$ar[$i]['work_site']=$row->work_site;
					$ar[$i]['present_date']=$row->present_date;					
					$ar[$i]['spec']=$row->spec;
					$ar[$i]['user_mod']=$row->user_mod;
					$ar[$i]['user_mod_id']=$row->user_mod_id;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['nurse']=$row->nurse;	
					$ar[$i]['ctct_name']=$row->ctct_name;					
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getPlaceMonthReport($post) {         
            $userid = $_COOKIE["phguid"];			
			
			$spec = trim($post["spec"]);
			$state = trim($post["state"]);
			if($state=="--")
				$state="";
				
			$year = trim($post["year"]);
			if($year=='')
				$year = date('Y');
			
			$sStat=$state;
			if($sStat!='')
				$sStat = "ctr_location_s = '".$sStat."'";
			$sSpec=substr($spec,0,2);
			if($sSpec=="---"){
				$sSpec="ctr_nurse=1";
				if($sStat!="")
					$sSpec = " and " .$sSpec;
			}
			elseif($sSpec!=""){
				$sSpec = "ctr_spec = '" . $sSpec . "'";
				if($sStat!="")
					$sSpec = " and " .$sSpec;
			}
			
			$sFromTo = $year;
			$sFromTo = "DATE_FORMAT(pl_date, '%Y')  = " . $sFromTo; 
			if($sStat.$sSpec !='')
				$sFromTo = " and " .$sFromTo;
			$sStat = " where " .$sStat . $sSpec . $sFromTo;
			
 
			//echo $state;
			//echo "select * from vplacmarketrpt" . $sStat . " union select * from vplacmarketrpt3" . $sStat . " order by pl_date, ctr_location_s, ctct_name, ctr_spec";
			$result = $this->adapter->query("select * from vplacmarketrpt" . $sStat . " union select * from vplacmarketrpt3" . $sStat . " order by pl_date, ctr_location_s, ctct_name, ctr_spec",  array());
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['pl_id']=$row->pl_id;
					$ar[$i]['pl_date']=$row->pl_date;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['st_name']=$row->st_name;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['cli_sys']=$row->cli_sys;
					$ar[$i]['mark_uname']=$row->mark_uname;
					$ar[$i]['mark_id']=$row->mark_id;					
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['ctct_name']=$row->ctct_name;	
					$ar[$i]['ctct_phone']=$row->ctct_phone;	
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['s2pl']=$row->s2pl;
					$ar[$i]['pl_annual']=$row->pl_annual;
					$ar[$i]['pl_split_emp']=$row->pl_split_emp;
					$ar[$i]['split_uname']=$row->split_uname;
					$ar[$i]['ph_name']=$row->ph_name;
					$ar[$i]['ph_city']=$row->ph_city;
					$ar[$i]['ph_state']=$row->ph_state;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['at_abbr']=$row->at_abbr;
					$ar[$i]['ctr_date']=$row->ctr_date;
									
									
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getISCReport($sordr,$dordr) {         
            $userid = $_COOKIE["phguid"];			
			
			//echo $sordr;
						
			$result = $this->adapter->query("select * from vctrsrciscrpt order by zerop desc,  ".$sordr,  array($sordr));
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_date']=$row->ctr_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ctct_name']=$row->ctct_name;					
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['csr_price']=$row->csr_price;					
					$ar[$i]['zerop']=$row->zerop;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;									
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function importDocs($post) { //importdoc absolute healthcare
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	$states=array();
	$result = $this->adapter->query("select st_code,st_name from dctstates", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->st_name;
					$states[$st] = $row->st_code;						
				}
			}
			//echo var_dump($states);
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	$bad = 0;
	
	$src_date = $ar[0];
	if(trim($src_date)!=""){
		$src_date = date('Y-m-d', strtotime($src_date));
	}
	else{
		$src_date="NULL";
	}
	
	$cspec = $ar[1];
	//$cskill = "NULL";
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
	//else { $cstate = $states[$cstate]; }
	if(strlen($cstate)>2)
		$cstate = $states[$cstate];
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
	$cemail = str_replace("mailto:",'',$cemail);
	
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
	
	//echo $bad;
	//echo $clic;
	if(!$bad){
		//echo "GOOD";
		
		$sql = "call AddImportPhys( $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $username . "','$ctme',$csub,$csubspec,$cskill,$note )";
				
		//echo $sql;
		
		$result = $this->adapter->query("call AddImportPhys( ?,?,?,?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,NOW(),?,?,?,? )", array($cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,$username,$csub,$csubspec,$cskill,$note));			
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->id;						
				}
			}
		
		if($id>0 && $id!=""){
		
			$table.='<tr>
<td><a href="/public/physician/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		else{
			$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';
	}
		
		return $table;
	}
	
	
	public function importMidlevels($post) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	$states=array();
	$result = $this->adapter->query("select st_code,st_name from dctstates", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->st_name;
					$states[$st] = $row->st_code;						
				}
			}
			//echo var_dump($states);
	$codes=array();
	$result = $this->adapter->query("select at_code,at_abbr from dctalliedtypes", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->at_code;
					$codes[$st] = $row->at_abbr;						
				}
			}
	//echo var_dump($codes);
	
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	$bad = 0;
	
	$src_date = $ar[0];
	if(trim($src_date)!=""){
		$src_date = date('Y-m-d', strtotime($src_date));
	}
	else{
		$src_date = date('Y-m-d');
		//$src_date="NULL";
	}
	
	$cspec = $ar[1];
	//$cskill = "NULL";
	
	//echo $cspec."<br/>";
	if($cspec!="")
	{
		//$cspec = $codes[$cspec]; //not currently used to swithc for spec name - PA, NP, etc.
	}
	else{
		$bad = 2;
	}
	//echo $cspec."-<br/>";
	
	$cname = trim($ar[2]);
	if($cname=='' || strpos(strtolower($cname), "anonymous")!==false){
		$bad = 3;
		$cname = "NULL";
	}
	else{
		//$cname = "NULL";
	}
	
	$status = $ar[3]; //not used
	
	$caddrc = trim($ar[5]);
	if(strpos($caddrc, "Confidential")!==false){
		$bad = 5;
	}		
	
	$cstate = trim($ar[6]);
	if( $cstate == "Washington D.C." ) { $cstate = 'DC'; }
	else { $cstate = $states[$cstate]; }
	if($cstate==''){
		$bad=6;
		$cstate=="NULL";
	}
	
	$caddrz = trim($ar[8]); //+1
	if($caddrz==''){		
		$caddrz=="NULL";
	}
	
	$country = $ar[9]; //+1
	
	
	$cphone = trim($ar[10]); //+1
	if($cphone=='')		
		$cphone=="NULL";
	
	$cemail = $ar[11];
	if($cemail=='')		
		$cemail=="NULL";
	$cemail = str_replace("mailto:",'',$cemail);
	
	//echo $cemail;
	$carl = $ar[12];
	$note = "";
	if($carl!='')
		$note .= ' Career Level: '.$ar[12];
		
	if(strpos($carl,'Entry L')!==false || strpos($carl,'Early C')!==false)
		$carl=1;
	elseif(strpos($carl,'Mid L')!==false )
		$carl=2;
	elseif(strpos($carl,'Advanced L')!==false )
		$carl=3;
	elseif(strpos($carl,'Senior L')!==false )
		$carl=4;
	else
		$carl=0;
	
		
	if($ar[13]!='')
		$note .= ' Primary Specialty: '.$ar[13];
	if($ar[14]!='')	
		$note .= ' Secondary Specialty: '.$ar[14];
	
	
	//start date 13;
	//travel $ar[14];
	//Willing to Relocate (unused) $ar[15];
	if($ar[18]!='')	{
		$note .= ' Ideal Locations: '.$ar[18];
	//Ideal Locations: MI-Detroit, MI-Grand Rapids, NC-Charlotte, TX-Dallas, TX-Austin
	//$ideal=states,$cprefc=cities
	$pstat=array();
	$pcity=array();
	$locs=explode(',',$ar[18]);
		foreach($locs as $k=>$v)
		{
			$arr2=explode('-',$v);
			if(trim($arr2[0])!='')
				$pstat[]=$arr2[0]; 
			if(trim($arr2[1])!='')	
				$pcity[]=$arr2[1]; 
		}
	$ideal=implode(",", $pstat);
	$cprefc=implode(",", $pcity);
	}//end if
	
	//echo $cprefc."<br/>";
	
	$clic =  $ar[19];
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
	
	
	//echo $clic;
	if(!$bad){
		//echo "GOOD";
		
		//$sql = "call AddImportPhys( $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $username . "','$ctme',$csub,$csubspec,$cskill,$note )";
		$sql = "call AddImportNurse ($cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,$username ,NOW(),$src_date,$note )";		
		//AddImportNurse $cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,'" . $Session->{"UserName"} . "','$ctme',$src_date,$note"
		//echo $sql."<br/>";
		
		$result = $this->adapter->query("call AddImportNurse( ?,?,?,?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,? )", array($cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal, $cprefc, $username, $src_date, $note));			
																															//AddImportNurse $cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,'" . $Session->{"UserName"} . "','$ctme',$src_date,$note"
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->id;						
				}
			}
		
		if($id>0 && $id!=""){
		
			$table.='<tr>
<td><a href="/public/midlevel/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		else{
			$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';
	}
		
		return $table;
	}
	
	public function importMidlevels2($post) { //old one
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	$states=array();
	$result = $this->adapter->query("select st_code,st_name from dctstates", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->st_name;
					$states[$st] = $row->st_code;						
				}
			}
			//echo var_dump($states);
	$codes=array();
	$result = $this->adapter->query("select at_code,at_abbr from dctalliedtypes", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->at_code;
					$codes[$st] = $row->at_abbr;						
				}
			}
	//echo var_dump($codes);
	
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	$bad = 0;
	
	$src_date = $ar[0];
	if(trim($src_date)!=""){
		$src_date = date('Y-m-d', strtotime($src_date));
	}
	else{
		$src_date="NULL";
	}
	
	$cspec = $ar[1];
	//$cskill = "NULL";
	
	//echo $cspec."<br/>";
	if($cspec!="")
	{
		//$cspec = $codes[$cspec]; //not currently used to swithc for spec name - PA, NP, etc.
	}
	else{
		$bad = 2;
	}
	//echo $cspec."-<br/>";
	
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
	
	$caddrz = trim($ar[7]);
	if($caddrz==''){		
		$caddrz=="NULL";
	}
	
	$country = $ar[8];
	
	//7 not used? $cspec = $ar[7];
	$cphone = trim($ar[9]);
	if($cphone=='')		
		$cphone=="NULL";
	
	$cphone = str_replace(' ','',$cphone);
	$cphone = str_replace('.','',$cphone);
	$cphone = str_replace('-','',$cphone);
	$cphone = str_replace('(','',$cphone);
	$cphone = str_replace(')','',$cphone);
	
	$cemail = $ar[10];
	if($cemail=='')		
		$cemail=="NULL";
	$cemail = str_replace("mailto:",'',$cemail);
	
	//echo $cemail;
	$carl = $ar[11];
	$note = "";
	if($carl!='')
		$note .= ' Career Level: '.$ar[11];
		
	if(strpos($carl,'Entry L')!==false || strpos($carl,'Early C')!==false)
		$carl=1;
	elseif(strpos($carl,'Mid L')!==false )
		$carl=2;
	elseif(strpos($carl,'Advanced L')!==false )
		$carl=3;
	elseif(strpos($carl,'Senior L')!==false )
		$carl=4;
	else
		$carl=0;
	
		
	if($ar[12]!='')
		$note .= ' Primary Specialty: '.$ar[12];
	if($ar[13]!='')	
		$note .= ' Secondary Specialty: '.$ar[13];
	
	
	//start date 13;
	//travel $ar[14];
	//Willing to Relocate (unused) $ar[15];
	if($ar[17]!='')	{
		$note .= ' Ideal Locations: '.$ar[17];
	//Ideal Locations: MI-Detroit, MI-Grand Rapids, NC-Charlotte, TX-Dallas, TX-Austin
	//$ideal=states,$cprefc=cities
	$pstat=array();
	$pcity=array();
	$locs=explode(',',$ar[17]);
		foreach($locs as $k=>$v)
		{
			$arr2=explode('-',$v);
			if(trim($arr2[0])!='')
				$pstat[]=$arr2[0]; 
			if(trim($arr2[1])!='')	
				$pcity[]=$arr2[1]; 
		}
	$ideal=implode(",", $pstat);
	$cprefc=implode(",", $pcity);
	}//end if
	
	//echo $cprefc."<br/>";
	
	$clic =  $ar[18];
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
	
	
	//echo $clic;
	if(!$bad){
		//echo "GOOD";
		
		//$sql = "call AddImportPhys( $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $username . "','$ctme',$csub,$csubspec,$cskill,$note )";
		$sql = "call AddImportNurse ($cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,$username ,NOW(),$src_date,$note )";		
		//AddImportNurse $cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,'" . $Session->{"UserName"} . "','$ctme',$src_date,$note"
		echo $sql."<br/>";
		
		$result = $this->adapter->query("call AddImportNurse( ?,?,?,?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,? )", array($cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal, $cprefc, $username, $src_date, $note));			
																															//AddImportNurse $cname,NULL,$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,0,NULL,$carl,NULL,0,0, $clic,NULL,$ideal,$cprefc,'" . $Session->{"UserName"} . "','$ctme',$src_date,$note"
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->id;						
				}
			}
		
		if($id>0 && $id!=""){
		
			$table.='<tr>
<td><a href="/public/midlevel/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		else{
			$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';
	}
		
		return $table;
	}
	
	
	
	public function importDocCafe($post) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	$states=array();
	$result = $this->adapter->query("select st_code,st_name from dctstates", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->st_name;
					$states[$st] = $row->st_code;						
				}
			}
			//echo var_dump($states);
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	$bad = 0;
	
	$src_date = $ar[0];
	if(trim($src_date)!=""){
		$src_date = date('Y-m-d', strtotime($src_date));
	}
	else{
		$src_date="NULL";
	}
	
	//$cspec = $ar[36];
	$cspec = $ar[42];
	//$cskill = "NULL";
	$cskill = "";
	if($cspec!="")
	{
	
	}
	else{
		$bad = 2;
	}
	
	$cname =  $ar[3]." ".$ar[4]; //trim($ar[2]);
	if($cname=='' || strpos($cname, "anonymous")!==false){
		$bad = 3;
		$cname = "NULL";
	}
	else{
		//$cname = "NULL";
	}
	
	$status = $ar[3]; //not used
	
	$caddrc = trim($ar[10]); //trim($ar[4]);
	if(strpos($caddrc, "Confidential")!==false){
		$bad = 5;
	}		
	
	$cstate = trim($ar[11]);
	if( $cstate == "Washington D.C." || $cstate == "District Of Columbia" ) { $cstate = 'DC'; }
	//else { $cstate = $states[$cstate]; }
	if($cstate==''){
		$bad=6;
		$cstate=="NULL";
	}
	
	$caddrz = trim($ar[12]);
	if($caddrz==''){		
		$caddrz=="NULL";
	}
	
	$country = $ar[13];
	
	//7 not used? $cspec = $ar[7];
	$cphone = trim($ar[14]);
	if($cphone=='')		
		$cphone=="NULL";
	$cphone = str_replace(' ','',$cphone);
	$cphone = str_replace('.','',$cphone);
	$cphone = str_replace('-','',$cphone);
	$cphone = str_replace('(','',$cphone);
	$cphone = str_replace(')','',$cphone);
	
	$cemail = $ar[19];
	if($cemail=='')		
		$cemail=="NULL";
	$cemail = str_replace("mailto:",'',$cemail);
	
	$note = "";
	//if($ar[10]!='')
		//$note .= ' Career Level: '.$ar[10];
		
	if($cspec!='')
		$note .= ' Primary Specialty: '.$cspec;
	//if($ar[12]!='')	
		//$note .= ' Secondary Specialty: '.$ar[12];
	
	$mobile = $ar[21]; //need to add***
	$mobile = str_replace(' ','',$mobile);
	$mobile = str_replace('.','',$mobile);
	$mobile = str_replace('-','',$mobile);
	$mobile = str_replace('(','',$mobile);
	$mobile = str_replace(')','',$mobile);
	
	//if($ar[16]!='')	
		//$note .= ' Ideal Locations: '.$ar[16];
	
	//$clic =  $ar[37];
	$clic =  $ar[43];		
	//echo $clic."<br/>";
	$lics = explode(",", $clic);
	//echo var_dump($lics);
	$lics2 = array();
	foreach($lics as $key=>$lic){
	//echo $lic."<br/>";
		$lic=trim($lic);
		if( $lic == "Washington D.C." || $lic == "District Of Columbia") { $lic = 'DC'; }
		//else { $lic = $states[$lic];}
		//echo $lic."<br/>";
		$lics2[] = $lic;
	}
	$clic = join(',',$lics2);
	if($clic=="")
		$clic="";
	
	
	//echo $clic;
	if($bad<=0){
		//echo "GOOD";
		
		$sql = "call AddImportPhys2( '$cname','MD','$cphone',NULL,NULL,NULL,'$mobile',NULL, '$cemail',NULL,NULL, '$caddrc','$caddrz','$cstate','$cspec',NULL,0,0,'---', '$clic',NULL,'$ideal','$src_date','" . $username . "','$ctme','$csub','$csubspec','$cskill','$note' )";
		//$sql2 = "EXEC AddImportPhys2 $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme','$csub','$csubspec',$cskill,$note";
					
		//echo $sql;
		//echo "<br/>";
		
		$result = $this->adapter->query("call AddImportPhys2( ?,?,?,?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,NOW(),?,?,?,? )", array($cname,'MD',$cphone,NULL,NULL,NULL,$mobile,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,$username,$csub,$csubspec,$cskill,$note));			
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->id;						
				}
			}
		//echo "BADDDD".$bad;
		if($id>0 && $id!=""){
		
			$table.='<tr>
<td><a href="/public/physician/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		else{
			$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';
	}
		
		return $table;
	}
	
	
	
	public function importSpecDemo($post) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	
			//echo var_dump($states);
		$arr = explode(PHP_EOL, $_POST["rawdata"]);
		//echo var_dump($arr);
		foreach($arr as $key=>$row)
		{
			//echo var_dump($row);	
			$ar = explode("\t", $row);
			$name='';
			$updated=false;
			//echo $ar[0];
			$spec=$ar[1];
			$spname=$ar[0];
			$result = $this->adapter->query("SELECT * FROM tspecdemo where sd_ama=?", array($spec));			
			
			if($result)
			{				
				foreach ($result as $row) {					
					$name = $row->sd_name;
					if($name!='')
					$result = $this->adapter->query("UPDATE tspecdemo SET sd_gt=?, sd_md=?, sd_do=?, sd_amg=?, sd_fmg=?, sd_bc=?, 
					sd_gt50=?, sd_md50=?, sd_do50=?, sd_amg50=?, sd_fmg50=?, sd_bc50=?,  
					sd_gt45=?, sd_md45=?, sd_do45=?, sd_amg45=?, sd_fmg45=?, sd_bc45=?,
					sd_gt40=?, sd_md40=?, sd_do40=?, sd_amg40=?, sd_fmg40=?, sd_bc40=?,
					sd_gtres=?, sd_mdres=?, sd_dores=?, sd_amgres=?, sd_fmgres=?, 
					sd_gt3=?, sd_md3=?, sd_do3=?, sd_amg3=?, sd_fmg3=?, sd_bc3=?
					where sd_ama=?", array($ar[2], $ar[3], $ar[4], $ar[5], $ar[6], $ar[7], $ar[8], $ar[9], $ar[10], $ar[11], $ar[12], $ar[13], $ar[14], $ar[15], $ar[16], $ar[17], $ar[18], $ar[19], $ar[20], $ar[21], $ar[22], $ar[23], $ar[24], $ar[25], $ar[26], $ar[27], $ar[28], $ar[29], $ar[30], $ar[31], $ar[32], $ar[33], $ar[34], $ar[35], $ar[36], $spec));
					$updated=true;
				}			
			}
			
			if(!$updated)
			{
				$result = $this->adapter->query("SELECT * FROM tspecdemo where sd_name=?", array($spname));			
			
				if($result)
				{				
					foreach ($result as $row) {					
						$name = $row->sd_name;
						if($ar[1]!='') //if code not blank
						{
							$result = $this->adapter->query("UPDATE tspecdemo SET sd_gt=?, sd_md=?, sd_do=?, sd_amg=?, sd_fmg=?, sd_bc=?, 
							sd_gt50=?, sd_md50=?, sd_do50=?, sd_amg50=?, sd_fmg50=?, sd_bc50=?,  
							sd_gt45=?, sd_md45=?, sd_do45=?, sd_amg45=?, sd_fmg45=?, sd_bc45=?,
							sd_gt40=?, sd_md40=?, sd_do40=?, sd_amg40=?, sd_fmg40=?, sd_bc40=?,
							sd_gtres=?, sd_mdres=?, sd_dores=?, sd_amgres=?, sd_fmgres=?, 
							sd_gt3=?, sd_md3=?, sd_do3=?, sd_amg3=?, sd_fmg3=?, sd_bc3=?, sd_ama=?
							where sd_name=?", array($ar[2], $ar[3], $ar[4], $ar[5], $ar[6], $ar[7], $ar[8], $ar[9], $ar[10], $ar[11], $ar[12], $ar[13], $ar[14], $ar[15], $ar[16], $ar[17], $ar[18], $ar[19], $ar[20], $ar[21], $ar[22], $ar[23], $ar[24], $ar[25], $ar[26], $ar[27], $ar[28], $ar[29], $ar[30], $ar[31], $ar[32], $ar[33], $ar[34], $ar[35], $ar[36], $ar[1], $spname));
							$updated=true;
						}
						else{
							$result = $this->adapter->query("UPDATE tspecdemo SET sd_gt=?, sd_md=?, sd_do=?, sd_amg=?, sd_fmg=?, sd_bc=?, 
							sd_gt50=?, sd_md50=?, sd_do50=?, sd_amg50=?, sd_fmg50=?, sd_bc50=?,  
							sd_gt45=?, sd_md45=?, sd_do45=?, sd_amg45=?, sd_fmg45=?, sd_bc45=?,
							sd_gt40=?, sd_md40=?, sd_do40=?, sd_amg40=?, sd_fmg40=?, sd_bc40=?,
							sd_gtres=?, sd_mdres=?, sd_dores=?, sd_amgres=?, sd_fmgres=?, 
							sd_gt3=?, sd_md3=?, sd_do3=?, sd_amg3=?, sd_fmg3=?, sd_bc3=?
							where sd_name=?", array($ar[2], $ar[3], $ar[4], $ar[5], $ar[6], $ar[7], $ar[8], $ar[9], $ar[10], $ar[11], $ar[12], $ar[13], $ar[14], $ar[15], $ar[16], $ar[17], $ar[18], $ar[19], $ar[20], $ar[21], $ar[22], $ar[23], $ar[24], $ar[25], $ar[26], $ar[27], $ar[28], $ar[29], $ar[30], $ar[31], $ar[32], $ar[33], $ar[34], $ar[35], $ar[36], $spname));
							$updated=true;
						}
					}
				}
			}
			
			if(!$updated)
			{
				$result = $this->adapter->query("INSERT INTO tspecdemo (sd_name, sd_gt, sd_md, sd_do, sd_amg, sd_fmg, sd_bc, 
							sd_gt50, sd_md50, sd_do50, sd_amg50, sd_fmg50, sd_bc50,  
							sd_gt45, sd_md45, sd_do45, sd_amg45, sd_fmg45, sd_bc45,
							sd_gt40, sd_md40, sd_do40, sd_amg40, sd_fmg40, sd_bc40,
							sd_gtres, sd_mdres, sd_dores, sd_amgres, sd_fmgres, 
							sd_gt3, sd_md3, sd_do3, sd_amg3, sd_fmg3, sd_bc3, sd_ama)
							VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
							", array($ar[0], $ar[2], $ar[3], $ar[4], $ar[5], $ar[6], $ar[7], $ar[8], $ar[9], $ar[10], $ar[11], $ar[12], $ar[13], $ar[14], $ar[15], $ar[16], $ar[17], $ar[18], $ar[19], $ar[20], $ar[21], $ar[22], $ar[23], $ar[24], $ar[25], $ar[26], $ar[27], $ar[28], $ar[29], $ar[30], $ar[31], $ar[32], $ar[33], $ar[34], $ar[35], $ar[36], $ar[1]));
							$updated=true;
			}
			
		}
	
		return true;
	}
	
	
	
	public function importAMGA($post) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
				
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
		$id='';
		$email='';
	//echo var_dump($row);	
	$ar = explode("\t", $row);	
	$bad = 0;	
	
	
	$cname =  $ar[0]." ".$ar[1]; //trim($ar[2]);
	if($cname=='' || strpos($cname, "anonymous")!==false){
		$bad = 3;
		$cname = "NULL";
	}
	else{
		//$cname = "NULL";
	}
	
	
	
	//echo $clic;
	if($bad<=0){
		//echo "GOOD";
		
		//$sql = "SELECT * FROM lstcontacts where ctct_name=$cname";
					
		//echo $sql;
		//echo "<br/>";
		
		$result = $this->adapter->query("SELECT * FROM lstcontacts where ctct_name=?", array($cname));			
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->ctct_id;
					$email = $row->ctct_email;
				}
			}
		//echo "BADDDD".$bad;
		if($id>0 && $id!="" && $email!=''){
			$table.= $email."<br/>";
			/*$table.='<tr>
<td><a href="/public/physician/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$email."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";*/
		}
		else{
			/*$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";*/
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		/*$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';*/
	}
		
		return $table;
	}
	
	
	
	
	public function importDocsList($post) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
	$states=array();
	$result = $this->adapter->query("select st_code,st_name from dctstates", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$st = $row->st_name;
					$states[$st] = $row->st_code;						
				}
			}
			//echo var_dump($states);
	$specs=array();
	$result = $this->adapter->query("select sp_code,sp_name from dctspecial", array());			
			
			if($result)
			{			
				foreach ($result as $row) {
					$sp = $row->sp_name;
					$specs[$sp] = $row->sp_code;						
				}
			}
			
	$arr = explode(PHP_EOL, $_POST["rawdata"]);
	//echo var_dump($arr);
	foreach($arr as $key=>$row)
	{
	//echo var_dump($row);	
	$ar = explode("\t", $row);
	
	$bad = 0;
	
	
	$src_date = date('Y-m-d');
	
	$cspec = $ar[11];
	$cskill = "";
	if($cspec!="")
	{
		$cspec = $specs[$cspec];
	}
	else{
		$bad = 2;
	}
	
	$cname =  $ar[0]." ".$ar[1]; //trim($ar[2]);
	if($cname=='' || strpos($cname, "anonymous")!==false){
		$bad = 3;
		$cname = "NULL";
	}
	else{
		//$cname = "NULL";
	}
	
	$status = $ar[3]; //not used
	
	$caddrc = trim($ar[4]); //trim($ar[4]);
	if(strpos($caddrc, "Confidential")!==false){
		$bad = 5;
	}		
	
	$cstate = trim($ar[5]);
	if( $cstate == "Washington D.C." || $cstate == "District Of Columbia" ) { $cstate = 'DC'; }
	elseif(strlen($cstate)<=2) { }
	else { $cstate = $states[$cstate]; }
	if($cstate==''){
		$bad=6;
		$cstate=="NULL";
	}
	
	$caddrz = trim($ar[6]);
	if($caddrz==''){		
		$caddrz=="NULL";
	}
	
	$country = "NULL"; //$ar[13];
	
	//7 not used? $cspec = $ar[7];
	$cphone = trim($ar[7]);
	if($cphone=='')		
		$cphone=="NULL";
	$cphone = str_replace(' ','',str_replace('-','',str_replace('(','',str_replace(')','',$cphone))));
	
	$cemail = $ar[8];
	if($cemail=='')		
		$cemail=="NULL";
	$cemail = str_replace("mailto:",'',$cemail);
	
	$cellphone = trim($ar[9]);
	if($cellphone=='')		
		$cellphone=="NULL";
	$cellphone = str_replace(' ','',str_replace('-','',str_replace('(','',str_replace(')','',$cellphone))));
	
	$note = $ar[12];
	//if($ar[10]!='')
		//$note .= ' Career Level: '.$ar[10];
		
	//if($ar[11]!='')
		//$note .= ' Primary Specialty: '.$ar[11];
	//if($ar[12]!='')	
		//$note .= ' Secondary Specialty: '.$ar[12];
	
	
	
	//if($ar[16]!='')	
		//$note .= ' Ideal Locations: '.$ar[16];
	
	//$clic =  $ar[37];
	//echo $clic."<br/>";
	//$lics = explode(",", $clic);
	//$lics2 = array();
	/*foreach($lics as $key=>$lic){
	//echo $lic."<br/>";
		$lic=trim($lic);
		if( $lic == "Washington D.C." || $lic == "District Of Columbia") { $lic = 'DC'; }
		else { $lic = $states[$lic];}
		//echo $lic."<br/>";
		$lics2[] = $lic;
	}
	$clic = join(',',$lics2);
	if($clic=="")
		$clic="NULL";
	*/
	
	//echo $clic;
	if($bad<=0){
		//echo "GOOD";
		
		//$sql = "call AddImportPhys3( '$cname','MD','$cphone',NULL,NULL,NULL,'$cellphone',NULL, '$cemail',NULL,NULL, '$caddrc','$caddrz','$cstate','$cspec',NULL,0,0,'---', '$clic',NULL,'$ideal','$src_date','" . $username . "','$ctme','$csub','$csubspec','$cskill','$note' )";
		//$sql2 = "EXEC AddImportPhys3 $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme','$csub','$csubspec',$cskill,$note";
					
		//echo $sql;
		//echo "<br/>";
		
		$result = $this->adapter->query("call AddImportPhys3( ?,?,?,?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?,?,?,NOW(),?,?,?,? )", array($cname,'MD',$cphone,NULL,NULL,NULL,$cellphone,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,$username,$csub,$csubspec,$cskill,$note));			
			
			if($result)
			{			
				foreach ($result as $row) {					
					$id = $row->id;						
				}
			}
		//echo "BADDDD".$bad;
		if($id>0 && $id!=""){
		
			$table.='<tr>
<td><a href="/public/physician/view/'.$id.'">'.$id.'</a></td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td>OK</td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		else{
			$table.='<tr>
<td>NOT ADDED</td>';

if($bad>0)
$table.='<td><span class="alert">Bad $bad</span><span class="alert bld">Error</span>Skip</td>';
else
$table.='<td></td>';

$table.='<td>'.$cname.'</td><td>'.$caddrc.', '.$cstate.'</td>
<td>'.$cemail."</td><td>P:$cphone, Z:$caddrz, S:$cspec $cskill, L:$clic, D:$src_date, $note</td>
</tr>";
		}
		
		//echo $id."+<br/>";
		//$sql = "EXEC AddImportPhys $cname,'MD',$cphone,NULL,NULL,NULL,NULL,NULL, $cemail,NULL,NULL, $caddrc,$caddrz,$cstate, $cspec,NULL,0,0,'---', $clic,NULL,$ideal,$src_date,'" . $Session->{"UserName"} . "','$ctme',$csub,$csubspec,$cskill,$note";
			
	}
	
	}//end for
	
	if($table!=""){
		$table = '<table>
		<tr>
		<td>ID</td><td>Result</td><td>Name</td><td>City, State</td><td>Email</td><td>Other</td>
		</tr>'.$table.'</table>';
	}
		
		return $table;
	}
	
	
	
	
	
	public function getHotLists($post, $market, $filter) {
           
			$sempid = $_COOKIE["phguid"];
			if($post["empid"]!="" && $post["empid"]!=0)
				$sempid=$post["empid"];
		

	if($market=="yes"){ 
	
	$table='<table border=1 cellspacing=0 cellpadding=2 width="100%" class="nobg">
<thead><TR ALIGN="center">
   <TD><B>City, State</B></TD>
   <TD><B>Facility</B></TD>';
if($filter=="p")
	$table.="<td><b>Specialty</b></td>";
$table.='<TD><B>Date</B></TD>
</TR>
<tbody>';


			$sql = "select *, DATE_FORMAT(ch_date_mod, '%Y-%m-%d') as ch_date_mod2 from vcalaccmarketer where ";
			
			if($sempid>0)
			{
				$sql .= "ch_emp_id = " . $sempid . " AND ";
			}
		
		switch($filter){
			case "p":
				$sql .=  "(ch_pending = 1 or ch_hot_prospect = 1)";
				break;
			case "h":
				$sql .=  "ch_hot_prospect = 1";
				break;
			case "1":
				$sql .=  "ch_lead_1 = 1";
				break;
			case "2":
				$sql .=  "ch_lead_2 = 1";
				break;
			default:
				$sql .=  "ch_pending = 1";
				break;
		}
		if($filter=="p")
			$sql .= " order by emp_uname, ch_pending desc, ch_date_mod desc";
		else
			$sql .= " order by emp_uname, ch_date_mod desc";
		$prevu="";
		//$table="";
			//echo $sql;
			
			$result = $this->adapter->query($sql, array());
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					//$ar[$i]['emp_uname']=$row->emp_uname;
					//$ar[$i]['notes']=$row->notes;
					if($prevu!=$row->emp_uname)	{
						$prevu=$row->emp_uname;
						$table .= '<TR><TD colspan=4 class="bld">'.$row->emp_uname.'</TD></tr>';	
					}	
					$table .= '<TR>
<TD>'.$row->ctct_addr_c.', '.$row->ctct_st_code.'</TD>
<TD><p style="margin-bottom: 0">';
if($filter=="p" && $row->ch_pending==0)
	$table.='<span class="tiny" title="Long Term Pendings">LTP</span> ';
$table .= '<a class="ndec" href="/public/client/view/'.$row->cli_id.'" target="phgbott">'.$row->ctct_name.'</a> '.$row->ctct_phone.'';
					
					
					$sql="select ctct_name, ctct_phone from lstcontacts where ctct_type = 5 and ctct_backref = ?";
					$result = $this->adapter->query($sql, array($row->cli_id));
					$wasmain="";
					if($result)
					{
						$wasmain .= '</p><div style="margin-left: 0.5cm; margin-top: 0; font-size: 12px;">';
						foreach ($result as $row2) {
							$wasmain.= $row2->ctct_name;
							if($row2->ctct_phone !=''){
								$wasmain.=": ".$row2->ctct_phone;
							}
							$wasmain .= "<b>;</b> ";
						}
					}
					if($wasmain!="")
						$wasmain.="</div>";
					else
						$wasmain.="</p>";
					$table.=$wasmain;
					
		$table.='</TD>';
		if($filter=="p")
			$table.='<td>'.$row->ch_spec.'&nbsp;</TD>';
		//$table.='
		//<TD>'.date('m/d/Y',strtotime($row->ch_date_mod2)).'</TD></TR>';
		$table.='
		<TD>'.$row->ch_date_mod2.'</TD></TR>';

				}//end foreach
			}
		}//end if market	
		
		$table.='</table>';
			return $table;
    }//end function
	
	
	
	public function getRecruiterHotLists($post, $market, $filter) {
           
			$sempid = $_COOKIE["phguid"];
			if($post["empid"]!="" && $post["empid"]!=0)
				$sempid=$post["empid"];
		
			$flt2;
			$emph;
			$flt2 = 1;
			
	//if($market==""){ 
	
			$table='<table border=1 cellspacing=0 cellpadding=2 width="100%" class="nobg">
			<thead>';
			
			$sql = "select * from vcalaccrecruiter";
			
			$emph="";
			if($sempid>0)
			{
				$emph = "phh_emp_id = " . $sempid . " AND ";
			}		
						
			switch($filter){
			case p:
				if($sempid>0)
					$emph =  " where phh_emp_id = ".$sempid;
				else
					$emph="";
				$sql = "select * from vrecpendings" . $emph . " union select * from vrecpendings3" . $emph . " ORDER BY emp_uname, ctct_name, ph_id";
				$flt2 = 2;
				break;
			case h:
				$sql .= "2 where " . $emph . "phh_hot_doc = 1 order by rec_from, ctct_name";
				break;
			case 1:
				$sql .=  "2 where " . $emph . "phh_lead_1 = 1 order by rec_from, ctct_name";
				break;
			case 2:
				$sql .=   "2 where " . $emph . "phh_lead_2 = 1 order by rec_from, ctct_name";
				break;
			case s:
				$sqlx = "";				
                if ($sempid > 0)  
					$sqlx = " and emp_id = " . $sempid;
                if ($sempid == -2)
					$sqlx .=  "and emp_dept = 'FA'";
                $sql = "select * from vphpassesrpt where date_> date_sub(NOW(),interval 30 day) " . $sqlx . " union select * from vanpassesrpt where date_> date_sub(NOW(),interval 30 day) " . $sqlx . " order by rec_from, date_ desc";
				//sql = "select * from vPhPassesRpt where datediff(day,date_,getdate()) < 120" & sqlx & " union select * from vanPassesRpt where datediff(day,date_,getdate()) < 120" & sqlx & " order by rec_from, date_ desc"                
                $flt2 = 3;
				break;
			default:
				if ($sempid > 0)
					$emph = " where phh_emp_id = " . $sempid;
				else 
					$emph = "";
					$sql = "select * from vrecpendings" . $emph . " union select * from vrecpendings3" . $emph . " ORDER BY emp_uname, ctct_name, ph_id";
					$flt2 = 2;
				break;
		}
			
			//echo $sql;
			
			$table.='<TR ALIGN="center">';
			if ($flt2 == 1)
				$table.='<td>#</td><TD><B>Name</B></TD><TD><B>Spec</B></TD>';
			elseif($flt2 == 3)
				$table.='<td>#</td><td><b>Name</b></td><td><b>Spec</b></td><td><b>Pass Date</b></td><td><b>To</b></td>';
			else
				$table.='<td>#</td><td><b>Name</b></td><TD><B>Spec.</B></TD><TD><B>City, State</B></TD>';
			$table.='</TR><tbody>';
			
			$result = $this->adapter->query($sql, array());
			
			$prevu = "";
			$iii = 0;
			if ($flt2 == 2)
			{
				if($result)
				{
					foreach ($result as $row) 
					{
						$iii += 1;
						if ($row->emp_uname != $prevu) { //' new recruiter
							$prevu = $row->emp_uname;
							$iii = 1;
							$table.='<tr><td class="bld" colspan=4>'.$prevu.'</td></tr>';
						}
						if(is_numeric(substr($row->ph_spec_main,1)))
							$link="midlevel";
						else
							$link="physician";
						$table.='<tr><td>'.$iii.'</td><td><a class="ndec" href="/public/'.$link.'/view/'.$row->ph_id.'" >'.$row->ctct_name.'</a> ('.$row->ph_id.')</td><td>'.$row->ph_spec_main.'</td><td>'.$row->ctr_location_c.', '.$row->ctr_location_s.'</td></tr> ';
						
					}//end for
				}
			}//end  if
			else
			{
				$prevpass = 0;
				foreach ($result as $row) 
				{
						$iii += 1;
						if ($row->rec_from != $prevu) { //' new recruiter
							$prevu = $row->rec_from;
							$iii = 1;
							if ($flt2==3 && $prevpass != 0)
								$table.="</td></tr>";
							$prevpass = 0;
							$colspan = $flt2+2;
							$table.=  '<tr><td colspan="'.$colspan.'" class="bld">'.$row->rec_from.'</td></tr>';

						}
						if($flt2 == 1)
						{	
							if(is_numeric(substr($row->ph_spec_main,1))){
								$link="midlevel";
								$abbr = $row->at_abbr;
							}
							else{
								$link="physician";	
								$abbr = $row->ph_spec_main;
							}
							$table.='<tr><td>'.$iii.'</td><td><a class="ndec" href="/public/'.$link.'/view/'.$row->ph_id.'" >'.$row->ctct_name.'</a> </td><td>'.$abbr.'</td></tr> ';													
						}
						elseif($prevpass!=$row->ph_id)
						{
							if($prevpass!=0)
								$table.= "</td></tr>";
							$prevpass = $row->ph_id;
							if(is_numeric(substr($row->ph_spec_main,1))){
								$link="midlevel";
								$abbr = $row->at_abbr;
							}
							else{
								$link="physician";	
								$abbr = $row->ph_spec_main;
							}
							$table.='<tr><td>'.$iii.'</td><td><a  href="/public/'.$link.'/view/'.$row->ph_id.'" >'.$row->physician.'</a> </td><td>'.$abbr.'</td><td>'.date('Y-m-d',strtotime($row->date_)).'</td><td>'.$row->rec_to.' ';											
							   
						}
						else{
							$table .= ', '.$row->rec_to.'';
						}
						
				}//end for
				if ($prevpass != 0)
					$table .="</td></tr>";
			}		
		
		$table.='</table>';
			return $table;
    }//end function

	
	public function getMarketingMeetingsReport($post) {         
            $userid = $_COOKIE["phguid"];			
			
			$start = $post["startdate"];
			$end = $post["enddate"];			
			
			if($start==""){
				$start  = mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
				$start = date('Y-m-d',$start);
			}
	
			if($end==""){
				$end = mktime(0, 0, 0, date("m")  , date("d")+14, date("Y")); //time();
				$end = date('Y-m-d',$end);
			}
			
			
			//echo "select * from vCliMeetings" . $sstat . " order by emp_uname, cm_nomeet, cm_date";
		
		$sstat=substr(trim($post["stat"]),0,2);
		$ssys = trim($post["cli_sys"]);
		
		
		if($sstat!="")
			$sstat = "ctct_st_code = '" . $sstat . "'";
		if($ssys!="")
		{
			if($sstat!="")
				$sstat = "cli_sys like '" . $ssys . "%' and " . $sstat;
			else
				$sstat = "cli_sys like '" . $ssys . "%'";
		}
		
		$iMark = 0;
		$iCoors = 0;
		if($post["coors"]=="1")
			$iCoors = 1;
		
		if($post["mark"]!="")
			$iMark = $post["mark"];
		$sMark = "";
		if($iMark==0)
			$iMark=$userid;
		
		if($iMark > 0)
		{
			if($iCoors == 1){
				$sql2 = "select * from lstemployees where emp_id = " . $iMark;
				$result = $this->adapter->query($sql2,  array($sordr));		
				if($result)
				{			
					foreach ($result as $row) {
						$user_mod=$row->emp_uname;					
					}
					$sMark = "cm_user_mod = '" . $user_mod . "'";
				}
			}
			else{
				$sMark = "emp_id = '" . $iMark . "'";
			}
			if($sstat!="")
				$sMark = " and emp_id = " . $iMark;
		}
		
		$cmdm = "cm_date";
		if($iCoors == 1)
			$cmdm = "cm_date_mod";
			
		if($start!="" && $end!="")
			$sql = $cmdm . " between '" . $start . "' and '" . $end . "'";
		elseif($start=="" && $end=="")
			$sql = "";
		elseif($start=="" )
			$sql = $cmdm . " <= '" . $end . "' ";
		else
			$sql = $cmdm . " >= '" . $start . "' ";
			
		
		if($sstat.$sMark !="")
			$sql = " and " . $sql;
		if($sstat.$sMark.$sql !="")
			$sstat = " where cm_cancel = 0 and " . $sstat . $sMark . $sql;
		else
			$sstat = " where cm_cancel = 0 ";
		
	
		
		 $icnt;
		 $prevuname;
		 $scnt;
		 $cccnt;
		 $reccnt;
      $icnt = 0;
	  $scnt = 0;
	  $cccnt = 0;
	  $reccnt = 0;
      $prevuname = "";
		
		$table = '<table class="nobg" width="100%" border=1 cellspacing=0 cellpadding=1><thead><tr><td>&nbsp;</td>
<td class="bld ctr">Date</td><td class="bld ctr">Facility</td>
<td class="bld ctr">Set/Modified</td><td class="bld ctr">by...</td>
</tr></thead><tbody>';
		
		//echo "select * from vclimeetings" . $sstat . " order by emp_uname, cm_nomeet, cm_date";
			$result = $this->adapter->query("select * from vclimeetings" . $sstat . " order by emp_uname, cm_nomeet, cm_date",  array($sordr));
			
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
				
					if($row->ctr_id != $prevuname){
						$scnt = $scnt + $icnt;
						$icnt = 0;
						$prevuname = $row->emp_uname;
						$table.='<tr><td class="bld" colspan=6>'.$prevuname.'</td></tr>';
					}
					if(!$row->cm_cancel)
						$icnt = $icnt + 1;
					if(strpos($row->emp_dept,"M")===false)
						$reccnt = $reccnt+1;
					elseif($row->cm_nomeet==1)
						$cccnt = $cccnt + 1;
					
					$table.='<tr><td>';
					if($row->cm_cancel)
						$table.='&nbsp;';
					else
						$table.=$icnt;
					$table.='</td><td>'.$row->cm_date.'</td><td>';
					if($row->cm_nomeet)
						$table.="<span class='smoll bld notes' title='Conference Call'><sup>C</sup><span style='font-size: 7pt'>C</span></span> ";
					$table.='<a class="ndec" href="/public/client/view/'.$row->cli_id.'">'.$row->ctct_name.'</a>';
					$table.='<br>'.$row->ctct_addr_c.', '.$row->ctct_st_code.'';
					if($row->cm_shortnote!='')
						$table.="<br><div class='smoll'>" .$row->cm_shortnote. "</div>";
				$table.='</td><td>'.$row->cm_date_mod.'</td><td>'.$row->ctct_st_code.'</td></tr>';								
					
					
				}
			}
			
		$scnt = $scnt + $icnt;
		$ss1 = "s";
		$ss2 = "s";
		$ss3 = "s";
		if($scnt-$reccnt-$cccnt == 1)
			$ss1 = "";
		if($cccnt==1)
			$ss2 = "";
		if($reccnt==1)
			$ss3 = "";
		
		$table.='<tr><td class="ctr" colspan="6"><b>Total meetings: '.$scnt.'</b><br>
   (<b>'.($scnt-$reccnt-$cccnt).'</b> Marketing Meeting'.$ss1.', 
    <b>'.$cccnt.'</b> Conference Call'.$ss2.'';
	if($reccnt>0)
		$table.=', <b>'.$reccnt.'</b> Recruiting Meeting'.$ss3.' or C.Call'.$ss3.'';
	$table.=')</td></tr>';

			$table.='</tbody></table>';
			
			return $table;
    }
	
	public function getHOTDocReport($post, $sort='') {         
            $userid = $_COOKIE["phguid"];			
			
			$start = $post["startdate"];
			$end = $post["enddate"];			
			
			if($start==""){
				$start  = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));
				$start = date('Y-m-d',$start);
			}
	
			if($end==""){
				//$end = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); //time();
				$end = date('Y-m-d');
			}
			$datelink= '&startdate='.$start.'&enddate='.$end;
			
			$table='<table class="nobg" width="100%" border=1 cellspacing=0 cellpadding=1><thead><tr>
<th>&nbsp;</th>
<th class="bld ctr"><a href="?sort=ctct_name'.$datelink.'">Physician</a></th><th class="bld ctr"><a href="?sort=ph_spec_main'.$datelink.'">Spec</a></th><th class="bld ctr"><a href="?sort=ph_prot_date'.$datelink.'">Prot. Date</a></th>
<th class="bld ctr"><a href="?sort=ph_src_date'.$datelink.'">Source Date</a></th><th class="bld ctr"><a href="?sort=note_dt'.$datelink.'">Note Date</a></th><th class="bld ctr">Notes</th>
</tr></thead><tbody>';
			
			$icnt = 0;
			$prevcliid = 0;
			$scls = "";
			
			//echo "call HotDocsReport($start,$end)";
			$result = $this->adapter->query("call HotDocsReport(?,?,?)",  array($start, $end, $sort));			
			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) 
				{
					$prevflag = "";
					if($row->ph_id!= $prevcliid){ //new client					
						$icnt = $icnt + 1;
						$prevcliid = $row->ph_id;
						$scls = "class='duble'";
						
						$table.='<tr><td class="duble">'.$icnt.'</td><td class="duble"><a href="/public/physician/view/'.$prevcliid.'">'.$row->ctct_name.'</a>
						<br/>H:'.$row->phone_h.', W:'.$row->phone_w.'';
						if($row->ctct_email!='')
							$table.=', <a href="mailto:'.$row->ctct_email.'">'.$row->ctct_email.'</a>';
						$table.='</td>';
						$table.='<td class="duble">'.$row->ph_spec_main.'</td>
						<td class="duble">'.$row->ph_prot_date.'&nbsp;</td>
						<td class="duble">'.$row->src_date_last.'&nbsp;</td>
						<td class="duble">'.$row->note_dt.'</td>';
			   
					}
					else{
						$table.='<tr><td>&nbsp;</td><td colspan="2">&nbsp;</td>';
						$scls = "";
						$prevflag = "colspan=4";
					}
					$table.=' <td '.$scls.' '.$prevflag.' >'.$row->note_text.'</td></tr>';
				}
			}
			
			$table.='</tbody></table>';
			
		return $table;	
	}
	
	public function getSysPresentsReport($post) {         
            $userid = $_COOKIE["phguid"];			
			$table="";
			//echo $sordr;
			$icnt = 0;
			$prcnt = 0;
			$plcnt = 0;						
			$sus = $post["sys"];
			$spec = $post["spec"];
			$nosus = $post["nosys"];
			if($sus!="" || $nosus !="")
			{
				$sus2 = "";				
			}			
			
			if($sus!="" || $nosus!="")
			{
			$slike = "and cli_sys like '%".$sus."%'";
			$sspe = "";
			if($nosus!=""){ 
				if($sus=="")
					$slike = "";
				else
					$slike = " and cli_sys not like '%".$sus."%' ";
			}
			if($spec=="midl.")
				$sspe = " and pipl_nurse=1 ";
			elseif($spec!="" && $spec!="---|--")
				$sspe = " and ph_spec_main = '".$spec."'";
			}
			$date1= $post["yer1"]."-".$post["mon1"]."-01";
			$date2= $post["yer2"]."-".$post["mon2"]."-31"; //just use 31 as catch-all
			//echo $date1;
						
			//$result = $this->adapter->query("select * from vcliarhreport3 where pipl_date between ".$date1,  array($sordr));
			
			$sql="select * from vcliarhreport where pipl_date between '".$date1."' and '".$date2."' $slike $sspe union select * from vcliarhreport3 where pipl_date between '".$date1."' and '".$date2."' $slike $sspe order by pipl_nurse, ph_spec, pipl_date, city";
			//echo $sql;
			$result = $this->adapter->query($sql,  array());
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) 
				{					
					switch($row->pipl_status){
						case 4:
							$plcnt = $plcnt+1;
							break;
						case 5:
							$plcnt = $plcnt+1;
							break;
						case 2:
							$prcnt = $prcnt+1;
							break;
						case 8:
							$prcnt = $prcnt+1;
							break;
						case 3:
							$icnt = $icnt+1;
							break;						
					}
					
					$table.='<tr>
 <td><a class="ndec" href="/public/';
 if($row->pipl_nurse==1)
	$table.='midlevel';
 else
	$table.='physician';
 $table.='/view/'.$row->ph_id.'">'.$row->physician.'</a></td>
 <td class="ctr">'.$row->ph_spec.'</td>
 <td class="ctr">'.date('Y-m-d',strtotime($row->pipl_date)).'</td>
 <td class="ctr">'.$row->status.'</td>
 <td><a class="ndec" href="/public/contract/view/'.$row->ctr_id.'">'.$row->facility.'</a> - '.$row->city.', '.$row->state.'</td>
 <td>&nbsp;</td>
 </tr>';
					//$ar[$i]['ctr_id']=$row->ctr_id;
					
					//$i+=1;
				}
				$subtable='<tr><td class="bld ctr" colspan=13>Total Presents: '.$prcnt.'<br>
Interviews: '.$icnt.'<br>
Placements: '.$plcnt.'<br>
Total Activities: '.($prcnt+$icnt+$plcnt).'</td></tr>
<tr><td colspan=13>
<address class="smoll">
This report includes all of the recruiting activity on both active and closed/canceled accounts.
<em>Please note: This report is for management purposes only.   Should any
candidate on this report be placed in another facility as a direct result of
information provided first by Pinnacle, then Pinnacle Health Group would
earn a placement fee according to the agreement in place.  Please respect
the rights of the company providing the information to you.</em>
</address>
</td></tr>';
			}
			
			if($table!=''){
			$table='<table class="nobg" width="100%" ><thead><tr>
<td class="bld ctr">Name</td>
<td class="bld ctr">Specialty</td>
<td class="bld ctr">Date</td>
<td class="bld ctr">Status</td><td class="bld ctr">Facility</td>
<td class="bld ctr">Comments</td>
</tr></thead><tbody>'.$table.''.$subtable.'</tbody></table>';
			}

			return $table;
    }
	
	
	public function getMonthPresentsReport($post) {         
            $userid = $_COOKIE["phguid"];			
			$table="";
			//echo $sordr;
			$icnt = 0;
			$prcnt = 0;
			$plcnt = 0;	
			$ctrid = 0;
			$cliid = 0;
			$sus = $post["sys"];			
			
			if($sus!="" || $nosus !="")
			{
				$sus2 = "";				
			}			
			
			if($sus!="" )
			{
			$slike = "and cli_sys like '%".$sus."%'";
			$sspe = "";			
				if($sus=="")
					$slike = "";				
			
			}
			$date1= $post["yer1"]."-".$post["mon1"]."-01";
			if($post["yer1"]=="" || $post["mon1"]=="")
				$date1= date('Y')."-".date('m')."-01";
			$date2= $post["yer2"]."-".$post["mon2"]."-31"; //just use 31 as catch-all
			if($post["yer2"]=="" || $post["mon2"]=="")
				$date1= date('Y')."-".date('m')."-31";
			//echo $date1;						
			
			$sql="select * from vclisysreport2 where pipl_date between '".$date1."' and '".$date2."' $slike  union select * from vclisysreport3 where pipl_date between '".$date1."' and '".$date2."' $slike  order by facility, cli_id, ctr_no, pipl_date, ph_spec";
			//echo $sql;
			$result = $this->adapter->query($sql,  array());
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) 
				{					
					switch($row->pipl_status){
						case 4:
							$plcnt = $plcnt+1;
							break;
						case 5:
							$plcnt = $plcnt+1;
							break;
						case 2:
							$prcnt = $prcnt+1;
							break;
						case 8:
							$prcnt = $prcnt+1;
							break;
						case 3:
							$icnt = $icnt+1;
							break;						
					}
					
					
					if($row->cli_id != $cliid){
						$cliid = $row->cli_id;
						$table.='<tr>
						<td colspan="6"><a class="ndec" href="/public/client/view/'.$row->cli_id.'">'.$row->facility.'</a></td>
						</tr>';
					}
					if($row->ctr_id != $ctrid){
						$ctrid = $row->ctr_id;
						$table.='<tr><td>&nbsp;</td>
						<td colspan="5"><a class="ndec" href="/public/contract/view/'.$row->ctr_id.'">'.$row->ctr_no.'</a> (';
						if($row->ctr_nurse==1)
							$table.="midl.";
						$table.=''.$row->ctr_spec.') - '.$row->city.', '.$row->state.'</td>
						</tr>';
					}
					$table.='<tr>
					<td colspan="2">&nbsp;</td>
					<td><a class="ndec" href="/public/';
						if($row->pipl_nurse==1)
							$table.='midlevel';
						else
							$table.='physician';
						$table.='/view/'.$row->ph_id.'">'.$row->physician.'</a></td>
						<td align="center">'.$row->ph_spec.'</td>
						<td align="center">'.date('Y-m-d',strtotime($row->pipl_date)).'</td>
						<td align="center">'.$row->status.'</td>
						</tr>';
				}
				$subtable='<tr><td class="bld ctr" colspan=13>Total Presents: '.$prcnt.'<br>
Interviews: '.$icnt.'<br>
Placements: '.$plcnt.'<br>
Total Activities: '.($prcnt+$icnt+$plcnt).'</td></tr>
<tr><td colspan=13>
<address class="smoll">
This report includes all of the recruiting activity on both active and closed/canceled accounts.
<em>Please note: This report is for management purposes only.   Should any
candidate on this report be placed in another facility as a direct result of
information provided first by Pinnacle, then Pinnacle Health Group would
earn a placement fee according to the agreement in place.  Please respect
the rights of the company providing the information to you.</em>
</address>
</td></tr>';
			}//end if result
			
			if($table!=''){
			$table='<table class="nobg" width="100%" border=1 cellspacing=0 cellpadding=1><thead><tr>
<td align="center"><strong>Facility</strong></td>
<td align="center"><strong>Contract</strong></td>
<td align="center"><strong>Name</strong></td>
<td align="center"><strong>Specialty</strong></td>
<td align="center"><strong>Date</strong></td>
<td align="center"><strong>Status</strong></td>
</tr></thead><tbody>'.$table.''.$subtable.'</tbody></table>';
			}
			return $table;
    }
	
	public function getRejectedFuzion($get) {         
            $userid = $_COOKIE["phguid"];			
			
			$start=$get["startdate"];
			$end=$get["enddate"];
			
			if($start==""){
				$start  = mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
				$start = date('Y-m-d',$start);
			}	
			if($end==""){
				//$end = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); //time();
				$end = date('Y-m-d');
			}
			
						
			$sFromTo = "";
			$sFromTo = " where date_add between '". $start ."' and '". $end ."'";
			
			$ordr;
			$sordr;
			$dordr;
			$ordr = $get["ordr"];
			
			if($ordr=="")
				$ordr = 0;
				
			switch($ordr)
			{			
				case 1:
					$sordr = "date_add desc, ctct_name";
					$dordr = "Date, Name";
					break;
				case 2:
					$sordr = "encoder, date_add desc, ctct_name";
					$dordr = "Data Entry, Date";
					break;
				case 3:
					$sordr = "ctct_name";
					$dordr = "Name";
					break;
				case 4:
					$sordr = "ctct_email";
					$dordr = "Email";
					break;
				case 5:
					$sordr = "ph_spec_main, ctct_name";
					$dordr = "Specialty, Name";
					break;
				case 6:
					$sordr = "reason, encoder";
					$dordr = "Status";
					break;
				case 7:
					$sordr = "other, reason, encoder";
					$dordr = "Notes";
					break;
				default:
					$sordr = "date_add desc, ctct_name";
					$dordr = "Date, Name";
					break;		
			}		
			
			$reasons[0] = "Exported";
			$reasons[1] = "No email";
			$reasons[2] = "No specialty";
			$reasons[3] = "No contact info";
			$reasons[4] = "Other";
			$reasons[5] = "No Medical School";
			$reasons[6] = "No Residency Info";
			$reasons[7] = "No training info"; //not used
			$reasons[8] = "Duplicate (no data added)";
			$reasons[9] = "Duplicate (data updated)";
			$reasons[10] = "Redi Data";
			$tcnt = 0;
			
			$sql = "select * from vfuexportrpt2 ".$sFromTo. " order by " .$sordr;
			
			//echo $sql;
			
			$result = $this->adapter->query($sql,
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['date_add']=$row->date_add;
					$ar[$i]['reason']=$row->reason;
					$ar[$i]['other']=$row->other;
					$ar[$i]['ph_id']=$row->ph_id;	
					$ar[$i]['ph_spec_main']=$row->ph_spec_main;		
					$ar[$i]['ctct_name']=$row->ctct_name;		
					$ar[$i]['encoder']=$row->encoder;		
					$ar[$i]['ctct_email']=$row->ctct_email;		
					$i+=1;
					
					$tcnt = $tcnt + 1;
					$icnt[$row->reason] = $icnt[$row->reason]+1;
	    //icnt(RS("reason")) = icnt(RS("reason")) + 1
					$table.='<tr><td>'.$tcnt.'</td>
   <td>'.$row->date_add.'</td>
   <td>&nbsp;'.$row->encoder.'</td>
   <td><a href="/public/physician/view/'.$row->ph_id.'">'.$row->ctct_name.'</a></td>
   <td>&nbsp;'.$row->ctct_email.'</td>
   <td>'.$row->ph_spec_main.'</td>
   <td>'.$reasons[$row->reason].'</td>
   <td>&nbsp;'.$row->other.'</td>
</tr>';
					
				}
			}
			$bottom="";
			for($i=0;$i<=4; $i++)
			{
				if($icnt[$i]>0)
					$bottom.='Total '.$reasons[$i].': '.$icnt[$i].'<br/>';
			}			
			
			$table='<p class="onscr"><span class="bld">Sorted by</span>: '.$dordr.'</p>

<p class="smoll onscr">Click on the column heading to sort in that order</p>
<table class="nobg" width="100%" border=1 cellspacing=0 cellpadding=1><thead><tr><td> </td>
<td class="bld ctr"><a class="ndec" href="?ordr=1&startdate='.$start.'&enddate='.$end.'">Date</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=2&startdate='.$start.'&enddate='.$end.'">Data Entry</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=3&startdate='.$start.'&enddate='.$end.'">Name</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=4&startdate='.$start.'&enddate='.$end.'">Email</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=5&startdate='.$start.'&enddate='.$end.'">Spec</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=6&startdate='.$start.'&enddate='.$end.'">Export Status</a></td>
<td class="bld ctr"><a class="ndec" href="?ordr=7&startdate='.$start.'&enddate='.$end.'">Notes</a></td>
</tr></thead><tbody>'.$table.'</tbody></table><p style="font-weight:bold; text-align:center">'.$bottom.'</p>';
			
			return $table;
    }
	
	
	public function getClientActivity($id, $post='') {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}	
		$year=date('Y');
		$last_year=$year-1;
		if($post["start_date"]=='')
			$start_date=date('Y-01-01');
		else
			$start_date = $post["start_date"];
		if($post["end_date"]=='')
			$end_date=date('Y-m-d');
		else
			$end_date = $post["end_date"];
		
		//echo "start".$start_date;
		//echo "end".$end_date;
		
		$ar=array();
		//get active searches
		$result = $this->adapter->query("select * from vretainedsearch where cli_id= ? order by ctr_id", array($id));			
		$i=0;	
			if($result)
			{			
				foreach ($result as $row) {
					$ar[$i]["ctr_id"] = $row->ctr_id;
					$ar[$i]["ctr_no"] = $row->ctr_no;
					$ar[$i]["ctr_spec"] = $row->ctr_spec;					
					$ar[$i]["rec_uname"] = $row->rec_uname;
					$ar[$i]["cli_id"] = $row->cli_id;
					$ar[$i]["ctct_addr_c"] = $row->ctct_addr_c;
					$ar[$i]["ctct_st_code"] = $row->ctct_st_code;
					$ar[$i]["ctct_name"] = $row->ctct_name;
					$ar[$i]["mark_uname"] = $row->mark_uname;
					$ar[$i]["cli_rating"] = $row->cli_rating;
					$ar[$i]["ctr_type"] = $row->ctr_type;
					$ar[$i]["rec_status"] = $row->rec_status;
					$ar[$i]["mark_status"] = $row->mark_status;
					$ar[$i]["ctr_nurse"] = $row->ctr_nurse;
					$ar[$i]["at_abbr"] = $row->at_abbr;
					$ar[$i]["ctr_date"] = $row->ctr_date;
					$ar[$i]["sp_name"] = $row->sp_name;
					//$ar[$i][""] = $row->st_name;
					//$ar[$i][""] = $row->st_name;
					$i++;
				}
			}
		
		//get placements
		//select * from vplacmarketrpt where cli_id = ? and YEAR(pl_date)=? union select * from vplacmarketrpt3  where cli_id = ? and YEAR(pl_date)=?
		//$result = $this->adapter->query("select * from vplacmarketrpt where cli_id = ? and YEAR(pl_date)=? union select * from vplacmarketrpt3  where cli_id = ?  and YEAR(pl_date)=?", array($id,$year,$id,$year));
		if($post["start_date"]=='')
			$result = $this->adapter->query("select * from vplacmarketrpt where cli_id = ? and YEAR(pl_date)=? union select * from vplacmarketrpt3  where cli_id = ?  and YEAR(pl_date)=?", array($id,$year,$id,$year));
		else
			$result = $this->adapter->query("select * from vplacmarketrpt where cli_id = ? and pl_date between ? and ? union select * from vplacmarketrpt3  where cli_id = ?  and pl_date between ? and ?", array($id, $start_date, $end_date, $id, $start_date, $end_date));
		
		$i=0;	
		$pltable="";
		if($result)
		{			
			foreach ($result as $row) {
				$pl[$i]["pl_id"] = $row->pl_id;
				$pl[$i]["pl_date"] = $row->pl_date;
				$pl[$i]["ctr_id"] = $row->ctr_id;
				$pl[$i]["ctr_no"] = $row->ctr_no;
				$pl[$i]["ctr_location_c"] = $row->ctr_location_c;
				$pl[$i]["ctr_location_s"] = $row->ctr_location_s;
				$pl[$i]["emp_uname"] = $row->emp_uname;
				$pl[$i]["ph_name"] = $row->ph_name;
				
				$pltable.="<tr><td><a href='http://testdb.phg.com/public/report/placement2/".$row->pl_id."'>".$row->ph_name."</a></td><td>".$row->emp_uname."</td><td>".$row->ctct_name."</td></tr>";
				$i++;
			}
		}
		$pltable="<strong>".$year." Placements</strong><br/><br/><table style='width: 100%; padding: 10px; border: 1px solid;'><tr><th>Name</th><th>Recruiter</th><th>Facility</th></tr>".$pltable."</table>";
		
		//get presents
		//select * from vpresentsrpt where ctr_cli_id=?  union select * from vpresentsrpt3 where ctr_cli_id=? order by emp_uname, pipl_date, ctr_no
		$table="";
		$table2="";
		$oldphid=0;
		$plcnt = 0;	
			$prcnt = 0;	
			$icnt = 0;	
			$pencnt = 0;
		$pr=array();
		foreach($ar as $key=>$val)
		{
			
			$table.="<tr><td colspan='8'><strong>".$val["sp_name"]."&nbsp;&nbsp;(".date('Y-m-d',strtotime($val["ctr_date"])).")</strong></td></tr>";
			$table2.="<tr><td colspan='8'><strong>".$val["sp_name"]." (".date('Y-m-d',strtotime($val["ctr_date"])).")</strong></td></tr>";
			
			//$result = $this->adapter->query("select * from vpresentsrpt left join sysrepcomments on sys_pipl_id=pipl_id where ctr_cli_id=? and ctr_id=?   union select * from vpresentsrpt3 left join sysrepcomments on sys_pipl_id=pipl_id where ctr_cli_id=? and ctr_id=? order by pipl_date desc, emp_uname, ctr_no", array($id,$val["ctr_id"],$id,$val["ctr_id"]));
			//$result = $this->adapter->query("select * from vcliarhreport left join sysrepcomments on sys_pipl_id=pipl_id where ctr_cli_id=? and ctr_id=?   union select * from vcliarhreport3 left join sysrepcomments on sys_pipl_id=pipl_id where ctr_cli_id=? and ctr_id=? order by ctr_id, pipl_id, ph_id, pipl_status", array($id,$val["ctr_id"],$id,$val["ctr_id"]));
			//$result = $this->adapter->query("call GetClientActivity(?,?)", array($id, $val["ctr_id"]));
			$result = $this->adapter->query("call GetClientActivity(?,?,?,?)", array($id, $val["ctr_id"], $start_date, $end_date));
			
			
			//echo $val["ctr_id"]."*****<br/>*****";
			$i=0;
			
			if($result)
			{		
				if($result->num_rows>0){
				$table.="<tr><th>Lead</th><th>Present Date</th><th>Interview Date</th><th>Placement Date</th><th>Pending Date</th><th>Phone</th><th>Cell</th><th>Comments</th></tr>";
				$table2.="<tr><th>Lead</th><th>Present Date</th><th>Interview Date</th><th>Placement Date</th><th>Pending Date</th><th>Phone</th><th>Cell</th><th>Comments</th></tr>";
				}
			foreach ($result as $row) 
			{
				$pdate='';
				$pldate='';
				$idate='';
				$pendate='';
				$phone='';
				$cell='';
				$pr[$val["ctr_id"]][$row->pipl_id]["ph_id"] = $row->ph_id;
				$pr[$val["ctr_id"]][$row->pipl_id]["pipl_id"] = $row->pipl_id;
				//$pr[$val["ctr_id"]]["ctct_name"] = $row->ctct_name;
				$pr[$val["ctr_id"]][$row->pipl_id]["ctct_name"] = $row->physician;
				$pr[$val["ctr_id"]][$row->pipl_id]["ctr_location_c"] = $row->ctr_location_c;
				$pr[$val["ctr_id"]][$row->pipl_id]["ctr_location_s"] = $row->ctr_location_s;
				$pr[$val["ctr_id"]][$row->pipl_id]["ph_spec_main"] = $row->ph_spec_main;
				$pr[$val["ctr_id"]][$row->pipl_id]["ctr_nurse"] = $row->ctr_nurse;
				$pr[$val["ctr_id"]][$row->pipl_id]["pipl_nurse"] = $row->pipl_nurse;
				$pr[$val["ctr_id"]][$row->pipl_id]["at_abbr"] = $row->at_abbr;
				$pr[$val["ctr_id"]][$row->pipl_id]["pipl_date"] = $row->pipl_date;
				if($row->ctct_phone!='' && $row->ctct_phone!='0'){
					$phone=$row->ctct_phone;
					$pr[$val["ctr_id"]][$row->pipl_id]["ctct_phone"] = $row->ctct_phone;
				}
				if($row->ctct_cell!='' && $row->ctct_cell!='0'){
					$cell=$row->ctct_cell;
					$pr[$val["ctr_id"]][$row->pipl_id]["ctct_cell"] = $row->ctct_cell;
				}
				if($row->pdate!='' && $row->pdate!='0000-00-00 00:00:00'){
					$pdate=date('Y-m-d',strtotime($row->pdate));
					$prcnt = $prcnt+1;
				}
				if($row->idate!='' && $row->idate!='0000-00-00 00:00:00'){
					$idate=date('Y-m-d',strtotime($row->idate));
					$icnt = $icnt+1;
					//echo "HERE";
				}
				if($row->pldate!='' && $row->pldate!='0000-00-00 00:00:00'){
					$pldate=date('Y-m-d',strtotime($row->pldate));
					$plcnt = $plcnt+1;
				}
				if($row->pendate!='' && $row->pendate!='0000-00-00 00:00:00'){
					$pendate=date('Y-m-d',strtotime($row->pendate));
					$pencnt = $penct+1;
				}
				
				
				/*switch($row->pipl_status){
						case 4:
							$plcnt = $plcnt+1;
							$pr[$val["ctr_id"]][$row->pipl_id]["placement_date"] = $row->pipl_date;
							break;
						case 5:
							$plcnt = $plcnt+1;
							$pr[$val["ctr_id"]][$row->pipl_id]["placement_date"] = $row->pipl_date;
							break;
						case 2:
							$prcnt = $prcnt+1;
							$pr[$val["ctr_id"]][$row->pipl_id]["present_date"] = $row->pipl_date;
							break;
						case 8:
							$prcnt = $prcnt+1;
							$pr[$val["ctr_id"]][$row->pipl_id]["present_date"] = $row->pipl_date;
							break;
						case 3:
							$icnt = $icnt+1;
							$pr[$val["ctr_id"]][$row->pipl_id]["interview_date"] = $row->pipl_date;
							break;						
					}*/
				$pr[$val["ctr_id"]][$row->pipl_id]["comment"] = $row->comment;	
				$comments = $row->comment;
				$i++;
				
				$table.="<tr style='border:1px solid'><td><a href='http://testdb.phg.com/public/physician/view/".$row->ph_id."'>".$row->physician."</a></td><td>".$pdate."</td><td>".$idate."</td><td>".$pldate."</td><td>".$pendate."</td><td>".$phone."</td><td>".$cell."</td><td><input type='hidden' name='comments2_".$row->pipl_id."' value='".$comments."'><input type='hidden' name='phid_".$row->pipl_id."' value='".$row->ph_id."'><textarea style='width:100%' name='comments_".$row->pipl_id."'>".$comments."</textarea></td></tr>";
				$table2.="<tr style='border:1px solid'><td>".$row->physician."</td><td>".$pdate."</td><td>".$idate."</td><td>".$pldate."</td><td>".$pendate."</td><td>".$phone."</td><td>".$cell."</td><td>".$comments."</td></tr>";
			}
			
				$pr=null;
			}//end if result
			
		}
		//$totals = '<table style="width: 100%; padding: 10px; border: 1px solid;"><tr><td>&nbsp;</td><td>Presents: '.$prcnt.'</td><td>Interviews: '.$icnt.'</td><td>Placements: '.$plcnt.'</td><td>Pendings: '.$pencnt.'</td><td></td><td></td></tr></table>';
				
		//echo var_dump($pr);
		
		$table = $pltable."<br/><br/><strong>Candidates ".$year."</strong><br/><br/><table style='width: 100%; padding: 10px; border: 1px solid;'>".$table."".'<tr colspan="8" style="border: 1px solid"><th>Totals</th></tr><tr><td>&nbsp;</td><td>Presents: '.$prcnt.'</td><td>Interviews: '.$icnt.'</td><td>Placements: '.$plcnt.'</td><td>Pendings: '.$pencnt.'</td><td></td><td></td></tr>'."</table>".$totals;
		$arr[0]=$table;
		$table2 = $pltable."<br/><br/><strong>Candidates ".$year."</strong><br/><br/><table style='width: 100%; border: 1px solid;'>".$table2."".'<tr  ><th>Totals</th></tr></tr><tr><td></td><td>Presents: '.$prcnt.'</td><td>Interviews: '.$icnt.'</td><td>Placements: '.$plcnt.'</td><td>Pendings: '.$pencnt.'</td><td></td><td></td></tr></table>';		
		$arr[1]=$table2;
		//echo $table;
		
		return $arr;	
	}
	
	public function addClientActivityComments($post, $id=0) {
		
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}

		foreach($post as $key=>$val)
		{
			$comment='';
			$pipl_id=0;
			if(strpos($key,'comments_')!==false)
			{
				$pipl_id = str_replace('comments_', '', $key);				
				$comment = $val;
			}
			$comment2=$post["comments2_".$pipl_id];
			$ph_id=$post["phid_".$pipl_id];
			//echo $comment2;
			//echo $ph_id;
			
				if($pipl_id>0 && $comment!='' && $comment!=$comment2)
				{
					$result = $this->adapter->query("insert into sysrepcomments (sys_pipl_id, sys_cli_id, sys_ph_id, comment) values (?,?,?,?)", array($pipl_id, $id, $ph_id, $comment));
					
				}
			
		}
			
		return true;
	}
	
}
