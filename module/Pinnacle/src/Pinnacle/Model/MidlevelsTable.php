<?php
// module/Pinnacle/src/Pinnacle/Model/MidlevelsTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class MidlevelsTable extends Report\ReportTable
{

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vanlookupsmall',
            '`an_id`, `ctct_name`, `ctct_title`, `st_name`, `phone`, `ctct_addr_c`, `ctct_st_code`, `an_type`, `an_status`, `an_dea`, `an_licenses`, `an_date_add`, `an_avail`, `an_pref_states`, `an_experience`, `an_citizen`, `at_name`, `at_abbr`, `an_locums`, ctct_id');
    }
    
    /**
     * @param array $ar     keys: see MidlevelsForm
    */
    public function buildQuery(array $ar = null) {
        if( is_array($ar) && $ar['emp_id'] ) {
            $where = new Where();
            $nduh = 0;

            if( trim($ar['an_id']) ) {
                $where->equalTo('an_id',$ar['an_id']);
                return $where;
            }

            if( trim($ar['an_lname']) && trim($ar['an_fname']) ) {
                $ln = trim($ar['an_lname']); $fn = trim($ar['an_fname']);
                $like1 = addslashes("$ln%, $fn%");
                $like2 = addslashes("$fn% $ln%");
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }
            elseif( trim($ar['an_lname']) ) {
                $ln = trim($ar['an_lname']);
                $like1 = addslashes("$ln%"); // will also find some first names
                $like2 = addslashes("% $ln%"); // and stuff like III,Jr,Esq,etc
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }
            elseif( trim($ar['an_fname']) ) {
                $fn = trim($ar['an_fname']);
                $like1 = addslashes("$fn%"); // will also find last names
                $like2 = addslashes("%, $fn%"); // and II,III,Jr.,esq.,etc.
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }

            if( $ar['an_type']!='0000000000' && $ar['an_type']!='' ) {
                $like = addslashes(trim($ar['an_type']));
                $where->like('an_type',"$like%"); $nduh++;
            }
            if( $ar['an_date_add'] ) {
                $ival = '';
                switch( $ar['an_date_add'] ) {
                    case '1': // '< Yesterday',
                        $ival = 'INTERVAL 1 DAY'; break;
                    case '2': // '< 1 week ago',
                        $ival = 'INTERVAL 7 DAY'; break;
                    case '3': // '< 2 weeks ago',
                        $ival = 'INTERVAL 14 DAY'; break;
                    case '4': // '< 1 month ago',
                        $ival = 'INTERVAL 1 MONTH'; break;
                    case '5': // '< 2 months ago',
                        $ival = 'INTERVAL 2 MONTH'; break;
                    case '6': // '< 6 months ago',
                        $ival = 'INTERVAL 6 MONTH'; break;
                    case '7': // '< 1 year ago',
                        $ival = 'INTERVAL 1 YEAR'; break;
                }
                if( $ival ) {
                    $where->literal('an_date_add >= CURDATE()-'.$ival); $nduh++;
                }
            }
            if( $ar['an_pref_state'] ) {
                $like = addslashes(trim($ar['an_pref_state']));
                $where->like('an_pref_states',"$like%"); $nduh++;
            }
            if( $ar['an_licenses'] ) {
                $like = addslashes(trim($ar['an_licenses']));
                $where->like('an_licenses',"%$like%"); $nduh++;
            }
            if( $ar['ctct_st_code'] ) { $where->equalTo('ctct_st_code',$ar['ctct_st_code']); $nduh++; }
            if( $ar['an_experience'] ) { $where->equalTo('an_experience',$ar['an_experience']); $nduh++; }
            if( $ar['an_citizen'] ) { $where->equalTo('an_citizen',$ar['an_citizen']); $nduh++; }
            if( $ar['an_dea'] ) { $where->equalTo('an_dea',$ar['an_dea']); $nduh++; }
            if( $ar['an_locums'] ) { $where->equalTo('an_locums',$ar['an_locums']); $nduh++; }
            if( $ar['an_status'] >= 0 ) { $where->equalTo('an_status',$ar['an_status']); $nduh++; }
			else { $where->notEqualTo('an_status',12); $nduh++; }
            if( $ar['date1'] && $ar['date2'] ) { $where->between('an_avail',$ar['date1'],$ar['date2']);$nduh++; }
            elseif( $ar['date1'] ) { $where->greaterThanOrEqualTo('an_avail',$ar['date1']); $nduh++; }
            elseif( $ar['date2'] ) { $where->lessThanOrEqualTo('an_avail',$ar['date2']); $nduh++; }

            return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
    }

    public function fetchAll($id = 0, array $ar = null) {
        $where = $this->buildQuery($ar);
        if( $where ) {
            $select = new Select($this->table);
            $select->where($where);
            $select->order('ctct_name');
            $limit = (int) $ar['pg_size']; if( !$limit || $limit > 200 ) $limit = 25;
            $select->limit($limit);
            $id--; if( $id < 0 ) $id = 0;
            $offset = $id * $limit;
            $select->offset($offset);
            $resultSet = $this->selectWith($select);
            return $resultSet;
        }
        return false;
    }

    public function getResortPages(array $ar = null) {
        $where = $this->buildQuery($ar);
        if( $where ) {
            $pgsize = (int) $ar['pg_size']; if( !$pgsize ) $pgsize = 25;
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->from($this->table);
            $select->where($where);
            $select->columns(array('*'), false);
            $str = @$sql->getSqlStringForSqlObject($select); //have to turn off error handling to use this
            $str = str_replace('SELECT * ','SELECT count(*) as cnt ',$str); // awful
            $rowSet = $this->adapter->query($str)->execute();
            foreach ($rowSet as $rec)
                $total = $rec['cnt'];
            if( $total ) $total += $pgsize;
            return intval($total / $pgsize);
        }
        return 0;
    }
    
    public function saveResortPages($uid,$lid,array $ar = null) {
        $where = $this->buildQuery($ar);
        $uid = (int) $uid; $lid = (int) $lid;
        if( $where && $uid && $lid ) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->from($this->table);
            $select->where($where);
            $select->columns(array('*'), false);
            $str = @$sql->getSqlStringForSqlObject($select); //have to turn off error handling to use this
            $param = "INSERT IGNORE INTO custlistsus (owneruid,listid,ctype,memberuid) SELECT $uid,$lid,15,ctct_id ";
            $str = str_replace('SELECT * ',$param,$str); // awful
            $result = $this->adapter->query($str,Adapter::QUERY_MODE_EXECUTE);
            return $result;
        }
        return 0;
    }

    public function getResort(AbstractController $ctrl, $id = 0, array $ar = null) {
        $res = $this->fetchAll($id,$ar);
        $a = array();
        $vm = new PhpRenderer();
        $urls = $ctrl->url();
        if( $res ) foreach ($res as $row) {
            $ar = array();
            $ar['url'] = $urls->fromRoute('midlevel', array('action'=>'view', 'part'=>'go', 'id' => $row->an_id));
            if( !$row->ctct_addr_c ) $row->ctct_addr_c = ' ';

            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }
	
	
		public function selectMidlevel($id = 0, array $ar = null) {    //get dr info
			$userid = $_COOKIE["phguid"];
            $ar = array();
			$arr = array();
			$this->table='vemplist';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$ar['user_name']=$row->ctct_name;
					$ar['user_email']=$row->ctct_email;					
				}
			}
			
			$this->table='vanmain';			
			$select = new Select($this->table);
			$select
			//->from(array('c'=>'vclient'))
			//->where('cli_id = ?',$id);
			->where->equalTo('an_id',$id);
			$resultSet = $this->selectWith($select);			
	
			if($resultSet)
			{ 
				foreach ($resultSet as $row) {
					$ar['an_id']=$row->an_id;
					$ar['an_ctct_id']=$row->an_ctct_id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['an_status']=$row->an_status;
					$ar['st_name']=$row->st_name;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['an_sex']=$row->an_sex;
					$ar['an_DOB']=$row->an_DOB;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['ctct_hphone']=$row->ctct_hphone;
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$row->ctct_fax;
					$ar['ctct_hfax']=$row->ctct_hfax;
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_cell']=$row->ctct_cell;
					$ar['ctct_pager']=$row->ctct_pager;
					$ar['ctct_ext3']=$row->ctct_ext3;
					$ar['ctct_email']=$row->ctct_email;
					$ar['at_name']=$row->at_name;
					$ar['an_prot_date']=$row->an_prot_date;
					$ar['emp_uname']=$row->emp_uname;
					$ar['an_lang']=$row->an_lang;
					//$ar['an_v_skills']=$row->an_v_skills;
					$ar['an_citizen']=$row->an_citizen;
					//$ar['an_med_school']=$row->an_med_school;
					//$ar['an_pref_state']=$row->an_pref_state;
					$ar['reg_name']=$row->reg_name;
					$ar['an_licenses']=$row->an_licenses;
					//$ar['an_spec_main']=$row->an_spec_main;
					//$ar['sp_name']=$row->sp_name;
					//$ar['ph_spm_bc']=$row->ph_spm_bc;
					//$ar['ph_spm_year']=$row->ph_spm_year;
					//$ar['ph_1st_inq']=$row->ph_1st_inq;
					$ar['an_avail']=$row->an_avail;
					//$ar['ph_cv_date']=$row->ph_cv_date;
					//$ar['ph_phone_date']=$row->ph_phone_date;
					//$ar['ph_ctr_date']=$row->ph_ctr_date;
					//$ar['ph_start_date']=$row->ph_start_date;
					$ar['an_cv_url']=$row->an_cv_url;
					$ar['an_dea']=$row->an_dea;
					$ar['an_experience']=$row->an_experience;
					//$ar['ph_dea_exp']=$row->ph_dea_exp;an_experience
					//$ar['ph_upin']=$row->ph_upin;
					//$ar['ph_usmle']=$row->ph_usmle;
					$ar['an_ref_submit']=$row->an_ref_submit;
					$ar['an_ref_client']=$row->an_ref_client;
					$ar['an_ama_submit']=$row->an_ama_submit;
					$ar['an_assesm']=$row->an_assesm;
					$ar['an_completed']=$row->an_completed;
					$ar['an_preint_got']=$row->an_preint_got;
					//$ar['ph_sub']=$row->ph_sub;
					//$ar['ph_subspec']=$row->ph_subspec;
					$ar['an_date_mod']=$row->an_date_mod;
					$ar['an_user_mod']=$row->an_user_mod;
					$ar['an_ref_recr']=$row->an_ref_recr;
					$ar['an_ref_hold']=$row->an_ref_hold;
					$ar['an_ca_not']=$row->an_ca_not;
					$ar['an_ama_client']=$row->an_ama_client;
					//$ar['an_skill']=$row->an_skill;
					//$ar['sk_name']=$row->sk_name;
					$ar['an_cv_text']=$row->an_cv_text;
$ar['at_abbr']=$row->at_abbr;	
$ar['an_certificates']=$row->an_certificates;	
$ar['an_pref_states']=$row->an_pref_states;
$ar['an_pref_city']=$row->an_pref_city;
//$ar['an_cv_text']=$row->an_cv_text;		
//$ar['an_cv_text']=$row->an_cv_text;	
					$arr = $this->getMidlevelCV($id);
					$ar["cv_id"] = $arr["cv_id"];
					$ar["filename"] = $arr["filename"];
					
				}
			}			
			
			
			
		return $ar;	
    }
	
	
	//new get comments
	public function getMidlevelComments($id = 0, $noq='') {         
            
			/*$this->table='allnotes';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('note_type',15)
			->where->equalTo('note_ref_id',$id);
			$select->order('note_dt DESC');
			$resultSet = $this->selectWith($select);*/
			
			$result = $this->adapter->query('select* from allnotes where note_type = 15 and note_ref_id = ?   order by note_dt desc',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['note_user']=$row->note_user;
					$ar[$i]['note_text']=$row->note_text;
					$ar[$i]['note_dt']=$row->note_dt;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getMidlevelCV($id) {                  
						//echo 'SELECT * FROM cvs2 where not isnull(an_id) AND an_id='.$id.' order by cv_id desc';
			$result = $this->adapter->query('SELECT * FROM cvs2 where not isnull(an_id) AND an_id=? order by cv_id desc LIMIT 1',
            array($id));			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar["cv_id"]=$row->cv_id;	
					$file = $row->filedirectory;	
					$arr = Array();
					$arr = explode('/', $file);
					$filename = $arr[count($arr)-1];
					$ar["filename"]=$filename;							
				}
			}
			
			return $ar;
    }
	
	public function getHistory($id = 0) {           
			
			
			$result = $this->adapter->query('SELECT * FROM vphpipl where pipl_an_id = ? order by pipl_date',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['pipl_id']=$row->pipl_id;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['pist_name']=$row->pist_name;
					$ar[$i]['pipl_ph_id']=$row->pipl_ph_id;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['pipl_cancel']=$row->pipl_cancel;
					$ar[$i]['pipl_date_cancel']=$row->pipl_date_cancel;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['pipl_status']=$row->pipl_status;
					$ar[$i]['pipl_an_id']=$row->pipl_an_id;
					$ar[$i]['pipl_nurse']=$row->pipl_nurse;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
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
			$note_user=$useremail;
			//echo $post["activ_type"];
			if($post["schedact_date"]=="")
				$post["schedact_date"]=date('Y-m-d');
				
			if($post["schedact_hrtxt"]=="")
				$post["schedact_hrtxt"]="12";
			if($post["schedact_mintxt"]=="")
				$post["schedact_mintxt"]="00";
			
			$act_date = $post["schedact_date"]." ".$post["schedact_hrtxt"].":".$post["schedact_mintxt"].":00";
			$result = $this->adapter->query('call AddAnActivity (?,?,?,?,?,?,?,?,?) '
			,
            array(
                $note_user, //user_mod, 
				$post["activ_type"], 
				$userid, //src_emp, 
				$post["act_user"], //, //trg_emp, 
				$act_date,	//trg_date, 
				$post["act_ref"], //db_ref, 
				$post["act_ret"], //ref_type, 
				$post["sched_act_notes"], //notes, 
				$post["act_ct"]
				               
			));
			
			//add link to dr
			$post["sched_act_notes"].="\r\nhttp://testdb.phg.com/public/midlevel/view/".$post["act_ref"]."\r\n";
			
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
ORGANIZER;CN=".$realname.":mailto:".$note_user."
UID:".rand (1000000, 9999999)."
ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= TRUE;CN=".$realname.":mailto:".$note_user."
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
mail($to_email, $subject, $message, $headers);
				
			return true;
    }
	
	public function cancelPIPL($post, $identity) {             		
				$id = $post["id"];		
			
			$result = $this->adapter->query('update tctrpipl set pipl_cancel = 1 where pipl_id = ? LIMIT 1 ', array($id));
				
			return true;
    }
	
	public function getContractInfo($id = 0) {           
			
			
			$result = $this->adapter->query('select * from vctrchangefrm where ctr_id = ?',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_date']=$row->ctr_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_phone']=$row->ctct_phone;
					$ar[$i]['ctct_addr_c']=$row->ctct_addr_c;
					$ar[$i]['ctct_addr_s']=$row->ctct_addr_s;
					$ar[$i]['ctct_st_code']=$row->ctct_st_code;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;
					$ar[$i]['at_name']=$row->at_name;
					$ar[$i]['sp_name']=$row->sp_name;
					$ar[$i]['ctr_recruiter']=$row->ctr_recruiter;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getPlacementInfo($id = 0, $an_id) {           
			
			
			$result = $this->adapter->query('select * from vplacement where pipl_ctr_id = ? and pipl_an_id = ? order by pl_date desc, pipl_cancel ',
            array($id, $an_id));
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					$ar['ctr_id=']=$ctr_id;
					$ar['an_id']=$an_id;
					$ar['pipl_id']=$row->pipl_id;
					$ar['pipl_ctr_id']=$row->pipl_ctr_id;
					$ar['pipl_ph_id']=$row->pipl_ph_id;
					$ar['pipl_status']=$row->pipl_status;
					$ar['pipl_cancel']=$row->pipl_cancel;
					$ar['pl_sent_date']=$row->pl_sent_date;
					$ar['pl_date']=$row->pl_date;
					$ar['pl_ref_emp']=$row->pl_ref_emp;
					$ar['pl_term']=$row->pl_term;
					$ar['pl_guar_net']=$row->pl_guar_net;
					$ar['pl_guar_gross']=$row->pl_guar_gross;
					$ar['pl_annual']=$row->pl_annual;
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
					$ar['pl_fam_dental']=$row->pl_fam_dental;
					$ar['pl_st_dis']=$row->pl_st_dis;	
					$ar['pl_life']=$row->pl_life;
					$ar['pl_oth_ben']=$row->pl_oth_ben;
					$ar['pl_signing']=$row->pl_signing;	
					$ar['pl_replacement']=$row->pl_replacement;
					$ar['pl_exp_years']=$row->pl_exp_years;
					$ar['pl_split_emp']=$row->pl_split_emp;	
					$ar['pl_source']=$row->pl_source;
					$ar['pipl_nurse']=$row->pipl_nurse;
					$ar['pipl_an_id']=$row->pipl_an_id;	
					$ar['pl_text1']=$row->pl_text1;
					$ar['pl_text2']=$row->pl_text2;
					$ar['pl_text3']=$row->pl_text3;	
					$ar['pl_text4']=$row->pl_text4;					
					
				}
			}
			
			return $ar;
    }
	
	public function getContractSpec($id = 0) {           
			
			
			$result = $this->adapter->query('select * from vctrspec where ctr_id = ?',
            array($id));
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					$ar['ctr_no']=$row->ctr_no;
					$ar['ctr_spec']=$row->ctr_spec;
					$ar['ctr_recruiter']=$row->ctr_recruiter;
					$ar['sp_name']=$row->sp_name;
					$ar['ctr_nurse']=$row->ctr_nurse;
					$ar['at_name']=$row->at_name;				
					
				}
			}
			
			return $ar;
    }
	
	//new get specialties - for midlevel
	public function getSpecialtyOptions() {         
            			
			$ar = array();
		
			/*$result = $this->adapter->query("select * from vspecial order by sp_code,skill")->execute();
			if($result)
			{
			$i=0;
			foreach ($result as $row) 
			{
				$ar[$i]['sp_code']=$row["sp_code"];	
				$ar[$i]['skill']=$row["skill"];	
				$ar[$i]['spec']=$row["spec"];													
				$i+=1;
			}
			}	*/		
			$result = $this->adapter->query("select * from dctalliedtypes order by at_code")->execute();
			if($result)
			{
			$i=0;
			foreach ($result as $row) 
			{
				$ar[$i]['at_code']=$row["at_code"];	
				$ar[$i]['at_name']=$row["at_name"];	
				$ar[$i]['at_sort']=$row["at_sort"];	
				$ar[$i]['at_abbr']=$row["at_abbr"];	
				$ar[$i]['at_select']=$row["at_select"];				
				$i+=1;
			}
			}	
			
			return $ar;
    }
	
	//MOVED TO CONTRACTS
	public function handleCtrChange($post, $identity) {             		
				$userid = $_COOKIE["phguid"];
			$username = $_COOKIE["realname"];
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}	
			

			$id=$post["chg_ctr_id"];
			$type=$post["chg_type"];
			$reason=$post["reason"];
			$comments=$post["comments"];
			$spec=$post["chg_spec"];
			$req=$post["chg_req"];
			if($req=='' || $req==null)
				$req=31; //house
			$pipl=$post["chg_pipl"];
			$chg_nut=$post["chg_nut"];
			
			
			$result = $this->adapter->query('call AddAChgRequest(?,?,?,?,?,?,?,?,?,?) ', array($id, $type, $reason, $comments, $spec, $req, 'NOW()', $userid, $pipl, $chg_nut));
			if($result)
			{			
			foreach ($result as $row) 
			{
				$chg_id=$row["chg_id"];
				
			}
			}

			if($chg_id>0 && $chg_id!='')
			{
				//send email
				/*Setting the header part, this is important */
			$headers = "From:info@phg.com\n";
			$headers .= "BCC:nturner@phg.com\n";
			$headers .= "MIME-Version: 1.0\n";			

			/*mail content , attaching the ics detail in the mail as content*/
			$subject = "DB: Contract Status Change Request from ".$username;
			$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			$message = "This is an automatically generated message\n\nA Change Contract Status Request has been placed by ".$username."\n";
			$message .= "Please click on the link below to review the changes requested:\n\n";
			$message .= "http://testdb.phg.com/public/midlevel/approve_ctrchange?chg_id=".$chg_id."&pipl_id=".$pipl."&ctr_id=".$id."&type=".$type."&action=writeoff\n\n";

			//echo $subject;
			/*mail send*/
			mail("jcouvillon@phg.com", $subject, $message, $headers); //add john to this
			}
			/*ctr_id decimal, typ tinyint,
reason varchar(255), //null
c0mment varchar(255), //null
spec char(3), req int,
dat datetime, emp_id int,
pipl decimal, //null 
nu_type char(10)*/
				
			return true;
    }
	
	//MOVED TO CONTRACTS TABLE
	public function getContractApprovalInfo($id = 0) {           
			
			
			$result = $this->adapter->query('select * from vctrchangesrpt where chg_id =  ?',
            array($id));
			
			$ar = array();
			if($result)
			{
			//$i=0;
				foreach ($result as $row) {
				$ar['chg_id']=$row->chg_id;
				
					$ar['chg_ctr_id']=$row->chg_ctr_id;
					$ar['chg_type']=$row->chg_type;
					$ar['chg_reason']=$row->chg_reason;
					$ar['chg_comment']=$row->chg_comment;
					$ar['chg_date']=$row->chg_date;
					$ar['chg_status']=$row->chg_status;
					$ar['chg_appr_date']=$row->chg_appr_date;
					$ar['chg_emp_id']=$row->chg_emp_id;
					//$ar['chg_spec']=$row->chg_spec;
					//$ar['chg_req']=$row->chg_req;
					$ar['chg_reserved']=$row->chg_reserved;
					$ar['sp_code']=$row->sp_code;
					$ar['sp_name']=$row->sp_name;
					$ar['sp_new_n']=$row->sp_new_n;
					$ar['ctr_no']=$row->ctr_no;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctr_location_c']=$row->ctr_location_c;
					$ar['ctr_location_s']=$row->ctr_location_s;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['emp_new']=$row->emp_new;
					$ar['emp_old']=$row->emp_old;
					$ar['sp_new_c']=$row->sp_new_c;
					$ar['ctr_id']=$row->ctr_id;
					$ar['ctr_nurse']=$row->ctr_nurse;
					$ar['ctr_nu_type']=$row->ctr_nu_type;
					$ar['at_name']=$row->at_name;
					$ar['at_new_n']=$row->at_new_n;					
					$ar['chg_nu_type']=$row->chg_nu_type;
					
					
				}
			}
			
			return $ar;
    }
	
	//MOVED TO CONTRACTS TABLE
	public function handleApproveCtrChange($post, $identity) {             		
				$userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];
			$username = urldecode($_COOKIE["username"]);
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;					
				}
			}	

			$id=$post["chg_id"];
			if(isset($post["approvebtn"]))
				$chg_status=1;
			if(isset($post["declinebtn"]))
				$chg_status=2;
			if($chg_status==1)
				$decision = "Approved";
			else
				$decision = "Declined";
			$ctr_id = $post["chg_ctr_id"];
			$type=$post["chg_type"];
			$reason=$post["reason"];
			$comments=$post["comments"];
			$spec=$post["chg_spec"];
			$req=$post["chg_req"];
			if($req=='' || $req==null)
				$req=31; //house
			$pipl=$post["chg_pipl"];
			$chg_nut=$post["chg_nut"];
			
			$chits = array("Cancel Request", "Place on Hold Request", "Reactivate Search Request", "Change of Specialty Request",
	       "Change of Recruiter Request", "Placement Write Off Request", "Zombify Request" );
		   
		   $result = $this->adapter->query('select ctr_no,ctr_location_c,ctr_location_s from allcontracts where ctr_id = ?', array($ctr_id));			
			
			if($result)
			{			
				foreach ($result as $row) {
					$ctr_no =$row->ctr_no;
					$ctr_location_c =$row->ctr_location_c;
					$ctr_location_s =$row->ctr_location_s;
				}
			}
		   
			/*
			id decimal,
 status0 tinyint,
 type0 tinyint,
 dte datetime,
 ctr decimal,
 user char(32)
 */
 //echo $id." ".$chg_status." ".$type." ".$ctr_id." ".$username;
			$result = $this->adapter->query('call SetCtrChange(?,?,?,?,?) ', array($id, $chg_status, $type,  $ctr_id, $username));
			if($result)
			{			
			
			}

			if($chg_id>0 && $chg_id!='')
			{
				//send email
				/*Setting the header part, this is important */
			$headers = "From:info@phg.com\n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail."\n";
			$headers .= "BCC:nturner@phg.com\n";
			/*mail content , attaching the ics detail in the mail as content*/
			$subject = "DB: Contract ".$ctr_no." Decision ";
			$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			$message = "A Decision for " . $chits[$type] . " has been made:\n".$decision."\n\n";
			$message .= "Please click on the link below to review the decision:\n\n";
			$message .= "http://testdb.phg.com/public/midlevel/ctrchange4?chg_id=".$chg_id."\n\n";

			//echo $subject;
			/*mail send*/
			mail("jcouvillon@phg.com", $subject, $message, $headers); //add john to this
			}
			
				
			return true;
    }
	
	public function addMidlevel($post, $identity) {             		
				$userid = $_COOKIE["phguid"];
			$username = urldecode($_COOKIE["username"]);
				
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;					
				}
			}	
			
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			
			//ph_avail
			if($post["ph_avail"]=='')
				$post["ph_avail"] = date('Y-m-d');
			
			//filter phone
			$post["ctct_phone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_phone"])));
			$post["ctct_hphone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_hphone"])));
			$post["ctct_hfax"] = str_replace('-','',$post["ctct_hfax"]);
			$post["ctct_cell"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_cell"])));
			$post["ctct_fax"] = str_replace('-','',$post["ctct_fax"]);
			$post["ctct_pager"] = str_replace('-','',$post["ctct_pager"]);

			$name=$post["an_lname"].", ".$post["an_fname"];
			$title=$post["ctct_title"];
			$ctct_phone=$post["ctct_phone"];
			$ctct_ext1=$post["ctct_ext1"];
			$ctct_fax=$post["ctct_fax"];
			$ctct_ext2=$post["ctct_ext2"];
			$ctct_cell=$post["ctct_cell"];
			$ctct_pager=$post["ctct_pager"];
			$ctct_ext3=$post["ctct_ext3"];
			$ctct_hphone=$post["ctct_hphone"];
			$ctct_hfax=$post["ctct_hfax"];
			$ctct_email=$post["ctct_email"];
			
			$ctct_addr_1=$post["ctct_addr_1"];
			$ctct_addr_2=$post["ctct_addr_2"];
			$ctct_addr_c=$post["ctct_addr_c"];
			$ctct_addr_z=$post["ctct_addr_z"];
			$ctct_st_code=$post["ctct_st_code"];
			
			$an_type=$post["an_type"];
			$bc=$post["an_bc"]; //always 0??
			$an_bc_state=$post["an_bc_state"];
			$an_certificates=$post["an_certificates"];
			$an_status=$post["an_status"];
			
			$an_cv_url=$post["an_cv_url"];
			$an_experience=$post["an_experience"];
			$an_DOB=$post["an_DOB"];			
			$an_sex=$post["an_sex"];
			$an_locums=$post["an_locums"];
			
			$an_citizen=$post["an_citizen"];
			$an_lang=$post["an_lang"];
			$an_licenses=$post["an_licenses"];
			$an_dea=$post["an_dea"];
			$ph_avail=$post["ph_avail"];
			$an_pref_states=$post["an_pref_states"];
			$an_pref_city=$post["an_pref_city"];
			//$username;
			//NOW()
			
			
			/*
			name0 varchar(127), //-- mandatory//
title varchar(63),
phone decimal, ext1 char(6),
fax decimal, ext2 char(6),
cell decimal, pager decimal, ext3 char(6),
hphone decimal, hfax decimal,
email varchar(63),
addr_1 varchar(63), addr_2 varchar(63),  addr_c varchar(50),
   addr_z char(10), st_code char(2) //= '--',

type_main char(10),
bc tinyint  //=0,
bc_state varchar(50), 
cert varchar(255),
status0 tinyint //= 1, -- bound to contactStatus, too,
cv_url varchar(255),
experience tinyint  //=0,
DOB datetime,
sex tinyint  //=0,, -- 1 male 0 female,
locums tinyint  //=0,

citizen tinyint  //=0,
lang varchar(80),
licenses varchar(155),
dea tinyint //=0,
avail datetime,
pref_states varchar(50), pref_city varchar(50),

user_mod char(32), date_mod datetime

36*/
			$result = $this->adapter->query('call AddANurse(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ', 
			array(
			$name,
			$title,
			$ctct_phone,
			$ctct_ext1,
			$ctct_fax,
			$ctct_ext2,
			$ctct_cell,
			$ctct_pager,
			$ctct_ext3,
			$ctct_hphone,
			$ctct_hfax,
			$ctct_email,
			
			$ctct_addr_1,
			$ctct_addr_2,
			$ctct_addr_c,
			$ctct_addr_z,
			$ctct_st_code,
			
			$an_type,
			$bc,
			$an_bc_state,
			$an_certificates,
			$an_status,
			
			$an_cv_url,
			$an_experience,
			$an_DOB,			
			$an_sex,
			$an_locums,
			
			$an_citizen,
			$an_lang,
			$an_licenses,
			$an_dea,
			$ph_avail,
			$an_pref_states,
			$an_pref_city,
			$username,
			'NOW()'
			));
			if($result)
			{			
			foreach ($result as $row) 
			{
				$nurse_id=$row["id"];
				
			}
			}
			
			
		return $nurse_id;
	}
	
	public function getMidlevelDetails($id = 0, $identity) {    //get dr info
			$userid = $_COOKIE["phguid"];
            $ar = array();
			/*$this->table='vemplist';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$ar['user_name']=$row->ctct_name;
					$ar['user_email']=$row->ctct_email;					
				}
			}*/
			
			/*$this->table='veditan';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('an_id',$id);
			$resultSet = $this->selectWith($select);*/
			
			$resultSet = $this->adapter->query('select * from veditan as v JOIN dctalliedtypes AS d on v.an_type = d.at_code where an_id=?', 
			array(	$id		));
	
			if($resultSet)
			{ 
				foreach ($resultSet as $row) {
					$ar['an_id']=$row->an_id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$row->ctct_fax;
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_cell']=$row->ctct_cell;
					$ar['ctct_pager']=$row->ctct_pager;
					$ar['ctct_ext3']=$row->ctct_ext3;
					$ar['ctct_email']=$row->ctct_email;
					
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_hphone']=$row->ctct_hphone;
					$ar['ctct_hfax']=$row->ctct_hfax;
					$ar['ctct_bounces']=$row->ctct_bounces;
					
					$ar['an_type']=$row->an_type;
					$ar['an_bc']=$row->an_bc;
					$ar['an_bc_state']=$row->an_bc_state;
					$ar['an_certificates']=$row->an_certificates;
					$ar['an_status']=$row->an_status;
					$ar['an_cv_url']=$row->an_cv_url;
					$ar['an_dea']=$row->an_dea;
					$ar['an_experience']=$row->an_experience;
					$ar['an_DOB']=$row->an_DOB;
					$ar['an_sex']=$row->an_sex;
					$ar['an_citizen']=$row->an_citizen;
					$ar['an_lang']=$row->an_lang;
					$ar['an_licenses']=$row->an_licenses;
					$ar['an_avail']=$row->an_avail;
					$ar['an_pref_states']=$row->an_pref_states;
					$ar['an_pref_city']=$row->an_pref_city;					
					$ar['an_nonewsletter']=$row->an_nonewsletter;
					$ar['an_nospeclist']=$row->an_nospeclist;
					$ar['an_locums']=$row->an_locums;
					$ar['an_cv_text']=$row->an_cv_text;	
					//$ar['an_cv_date']=$row->an_cv_date;						
					
					$ar['at_abbr']=$row->at_abbr;
					$ar['at_name']=$row->at_name;
					
				}
			}
			$result = $this->adapter->query('select * from cvs2 where an_id = ? ORDER BY cv_id DESC LIMIT 1',
            array($id));
			if($result)
			{ 
				foreach ($result as $row) {
					$ar['cv_id']=$row->cv_id;
					$ar['filename']=$row->filename;
				}
			}	
			
		return $ar;	
    }
	
	
	public function editMidlevel($post, $identity) {             		
				$userid = $_COOKIE["phguid"];
			$username = urldecode($_COOKIE["username"]);
			$phar = array();
			$ctar = array();
			$wctar = array();
				
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;					
				}
			}	
			
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			
			if($post["ph_avail"]=='')
				$post["ph_avail"] = date('Y-m-d');
			
			//filter phone
			$post["ctct_phone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_phone"])));
			$post["ctct_hphone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_hphone"])));
			$post["ctct_hfax"] = str_replace('-','',$post["ctct_hfax"]);
			$post["ctct_cell"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_cell"])));
			$post["ctct_fax"] = str_replace('-','',$post["ctct_fax"]);
			$post["ctct_pager"] = str_replace('-','',$post["ctct_pager"]);

			$name=$post["an_lname"].", ".$post["an_fname"];
			$title=$post["ctct_title"];
			$ctct_phone=$post["ctct_phone"];
			$ctct_ext1=$post["ctct_ext1"];
			$ctct_fax=$post["ctct_fax"];
			$ctct_ext2=$post["ctct_ext2"];
			$ctct_cell=$post["ctct_cell"];
			$ctct_pager=$post["ctct_pager"];
			$ctct_ext3=$post["ctct_ext3"];
			$ctct_hphone=$post["ctct_hphone"];
			$ctct_hfax=$post["ctct_hfax"];
			$ctct_email=$post["ctct_email"];
			
			$ctct_addr_1=$post["ctct_addr_1"];
			$ctct_addr_2=$post["ctct_addr_2"];
			$ctct_addr_c=$post["ctct_addr_c"];
			$ctct_addr_z=$post["ctct_addr_z"];
			$ctct_st_code=$post["ctct_st_code"];
			
			$an_type=$post["an_type"];
			$bc=$post["an_bc"]; //always 0??
			$an_bc_state=$post["an_bc_state"];
			$an_certificates=$post["an_certificates"];
			$an_status=$post["an_status"];
			
			$an_cv_url=$post["an_cv_url"];
			$an_cv_text=$post["an_cv_text"];
			$an_experience=$post["an_experience"];
			$an_DOB=$post["an_DOB"];			
			$an_sex=$post["an_sex"];
			$an_locums=$post["an_locums"];
			
			$an_citizen=$post["an_citizen"];
			$an_lang=$post["an_lang"];
			$an_licenses=$post["an_licenses"];
			$an_dea=$post["an_dea"];
			$ph_avail=$post["ph_avail"];
			$an_pref_states=$post["an_pref_states"];
			$an_pref_city=$post["an_pref_city"];
			//$username;
			//NOW()
			//$post["an_avail"]=$post["ph_avail"]; //switch to an
			//$post["an_cv_date"]=$post["ph_cv_date"];
			
			//echo var_dump($post);
			foreach($post as $key=>$val)
			{
				if($key!="an_id" && $key!="an_cv_datetxt" && $key!="an_cv_date"  && $key!="an_type0" && $key!="an_availtxt" && $key!="an_1st_inqtxt" && $key!="submitedit" && $key!="an_xchg" && $key!="srs" && $key!="an_workaddr"){
					//$updatestr.=$key."='".$val."',";
					//$updatestr.=$key."=?,";
					if(strpos($key,"an_")!==false)
					{					
						$phar[]=$val;
						$an_updatestr.=$key."=?,";
					}
					if(strpos($key,"ctct_")!==false)
					{		
						if($key!="ctct_waddr_1" && $key!="ctct_waddr_2" && $key!="ctct_waddr_c" && $key!="wctct_st_code" && $key!="ctct_waddr_z")
						{
							$ctar[]=$val;
							$ct_updatestr.=$key."=?,";
						}
						else 
						{
							
						}
					}
				}				
			}
			
			
			//work address string
			//$wct_updatestr = " ctct_addr_1= '".$post["ctct_waddr_1"]."', ctct_addr_2= '".$post["ctct_waddr_2"]."', ctct_addr_c= '".$post["ctct_waddr_c"]."', ctct_st_code= '".$post["wctct_st_code"]."', ctct_addr_z= '".$post["ctct_waddr_z"]."' ";
			$phar[]=$username;
			$ctar[]=$username;
			$phar[]=$post["an_id"];
			$ctar[]=$post["an_id"]; //$id;
			$an_updatestr .= "an_user_mod = ?, an_date_mod = now()";
			$ct_updatestr .= "ctct_user_mod = ?, ctct_date_mod = now()";
			
			//echo $an_updatestr;
			//echo var_dump($phar);			
			//echo $ct_updatestr;
			//echo var_dump($ctar);
			
			$result = $this->adapter->query('update lstalliednurses set '.$an_updatestr.' where an_id= ? LIMIT 1', $phar);	
			$result = $this->adapter->query('update lstcontacts set '.$ct_updatestr.' where ctct_id= (select an_ctct_id from lstalliednurses where an_id = ? ) and ctct_id <> 9 LIMIT 1', $ctar);
		
			/*if($result)
			{			
			foreach ($result as $row) 
			{
				$nurse_id=$row["id"];
				
			}
			}*/
			
			
		return true;
	}
	
	//pass to recruiter
	public function passTo($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		/*sql0 = "insert into tNuPasses (np_an_id,np_emp_from,np_date,np_src_id,np_emp_to) values " _
	& "(" & CLng(Request("an_id")) & "," & Session("emp_id") & ",'" _
	& Now & "'," & CLng(Request("src")) & ","
      for j = 1 to Request.Form("to").Count step 1
         sql = sql0 & CLng(Request.Form("to")(j)) & ")"*/
		//echo var_dump($post["to"]);
		
		foreach ($post["to"] as $key=>$val)
		{
		
			$to_id = $val;
			if($to_id>0 && $userid!='' && $post["an_id"]!='')
			{
			$result = $this->adapter->query('insert into tnupasses (np_an_id,np_emp_from,np_date,np_src_id,np_emp_to) values (?,?,NOW(),?,?)', 
			array(
			$post["an_id"],
			$userid,
			//$post[""],
			$post["pass_source"],
			$to_id
			));	
			
			$post["activ_type"]="17";
			$post["sched_act_notes"]="Midlevel Pass from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$to_id; //to
			$post["act_ref"]=$post["an_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];	
		
			$this->addActivity($post, $identity);
			}
			else { return false; }
		}
		
		
		return true;
	}
	
	public function getPasses($id = 0) {           
			
			
			$result = $this->adapter->query('SELECT * FROM vanpasseslist where np_an_id = ? order by np_date desc',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['pp_date']=$row->np_date;
					$ar[$i]['rec_from']=$row->rec_from;
					$ar[$i]['rec_to']=$row->rec_to;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//createPresent
	public function createPresent($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		/*sql0 = "insert into tNuPasses (np_an_id,np_emp_from,np_date,np_src_id,np_emp_to) values " _
	& "(" & CLng(Request("an_id")) & "," & Session("emp_id") & ",'" _
	& Now & "'," & CLng(Request("src")) & ","
      for j = 1 to Request.Form("to").Count step 1
         sql = sql0 & CLng(Request.Form("to")(j)) & ")"*/
		//echo var_dump($post["to"]);
				
			
		if($post["an_id"]!='' && $post["pres_status"]!=7 && $post["pres_ctr_id"]!='') //7 is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_an_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["pres_ctr_id"],
			$userid,
			$post["an_id"],
			$post["pres_status"],
			$post["pres_date"],
			1
			));	
			
			$result = $this->adapter->query('select * from vphquestemail where ctr_id = ?', array($post["pres_ctr_id"]));
			
			foreach ($result as $row) 
			{			
				$post["cliname"]=$row->cli_name.", ".$row->ctr_location_c." ".$row->ctr_location_s;				
				$post["recid"]=$row->emp_id;
				$post["recname"]=$row->rec_name;	
				$post["recfax"]=$row->ctct_fax;	
				$post["recphone"]=$row->ctct_phone;	
				$post["recemail"]=$row->ctct_email;	
				$post["rectitle"]=$row->ctct_title;	
				$post["ctrno"]=$row->ctr_no;					
							
			}
			
			$post["activ_type"]="2";
			$post["sched_act_notes"]="Midlevel Present from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$to_id; //to
			$post["act_ref"]=$post["an_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=1;
			//$post["addon"];
			
			/*
			cliname = RS("cli_name") & ", " & RS("ctr_location_c") & ", " & RS("ctr_location_s")
			recid = RS("emp_id")
			recname = RS("rec_name")
			recfax = RS("ctct_fax")
			recphone = RS("ctct_phone")
			recemail = RS("ctct_email")
			rectitle = RS("ctct_title")
			ctrno = RS("ctr_no")
			*/
		
			//addActivity($post, $identity);
			
			$this->sendPresent($post); 
		}
			else { return false; }		
		
		
		return true;
	}
	
	//create Locum Tenens Present
	public function createLocumsPresent($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		
		//echo var_dump($post["to"]);
				
			
		if($post["an_id"]!='' && $post["pres_status"]!=7 && $post["pres_ctr_id"]!='') //7 is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_an_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["pres_ctr_id"],
			$userid,
			$post["an_id"],
			$post["pres_status"],
			$post["pres_date"],
			1
			));	
			
			$result = $this->adapter->query('select * from vphquestemail where ctr_id = ?', array($post["pres_ctr_id"]));
			
			foreach ($result as $row) 
			{			
				$post["cliname"]=$row->cli_name.", ".$row->ctr_location_c." ".$row->ctr_location_s;				
				$post["recid"]=$row->emp_id;
				$post["recname"]=$row->rec_name;	
				$post["recfax"]=$row->ctct_fax;	
				$post["recphone"]=$row->ctct_phone;	
				$post["recemail"]=$row->ctct_email;	
				$post["rectitle"]=$row->ctct_title;	
				$post["ctrno"]=$row->ctr_no;					
							
			}
			
			$post["activ_type"]="2";
			$post["sched_act_notes"]="Midlevel Locums Present from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid;//$to_id; //to
			$post["act_ref"]=$post["an_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=1;
			$post["act_ret"]=2; //ref_type, 
			//$post["addon"];			
		
			//$this->addActivity($post, $identity);
			
			$this->sendPreInterview($post); 
		}
		else { return false; }			
		
		return true;
	}
	
	
	//pass to locums
	public function createLocumsPass($post, $identity) {             		
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
		if($userid=='')
			return false;
			
		$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$post["emp_id"]);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$to=$row->emp_uname;
					$dept=$row->emp_dept;					
				}
			}
		
		$note = "<em>Automatic Note:</em> Provider pass to locums by <b>".$realname."</b>";
		
		$result = $this->adapter->query('insert into allnotes (note_user, note_ref_id, note_type, note_emp_id, note_text, note_reserved, note_update)  values (?,?,?,?,?,?,?)', 
			array(			
				$username,
				$post["an_id"],
				15,
				$userid,
				$note,
				NULL,
				0
			));
			
		$result = $this->adapter->query('insert into tvistapasses (vp_type, vp_ref_id, vp_emp_id)  values (?,?,?)', 
			array(			
			15,			
			$post["an_id"],
			$post["emp_id"]		
			));	
				
		
		//send email
		$headers = "From: locumpass@phg.com \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail."\n";
			
			$subject = "Provider Pass from PHG";
			
			$message = "Provider Lead Pass - PHG to Pinnacle Locums\n\n
PHG contact name: ".$post["empname"]."
Best time to call to discuss: ".$post["emptime"]."\n\n
Step 1
	Profider Name: ".$post["phname"].", ".$post["title"].".
	Address 1: ".$post["caddr1"]."
	Address 2: ".$post["caddr2"]."
	City, State, Zip: ".$post["caddr3"]."
	Phone Number: ".$post["cphone"]."
	Email Address: ".$post["cemail"]."
\nStep 2
	Specialty: ".$post["spec"]."
	Board certified/Board eligible: ".$post["bcbe"]."
	Licenses: ".$post["licenses"]."
\nStep 3
	Talked to Dr. about locums? ".$post["talked"]."
	Currently working with PHG? ".$post["current"]."
	If yes, go through PHG contact first? ".$post["through"]."
\nStep 4
	Current situation/type of position desired:
	".$post["situation"]." 
	Best time to call: ".$post["besttime"]."\n\n
--
This message was sent automatically. Please reply, if you have any questions.\n\n
REF_ID#".$post["an_id"]."";
		
		mail($to, $subject, $message, $headers);
		
		
		return true;
	}
	
	
	
	
	
	
	//send pre interview email, etc.
	public function sendPreInterview($post) 
	{
		$realname = $_COOKIE["realname"];
		$username = $_COOKIE["username"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}
		/*my $cid = $Request->{phid}->Item;
	my $recid = $Request->{recid}->Item;
	my $recph = $Request->{recphone}->Item;
	my $recfx = $Request->{recfax}->Item;
	my $rectt = ($Request->{rectitle}->Item or "Search Consultant");
	my $retref = $Request->{retref}->Item;
	my $recem = $Request->{recemail}->Item;
	my $recemail = uf_uristr($recem);
	my $cno = $Request->{ctrno}->Item;
	my $idate = $Request->{idate}->Item;
	my $realuser = $Session->{UserName};
	my $bnu = $Request->{nurse}->Item;
	my $dr = $bnu?'':' Dr.';
	my $ext = $bnu?'ext':'';
	*/		$rector = 0;
			//$cemail = $post["phemail"];
			$addon = $post["addon"];
			$cliname = $post["cliname"];
			$cname = $post["an_name"];
			$cid = $post["an_id"]; //change to ph_id for dr
			//$post["cliname"];				
			$recid = $post["recid"];
			$recname = $post["recname"];	
			$recfx = $post["recfax"];	
			$recph = $post["recphone"];	
			$recem = $post["recemail"];	
			$rectt = $post["rectitle"];	
			$retref = ''; //not sure what this is for...blank in old system
			$cno = $post["ctrno"];		
			$idate = $post["pres_date"];
			//$post["act_user"]; //to
			$realuser = urldecode($_COOKIE["realname"]);
			$bnu = $post["nurse"];
			$dr = ''; //not a dr.
			$ext = 'ext'; //for a nurse...part of the link below
				
			
		$rr00 = 'rrector@phg.com';
		$rr01 = 'jpolver@phg.com';
		//$to = "preinterview@phg.com";
		
		$to = $useremail; //"nturner@phg.com"; //FOR NOW, CHANGE LATER to 
		
		$hascv='';
		if($addon=='YES')
		{
			$hascv = 'has a CV'; 
			$cemail = $rr01;
		}
		if($addon=='NO' || $addon=='')
		{
			$hascv = 'does not have CV'; 
			$cemail = $rr00;
		}
		
		
			$headers = "From: speclistret@phg.com \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail.",".$cemail.",jcouvillon@phg.com\n";
			$headers .= "BCC:nturner@phg.com\n";
			
			//$subject = "Pre-Interview Form";
			//$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			
		
		/*if($hascv!='')
		{
			$subject = "[DB] New Present Notification";
			
			$message = "New present has been made in the database. Recruiter $hascv.\n
			Client: $cliname
			Date: $idate
			Contract: $cno
			Ph.ID#: $cid
			Name: $cname

			Recruiter $recname $hascv
			Please email $recem for details.";
		}*/
		//else //CV Yes/No left blank
		//{
			$subject = "Pre-Interview Form";
			$message = "
Dear $dr $cname:\n

In order to proceed with your upcoming interview with
$cliname,
scheduled for $idate, Pinnacle Health Group will require
some additional information from you to prepare our client for the interview.
To facilitate this process, we have designed a short form.\n

To complete the form, follow the link:\n

http://www.phg.com/pre_interview$ext.php?a=$cid&b=$recid&c=$cno \n  

In an effort to preserve the integrity of the reference process,
Pinnacle Health Group's Credentialing Department performs all
reference checks. By submitting this questionnaire you are 
authorizing Pinnacle Health Group to procure reference
reports. This information may be used for employment purposes.

Please note that all interviewing providers must complete this form
before their interview can take place. Since this message is
automatically sent out, you may have completed the form or
communicated it to us already. If this is the case,
please disregard this e-mail. If you encounter any problems,
please contact me at my telephone number shown below.\n

Thank you for your timely response.\n

Sincerely,\n

$recname
$rectt
Pinnacle Health Group
Phone: $recph Fax: $recfx
Toll-Free: 800-492-7771
Email: $recem";
		//}

			//echo $subject;
			/*mail send*/
			mail($to, $subject, $message, $headers); //add john to this
	}
	
	public function sendPresent($post) 
	{
		$realname = $_COOKIE["realname"];
		$username = $_COOKIE["username"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}
			$rector = 0;
			//$cemail = $post["phemail"];
			$addon = $post["addon"];
			$cliname = $post["cliname"];
			$cname = $post["an_name"];
			$cid = $post["an_id"]; //change to ph_id for dr
			//$post["cliname"];				
			$recid = $post["recid"];
			$recname = $post["recname"];	
			$recfx = $post["recfax"];	
			$recph = $post["recphone"];	
			$recem = $post["recemail"];	
			$rectt = $post["rectitle"];	
			$retref = ''; //not sure what this is for...blank in old system
			$cno = $post["ctrno"];		
			$idate = $post["pres_date"];
			//$post["act_user"]; //to
			$realuser = urldecode($_COOKIE["realname"]);
			$bnu = $post["nurse"];
			$dr = ''; //not a dr.
			$ext = 'ext'; //for a nurse...part of the link below
				
			
		$rr00 = 'rrector@phg.com';
		$rr01 = 'jpolver@phg.com';
		//$to = "preinterview@phg.com";
		
		$to = $useremail; //"nturner@phg.com"; //FOR NOW, CHANGE LATER to 
		
		$hascv='';
		if($addon=='YES')
		{
			$hascv = 'has a CV'; 
			$cemail = $rr01;
		}
		if($addon=='NO' || $addon=='')
		{
			$hascv = 'does not have CV'; 
			$cemail = $rr00;
		}
		
		
			$headers = "From: speclistret@phg.com \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail.",".$cemail.",jcouvillon@phg.com\n";
			$headers .= "BCC:nturner@phg.com\n";
			
			//$subject = "Pre-Interview Form";
			//$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			
		
		//if($hascv!='')
		//{
			$subject = "[DB] New Present Notification";
			
			$message = "New present has been made in the database. Recruiter $hascv.\n
			Client: $cliname
			Date: $idate
			Contract: $cno
			Ph.ID#: $cid
			Name: $cname

			Recruiter $recname $hascv
			Please email $recem for details.";
		//}
		

			//echo $subject;
			/*mail send*/
			mail($to, $subject, $message, $headers); //add john to this
	}
	
	//createInterview
	public function createInterview($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		
				
			
		if($post["an_id"]!='' && $post["int_status"]!=7 && $post["int_ctr_id"]!='') //7 is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_an_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["int_ctr_id"],
			$userid,
			$post["an_id"],
			$post["int_status"],
			$post["int_date"],
			1 //is nurse
			));	
			
			$result = $this->adapter->query('select * from vphquestemail where ctr_id = ?', array($post["int_ctr_id"]));
			
			foreach ($result as $row) 
			{			
				$post["cliname"]=$row->cli_name.", ".$row->ctr_location_c." ".$row->ctr_location_s;				
				$post["recid"]=$row->emp_id;
				$post["recname"]=$row->rec_name;	
				$post["recfax"]=$row->ctct_fax;	
				$post["recphone"]=$row->ctct_phone;	
				$post["recemail"]=$row->ctct_email;	
				$post["rectitle"]=$row->ctct_title;	
				$post["ctrno"]=$row->ctr_no;					
							
			}
			
			$post["activ_type"]="2";
			$post["sched_act_notes"]="Midlevel Interview from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["an_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=1;
			//$post["addon"]; //blank for this one
					
			
			$this->sendPreInterview($post);
			$this->addActivity($post, $identity);			
		}
			else { return false; }		
		
		
		return true;
	}
	
	//add Placement
	public function addPlacement($post, $identity) 
	{
		$userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}
		
		$phid = $post["an_id"]; //ph_id / an_id
		$plid = $post["pl_id"]; //placement id  / pipl ID
		
		//echo var_dump($post);
		
		$phnm = $post["ph_nm"];
		$ctrno = $post["ctr_no"];
		$ctrspec = $post["ctr_spec"];
		$ctrreq = $post["ctr_req"];
		$ctrlocc = $post["ctr_locc"];
		$ctrlocs = $post["ctr_locs"];
		
		$post["pl_guar_net"] = $post["pl_guar_net"] == null ? 0 : 1;
		$post["pl_guar_gross"] = $post["pl_guar_gross"] == null ? 0 : 1;
		//$post["pl_annual"] = $post["pl_annual"] == null ? 0 : 1;
		//$post["pl_guar"] = $post["pl_guar"] == null ? 0 : 1;
		$post["pl_incent"] = $post["pl_incent"] == null ? 0 : 1;
		$post["pl_met_coll"] = $post["pl_met_coll"] == null ? 0 : 1;
		$post["pl_met_pro"] = $post["pl_met_pro"] == null ? 0 : 1;
		$post["pl_met_num"] = $post["pl_met_num"] == null ? 0 : 1;
		$post["pl_based_ass"] = $post["pl_based_ass"] == null ? 0 : 1; 
		$post["pl_based_rec"] = $post["pl_based_rec"] == null ? 0 : 1; 
		$post["pl_based_sto"] = $post["pl_based_sto"] == null ? 0 : 1;

		$post["pl_health"] = $post["pl_health"] == null ? 0 : 1;
		$post["pl_dental"] = $post["pl_dental"] == null ? 0 : 1; 
		$post["pl_fam_health"] = $post["pl_fam_health"] == null ? 0 : 1;
		$post["pl_fam_dental"] = $post["pl_fam_dental"] == null ? 0 : 1;
		$post["pl_st_dis"] = $post["pl_st_dis"] == null ? 0 : 1;
		$post["pl_lt_dis"] = $post["pl_lt_dis"] == null ? 0 : 1;
		$post["pl_life"] = $post["pl_life"] == null ? 0 : 1;
		//$post["pl_oth_ben"] = $post["pl_oth_ben"] == null ? 0 : 1;
		
		//$plid=''; //remove
		if($plid>0 && trim($plid)!='') //update
		{
		//echo "HERE";
			$result = $this->adapter->query('UPDATE tplacements SET 
			pl_ref_emp=?, pl_src_id=?, pl_term=?, pl_guar_net=?, pl_guar_gross=?, pl_annual=?, pl_guar=?, pl_incent=?,
			pl_met_coll=?, pl_met_pro=?, pl_met_num=?, pl_met_oth=?, pl_partner=?, pl_partner_yrs=?, pl_buyin=?, pl_based_ass=?, pl_based_rec=?,
			pl_based_sto=?, pl_based_oth=?, pl_loan=?, pl_vacat=?, pl_cme_wks=?, pl_cme=?, pl_reloc=?, pl_health=?, pl_dental=?, pl_fam_health=?, pl_fam_dental=?,
			pl_st_dis=?, pl_lt_dis=?, pl_life=?, pl_oth_ben=?, pl_replacement=?, pl_emp_id=?, pl_user_mod=?, pl_signing=?, pl_source=?,
			pl_exp_years=?, pl_split_emp=?, pl_text1=?, pl_text2=?, pl_text3=?, pl_text4=?, pl_date_mod=NOW()
			WHERE pl_id=? LIMIT 1', 
			array(		
$post["pl_ref_emp"], 
$post["pl_src_id"],
$post["pl_term"],
$post["pl_guar_net"], 
$post["pl_guar_gross"],
$post["pl_annual"],
$post["pl_guar"], 
$post["pl_incent"],
$post["pl_met_coll"],
$post["pl_met_pro"],
$post["pl_met_num"],
$post["pl_met_oth"],  
$post["pl_partner"], 
$post["pl_partner_yrs"], 
$post["pl_buyin"], 
$post["pl_based_ass"], 
$post["pl_based_rec"], 
$post["pl_based_sto"],
$post["pl_based_oth"], 
$post["pl_loan"],
$post["pl_vacat"], 
$post["pl_cme_wks"], 
$post["pl_cme"], 
$post["pl_reloc"], 
$post["pl_health"], 
$post["pl_dental"], 
$post["pl_fam_health"], 
$post["pl_fam_dental"],
$post["pl_st_dis"], 
$post["pl_lt_dis"], 
$post["pl_life"],
$post["pl_oth_ben"], 
$post["pl_replacement"], 
$userid, 
$username, 
$post["pl_signing"], 
$post["pl_source"],
$post["pl_exp_years"],
$post["pl_split_emp"],
$post["pl_text1"],
$post["pl_text2"],
$post["pl_text3"],
$post["pl_text4"],
$plid		
));	
		}
		else{

//echo "HERE2";
			//AddAPlacement			
			$result = $this->adapter->query('call AddAPlacement (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
			array(			
$post["pl_sent_date"], //-- null
$post["pl_date"], //-- -> pl_date
$post["pl_ref_emp"], //-- null
$post["pl_source"], //-- null
$post["pl_term"], //--null
$post["pl_guar_net"], 
$post["pl_guar_gross"],
$post["pl_annual"], 
$post["pl_guar"], //-- null,null
$post["pl_incent"], 
$post["pl_met_coll"], 
$post["pl_met_pro"], 
$post["pl_met_num"],
$post["pl_met_oth"], //-- null
$post["pl_partner"],
$post["pl_partner_yrs"], 
$post["pl_buyin"], //null
$post["pl_based_ass"], 
$post["pl_based_rec"], 
$post["pl_based_sto"],
$post["pl_based_oth"], //-- null
$post["pl_loan"],
$post["pl_vacat"], 
$post["pl_cme_wks"], //null
$post["pl_cme"], 
$post["pl_reloc"], //null
$post["pl_health"], 
$post["pl_dental"], 
$post["pl_fam_health"], 
$post["pl_fam_dental"],
$post["pl_st_dis"], 
$post["pl_lt_dis"], 
$post["pl_life"],
$post["pl_oth_ben"], //null
$post["pl_replacement"], 
$userid, //$post["pl_emp_id"],
$post["pl_signing"], 
$post["pl_exp_years"],
$post["pl_split_emp"],
$post["pl_src_id"],

$post["pl_text1"],
$post["pl_text2"],
$post["pl_text3"],
$post["pl_text4"],
$username, 
'NOW()', //date_mod
$post["ctr_id"], 
null, //ph_id
$post["an_id"] 			));	

if($result)
{			
	foreach ($result as $row) 
	{
		$plid=$row["pl_id"];				
	}
}
			

			
			//$to = $useremail; //"placements@phg.com, phgmarketing@phg.com, allusers@phg.com"; //FOR NOW, CHANGE LATER to 	
			$to = "placements@phg.com, phgmarketing@phg.com, allusers@phg.com";
			
			$headers = "From: ".$useremail." \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail."\n";				
		$headers .= "BCC:nturner@phg.com,jcouvillon@phg.com\n";
			$subject = "Subject: DB Placement Notification from ".$username;
			
			$message = "New placement has been entered into the Database\nPlacement Info:\n";
			$message .= "Contract: $ctrno ($ctrspec) $ctrlocc, $ctrlocs\nCandidate: $phnm\n\n";
			$message .= "Click on the link below to get the Placement Report:\n\n";
			$message .= "http://testdb.phg.com/public/report/placement2/$plid?isan=1\n";
		}
			//echo $subject;
			/*mail send*/
			mail($to, $subject, $message, $headers); //add john to this
			
			//$post["activ_type"]=4;
			//$this->addActivity($post, $identity);
			
			return true;
	}
	
	public function updateCredentialsDate($post, $identity) {             		
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
		if($userid=='' || $post["cdate_date"]=='')
			return false;
		if(isset($_POST["ref_date_submit_btn"])){
			$result = $this->adapter->query('update lstalliednurses set an_ref_submit=?, an_ref_client=?, an_ref_recr=?, an_ref_hold=?,an_user_mod=?,an_date_mod=NOW() where an_id = ?', 
			array(			
			$post["cdate_date"],			
			$post["client_tmp"],
			$post["client2_tmp"],
			$post["client4_tmp"],			
			$username,
			$post["an_id"]
			));
		}
		if(isset($_POST["ama_date_submit_btn"])){			
			$client_ama = $post["client3_tmp"];
			$result = $this->adapter->query('update lstalliednurses set an_ama_submit=?,an_ama_client=?,an_user_mod=?,an_date_mod=NOW() where an_id = ?', 
			array(			
			$post["cdate_date"],
			$client_ama,
			$username,
			$post["an_id"]
			));
		}
		if(isset($_POST["preint_date_submit_btn"])){
			$result = $this->adapter->query('update lstalliednurses set an_preint_got=?,an_user_mod=?,an_date_mod=NOW() where an_id = ?', 
			array(			
			$post["cdate_date"],			
			$username,
			$post["an_id"]
			));
		}
		if(isset($_POST["packet_date_submit_btn"])){
			$result = $this->adapter->query('update lstalliednurses set an_completed=?,an_user_mod=?,an_date_mod=NOW() where an_id = ?', 
			array(			
			$post["cdate_date"],			
			$username,
			$post["an_id"]
			));
		}
		
		
		return true;
	}//end
	
	
	public function getAssessment($id, $ctr_id) {             		
			/*$userid = $_COOKIE["phguid"];
			$realname = $_COOKIE["realname"];
			$username = urldecode($_COOKIE["username"]);
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;					
				}
			}	*/
			$ar = array();
			

			
		   
		   $result = $this->adapter->query('select as_date,as_motiv,as_goals,as_family,as_hobby,as_finobj,as_items from tassesments where as_ph_id = ?  and as_ctr_id = ?  and as_nurse = 1 ', array($id, $ctr_id));			
			
			if($result)
			{			
				foreach ($result as $row) {
					$ar["ph_id"] =$id;
					$ar["ctr_id"] =$ctr_id;
					$ar["as_date"] =$row->as_date;
					$ar["as_motiv"] =$row->as_motiv;
					$ar["as_goals"] =$row->as_goals;
					$ar["as_family"] =$row->as_family;
					$ar["as_hobby"] =$row->as_hobby;
					$ar["as_finobj"] =$row->as_finobj;
					$ar["as_items"] =$row->as_items;	
					$ar["waseof"] =false; //what
				}
			} 
			$result = $this->adapter->query('select * from vanassessfrm where an_id = ?  and ctr_id = ?   ', array($id, $ctr_id));			
			
			if($result)
			{			
				foreach ($result as $row) {
					
					$ar["ctct_name"] =$row->ctct_name;
					$ar["ctct_title"] =$row->ctct_title;
					$ar["ctr_no"] =$row->ctr_no;					
				}
			}  
			
			
				
			return $ar;
    }
	
	
	public function addAssessment($post, $identity) {             		
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
		$result = $this->adapter->query('insert into tassesments (as_ph_id, as_ctr_id, as_nurse, as_date, as_motiv, as_goals, as_family, as_hobby, as_finobj, as_items, as_idx, as_user_mod, as_date_mod) values (?,?,?,?,?,?,?,?,?,?,?,?,NOW())', 
		array(		
		$post["ph_id"],
		$post["ctr_id"],
		1,
		$post["as_date"],
		$post["as_motiv"],
		$post["as_goals"],
		$post["as_family"],
		$post["as_hobby"],
		$post["as_finobj"],
		$post["as_items"],
		$post["as_idx"]=1, //what is this
		$username
		));		
		
		$result = $this->adapter->query('update lstalliednurses set an_assesm=?,an_ca_not=0,an_user_mod=?,an_date_mod= NOW() where an_id = ?', 
		array(
		$post["as_date"],
		$username,
		$post["ph_id"],
		));				
			if($result)
			{		
			}
		return true;
	}
	
	//new get billing
	public function getBilling($ctr_id, $an_id) {             
			
			
			$result = $this->adapter->query('call GetReferenceReq3(?,?)',
            array($an_id, $ctr_id));
			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar['rn']=$row->rn;
					$ar['no']=$row->no;
					$ar['sp']=$row->sp;
					$ar['cn']=$row->cn;
					$ar['cc']=$row->cc;
					$ar['cs']=$row->cs;
					$ar['pn']=$row->pn;
					$ar['px']=$row->px;
					$ar['pp']=$row->pp;
					$ar['pc']=$row->pc;
					$ar['ps']=$row->ps;
					$ar['n1']=$row->n1;
					$ar['r1']=$row->r1;
					$ar['c1']=$row->c1;
					$ar['s1']=$row->s1;
					$ar['o1']=$row->o1;
					$ar['h1']=$row->h1;
					$ar['n2']=$row->n2;
					$ar['r2']=$row->r2;
					$ar['c2']=$row->c2;
					$ar['s2']=$row->s2;
					$ar['o2']=$row->o2;
					$ar['h2']=$row->h2;
					$ar['n3']=$row->n3;
					$ar['r3']=$row->r3;
					$ar['c3']=$row->c3;
					$ar['s3']=$row->s3;
					$ar['o3']=$row->o3;
					$ar['h3']=$row->h3;
					$ar['n4']=$row->n4;
					$ar['r4']=$row->r4;
					$ar['c4']=$row->c4;
					$ar['s4']=$row->s4;
					$ar['o4']=$row->o4;
					$ar['h4']=$row->h4;
					
				}
			}
			
			return $ar;
    }
	
	public function getAMACheck($ctr_id, $an_id) {             
			
			
			$result = $this->adapter->query('SELECT * FROM vanreqama WHERE ph_id=? AND ctr_id=? ',
            array($an_id, $ctr_id));
			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar['an_id']=$row->ph_id;
					$ar['ph_id']=$row->ph_id;
					$ar['an_id']=$row->ph_id;					
					$ar['ph_DOB']=$row->ph_DOB;
					$ar['ph_med_school']=$row->ph_med_school;
					$ar['ph_name']=$row->ph_name;
					$ar['ph_title']=$row->ph_title;
					$ar['ph_addr1']=$row->ph_addr1;
					$ar['ph_addr2']=$row->ph_addr2;
					$ar['ph_city']=$row->ph_city;
					$ar['ph_zip']=$row->ph_zip;
					$ar['ph_state']=$row->ph_state;
					$ar['ctr_id']=$row->ctr_id;
					$ar['ctr_no']=$row->ctr_no;
					$ar['at_abbr']=$row->at_abbr;
					$ar['at_name']=$row->at_name;
					
					$ar['ctr_spec']=$row->ctr_spec;
					$ar['sp_name']=$row->sp_name;
					$ar['cli_name']=$row->cli_name;
					$ar['cli_city']=$row->cli_city;
					$ar['cli_state']=$row->cli_state;
					$ar['req_name']=$row->req_name;
					
					
				}
			}
			
			return $ar;
    }
	
	public function getCandidateAssessment($post) {             
			
			
			$result = $this->adapter->query('select * from vanassessrpt where ph_id = ?',
            array($post["ph_id"]));
			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar['ph_id']=$row->ph_id;
					$ar['phname']=$row->phname;
					$ar['phtitle']=$row->phtitle;
					$ar['ctr_id']=$row->ctr_id;				
					$ar['ctr_no']=$row->ctr_no;
					$ar['cliname']=$row->cliname;		
					$ar['addr1']=$row->addr1;		
					$ar['addr2']=$row->addr2;		
					$ar['city']=$row->city;		
					$ar['zip']=$row->zip;	
					$ar['state']=$row->state;	
					$ar['spec']=$row->spec;		
					$ar['specn']=$row->specn;	
					$ar['reqname']=$row->reqname;							
					$ar['reqtitle']=$row->reqtitle;		
					$ar['as_date']=$row->as_date;		
					$ar['motiv']=$row->motiv;		
					$ar['goals']=$row->goals;	
					$ar['fam']=$row->fam;		
					$ar['hob']=$row->hob;	
					$ar['items']=$row->items;		
					$ar['lic']=$row->lic;						
				}
			}
			
			return $ar;
    }
	
	public function getSourceHistory($id) {             
			
			
			$result = $this->adapter->query('select * from vansourcesn where nsr_an_id = ?',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['nsr_id']=$row->nsr_id;
					$ar[$i]['nsr_an_id']=$row->nsr_an_id;
					$ar[$i]['nsr_date']=$row->nsr_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['nsr_source']=$row->nsr_source;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['nsr_dm_code']=$row->nsr_dm_code;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//add source code for midlevels
	public function addSource($post, $identity) {             		
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
		
		
	  
		if($post["is_dm"]==1){
			$src="Direct Mail"; 
			$dm_code="".$post["dm_code"]."";
		}
		elseif($post["is_dm"]==2)
			$src="NAPR";
		elseif($post["is_dm"]==3)
			$src="www.phg.com";
		elseif($post["is_dm"]==4)
			$src="Fuzion";
		elseif($post["is_dm"]==5)
			$src="Physician Work";
		elseif($post["is_dm"]==6)
			$src="Email Campaigns";
		elseif($post["source"]!='')
			$src="".$post["source"]."";
		
		if($post["an_id"]=='' || $post["addsource_ctr_id"]=='') //fail
			return false;
			
			$result = $this->adapter->query('insert into tnusourcesn (nsr_ctr_id, nsr_an_id, nsr_date, nsr_source, nsr_emp_id, nsr_dm_code) values (?,?,?,?,?,?)', 
			array(			
			$post["addsource_ctr_id"],
			$post["an_id"],
			$post["addsource_date"],
			$src,
			$userid,
			$dm_code,
			));			
		
		
		return true;
	}
	
	//createPending
	public function createPending($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
								
			
		if($post["ph_id"]!='' && $post["pend_status"]!=7 && $post["ctr_id"]!='') // is a fake from somewhere?
		{
			//insert into tCtrPIPL (pipl_ctr_id,pipl_emp_id,pipl_an_id,pipl_status,pipl_date,pipl_nurse) values
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_an_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["ctr_id"],
			$userid,
			$post["ph_id"],
			$post["pend_status"],
			$post["pend_date"],
			1
			));	
			
		}
			else { return false; }		
		
		
		return true;
	}

}
