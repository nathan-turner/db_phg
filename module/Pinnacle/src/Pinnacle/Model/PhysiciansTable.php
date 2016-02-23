<?php
// module/Pinnacle/src/Pinnacle/Model/PhysiciansTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class PhysiciansTable extends Report\ReportTable
{

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vphlookupsmall',
            '`ph_id`, `ctct_name`, `ctct_title`, `st_name`, `phone`, `ctct_addr_c`, `ctct_st_code`, `ph_spec_main`, `ph_status`, `ph_recruiter`, `ph_licenses`, `ph_cv_date`, `ph_src_date`, `ph_pref_state`, `ph_pref_region`, `ph_subspec`, `ph_datasrc`, `ph_citizen`, `ph_practice`, `ph_skill`, `ph_locums`, ctct_id');
    }
    
    /**
     * @param array $ar     keys: see PhysiciansForm
    */
    public function buildQuery(array $ar = null) {
        if( is_array($ar) && $ar['emp_id'] ) {
            $where = new Where();
            $nduh = 0;

            if( trim($ar['ph_id']) ) {
                $where->equalTo('ph_id',$ar['ph_id']);
                return $where;
            }

            if( trim($ar['ph_lname']) && trim($ar['ph_fname']) ) {
                $ln = trim($ar['ph_lname']); $fn = trim($ar['ph_fname']);
                $like1 = addslashes("$ln%, $fn%");
                $like2 = addslashes("$fn% $ln%");
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }
            elseif( trim($ar['ph_lname']) ) {
                $ln = trim($ar['ph_lname']);
                $like1 = addslashes("$ln%"); // will also find some first names
                $like2 = addslashes("% $ln%"); // and stuff like III,Jr,Esq,etc
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }
            elseif( trim($ar['ph_fname']) ) {
                $fn = trim($ar['ph_fname']);
                $like1 = addslashes("$fn%"); // will also find last names
                $like2 = addslashes("%, $fn%"); // and II,III,Jr.,esq.,etc.
                $where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
                $nduh++;
            }

            if( $ar['ph_spec_main'] ) {
                $spec = trim(substr($ar['ph_spec_main'],0,3));
                $skill = substr($ar['ph_spec_main'],3);
                $where->equalTo('ph_spec_main',$spec); $nduh++;
                if( $skill && $skill !== '--' && $skill !== '  ' ) $where->equalTo('ph_skill',$skill); 
            }
            if( strlen(trim($ar['ph_subspec'])) > 2 ) {
                $like = addslashes(trim($ar['ph_subspec']));
                $where->like('ph_subspec',"%$like%"); $nduh++;
            }
            if( $ar['ph_src_date'] ) {
                $ival = '';
                switch( $ar['ph_src_date'] ) {
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
                    $where->literal('ph_src_date >= CURDATE()-'.$ival); $nduh++;
                }
            }
            if( $ar['orcond'] && ($ar['ph_pref_state']||$ar['ph_licenses']||$ar['ctct_st_code']) ) {
                // or cond. weird syntax.
                $nest = $where->nest();
                if( $ar['ph_pref_state'] ) {
                    $like = addslashes(trim($ar['ph_pref_state']));
                    $nest = $nest->like('ph_pref_state',"$like%")->or; $nduh++;
                }
                if( $ar['ph_licenses'] ) {					
						$like = addslashes(trim($ar['ph_licenses']));
						$nest = $nest->like('ph_licenses',"%$like%")->or; $nduh++;
					
                }
                if( $ar['ctct_st_code'] ) { $nest = $nest->equalTo('ctct_st_code',$ar['ctct_st_code']); $nduh++; }
                $nest->unnest()->and;
            }
            else {
                if( $ar['ph_pref_state'] ) {
                    $like = addslashes(trim($ar['ph_pref_state']));
                    $where->like('ph_pref_state',"$like%"); $nduh++;
                }
                if( $ar['ph_licenses'] ) {
                    $like = addslashes(trim($ar['ph_licenses']));
                    $where->like('ph_licenses',"%$like%"); $nduh++;
                }
                if( $ar['ctct_st_code'] ) { $where->equalTo('ctct_st_code',$ar['ctct_st_code']); $nduh++; }
            }
            if( $ar['ph_pref_region'] >= 0 ) { $where->equalTo('ph_pref_region',$ar['ph_pref_region']); $nduh++; }
            if( $ar['ph_citizen'] ) { $where->equalTo('ph_citizen',$ar['ph_citizen']); $nduh++; }
            if( $ar['ph_recruiter'] ) { $where->equalTo('ph_recruiter',$ar['ph_recruiter']); $nduh++; }
            if( $ar['ph_locums'] ) { $where->equalTo('ph_locums',$ar['ph_locums']); $nduh++; } 
			//if( $ar['has_cv'] ) { $where->equalTo('ph_locums',$ar['has_cv']); $nduh++; }
            if( $ar['ph_status'] >= 0 ) { $where->equalTo('ph_status',$ar['ph_status']); $nduh++; }
			else{ $where->notEqualTo('ph_status',12); $nduh++; }
            if( $ar['date1'] && $ar['date2'] ) { $where->between('ph_cv_date',$ar['date1'],$ar['date2']);$nduh++; }
            elseif( $ar['date1'] ) { $where->greaterThanOrEqualTo('ph_cv_date',$ar['date1']); $nduh++; }
            elseif( $ar['date2'] ) { $where->lessThanOrEqualTo('ph_cv_date',$ar['date2']); $nduh++; }
			if( $ar['has_cv'] ==1) 
			{ 
				$where->literal(' NOT ISNULL(cv_id)');
				//$where->literal('AND NOT ISNULL(ph_cv_url)');
				//$where->literal('NOT ISNULL(ph_cv_url)',array()); 
				$nduh++; 
			}
			
			
            return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
    }

    public function fetchAll($id = 0, array $ar = null) {
        $where = $this->buildQuery($ar);
        if( $where ) {
			if( $ar['has_cv'] ==1) 
				$this->table = "vphlookupsmall2";
            $select = new Select($this->table);
            $select->where($where);
            $select->order('ctct_name');
            $limit = (int) $ar['pg_size']; if( !$limit || $limit > 200 ) $limit = 25;
            $select->limit($limit);
            $id--; if( $id < 0 ) $id = 0;
            $offset = $id * $limit;
            $select->offset($offset);
			if( $ar['has_cv'] ==1) 
				$this->adapter->query("SET SQL_BIG_SELECTS = 1")->execute();
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
			if( $ar['has_cv'] ==1) 
				$this->table = "vphlookupsmall2";
            $select->from($this->table);
            $select->where($where);
            $select->columns(array('*'), false);
            $str = @$sql->getSqlStringForSqlObject($select); //have to turn off error handling to use this
            $str = str_replace('SELECT * ','SELECT count(*) as cnt ',$str); // awful
			if( $ar['has_cv'] ==1) 
				$this->adapter->query("SET SQL_BIG_SELECTS = 1")->execute();
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
            $param = "INSERT IGNORE INTO custlistsus (owneruid,listid,ctype,memberuid) SELECT $uid,$lid,3,ctct_id ";
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
            $ar['url'] = $urls->fromRoute('physician', array('action'=>'view', 'part'=>'go', 'id' => $row->ph_id));
            if( !$row->ctct_addr_c ) $row->ctct_addr_c = ' ';

            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }
	
	//strip from phone #
	public function stripPhoneNumber($phoneNumber) {
		
		$phoneNumber = str_replace(' ','',$phoneNumber);
		$phoneNumber = str_replace('.','',$phoneNumber);
		$phoneNumber = str_replace('(','',$phoneNumber);
		$phoneNumber = str_replace(')','',$phoneNumber);
		$phoneNumber = str_replace('-','',$phoneNumber);
		$phoneNumber = str_replace('+','',$phoneNumber);
		$phoneNumber = str_replace('—','',$phoneNumber);		
		$phoneNumber = str_replace('/','',$phoneNumber);	
		$phoneNumber = str_replace('\\','',$phoneNumber);	
		
		return $phoneNumber;
	}
	
	public function selectPhysician($id = 0, array $ar = null) {    //get dr info
			$userid = $_COOKIE["phguid"];
            $ar = array();
			$ar2 = array();
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
			
			$this->table='vphmain';			
			$select = new Select($this->table);
			$select
			//->from(array('c'=>'vclient'))
			//->where('cli_id = ?',$id);
			->where->equalTo('ph_id',$id);
			$resultSet = $this->selectWith($select);			
	
			if($resultSet)
			{ 
				foreach ($resultSet as $row) {
					$ar['ph_id']=$row->ph_id;
					$ar['ph_ctct_id']=$row->ph_ctct_id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ph_status']=$row->ph_status;
					$ar['st_name']=$row->st_name;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['ph_sex']=$row->ph_sex;
					$ar['ph_DOB']=$row->ph_DOB;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['ctct_phone2']=$this->stripPhoneNumber($row->ctct_phone);
					$ar['ctct_hphone']=$row->ctct_hphone;
					$ar['ctct_hphone2']=$this->stripPhoneNumber($row->ctct_hphone);
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$row->ctct_fax;
					$ar['ctct_hfax']=$row->ctct_hfax;
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_cell']=$row->ctct_cell;
					$ar['ctct_cell2']=$this->stripPhoneNumber($row->ctct_cell);
					$ar['ctct_pager']=$row->ctct_pager;
					$ar['ctct_ext3']=$row->ctct_ext3;
					$ar['ctct_email']=$row->ctct_email;
					$ar['pt_name']=$row->pt_name;
					$ar['ph_prot_date']=$row->ph_prot_date;
					$ar['emp_uname']=$row->emp_uname;
					$ar['ph_lang']=$row->ph_lang;
					$ar['ph_v_skills']=$row->ph_v_skills;
					$ar['ph_citizen']=$row->ph_citizen;
					$ar['ph_med_school']=$row->ph_med_school;
					$ar['ph_pref_state']=$row->ph_pref_state;
					$ar['reg_name']=$row->reg_name;
					$ar['ph_licenses']=$row->ph_licenses;
					$ar['ph_spec_main']=str_replace(' ','',$row->ph_spec_main);
					$ar['sp_name']=$row->sp_name;
					$ar['ph_spm_bc']=$row->ph_spm_bc;
					$ar['ph_spm_year']=$row->ph_spm_year;
					$ar['ph_1st_inq']=$row->ph_1st_inq;
					$ar['ph_avail']=$row->ph_avail;
					$ar['ph_cv_date']=$row->ph_cv_date;
					$ar['ph_phone_date']=$row->ph_phone_date;
					$ar['ph_ctr_date']=$row->ph_ctr_date;
					$ar['ph_start_date']=$row->ph_start_date;
					$ar['ph_cv_url']=$row->ph_cv_url;
					$ar['ph_dea']=$row->ph_dea;
					$ar['ph_dea_exp']=$row->ph_dea_exp;
					$ar['ph_upin']=$row->ph_upin;
					$ar['ph_usmle']=$row->ph_usmle;
					$ar['ph_ref_submit']=$row->ph_ref_submit;
					$ar['ph_ref_client']=$row->ph_ref_client;
					$ar['ph_ama_submit']=$row->ph_ama_submit;
					$ar['ph_assesm']=$row->ph_assesm;
					$ar['ph_completed']=$row->ph_completed;
					$ar['ph_preint_got']=$row->ph_preint_got;
					$ar['ph_sub']=$row->ph_sub;
					$ar['ph_subspec']=$row->ph_subspec;
					$ar['ph_date_mod']=$row->ph_date_mod;
					$ar['ph_user_mod']=$row->ph_user_mod;
					$ar['ph_ref_recr']=$row->ph_ref_recr;
					$ar['ph_ref_hold']=$row->ph_ref_hold;
					$ar['ph_ca_not']=$row->ph_ca_not;
					$ar['ph_ama_client']=$row->ph_ama_client;
					$ar['ph_skill']=$row->ph_skill;
					$ar['sk_name']=$row->sk_name;
					$ar['ph_cv_text']=$row->ph_cv_text;
					
					$arr = $this->getPhysicianCV($id);
					$ar["cv_id"] = $arr["cv_id"];
					$ar["filename"] = $arr["filename"];
					
				}
			}			
			
			//echo var_dump($ar);
			
		return $ar;	
    }
	
	public function getPhysicianCV($id) {                  
						
			$result = $this->adapter->query('SELECT * FROM cvs2 where not isnull(cv_ph_id) AND cv_ph_id=? order by cv_id desc LIMIT 1',
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
	
	public function searchEmails($email) {                  
						
			$result = $this->adapter->query('SELECT * FROM vphmain where ctct_email = ? ',
            array($email));			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar["ph_id"]=$row->ph_id;	
											
				}
			}			
			return $ar;
    }
	
	public function getPhysician($id = 0) {    //get dr info
			$userid = $_COOKIE["phguid"];
            $ar = array();
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
			
			$result = $this->adapter->query('select * from veditph where ph_id = ?',
            array($id));			
	
			if($result)
			{ 
				foreach ($result as $row) {
					$ar['ph_id']=$row->ph_id;
					$ar['ph_old_id']=$row->ph_old_id;
					$ar['ph_ctct_id']=$row->ph_ctct_id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ph_status']=$row->ph_status;
					$ar['st_name']=$row->st_name;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					
					$ar['ctct_waddr_c']=$row->ctct_waddr_c;
					$ar['ctct_waddr_1']=$row->ctct_waddr_1;
					$ar['ctct_waddr_2']=$row->ctct_waddr_2;
					$ar['ctct_wst_code']=$row->ctct_wst_code;
					$ar['ctct_waddr_z']=$row->ctct_waddr_z;
					$ar['ph_sex']=$row->ph_sex;
					$ar['ph_DOB']=$row->ph_DOB;
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
					$ar['ph_recruiter']=$row->ph_recruiter;
					$ar['pt_name']=$row->pt_name;
					$ar['ph_prot_date']=$row->ph_prot_date;
					$ar['emp_uname']=$row->emp_uname;
					$ar['ph_lang']=$row->ph_lang;
					$ar['ph_v_skills']=$row->ph_v_skills;
					$ar['ph_citizen']=$row->ph_citizen;
					$ar['ph_med_school']=$row->ph_med_school;
					$ar['ph_pref_state']=$row->ph_pref_state;
					$ar['reg_name']=$row->reg_name;
					$ar['ph_licenses']=$row->ph_licenses;
					$ar['ph_spec_main']=str_replace(' ','',$row->ph_spec_main);
					$ar['sp_name']=$row->sp_name;
					$ar['ph_spm_bc']=$row->ph_spm_bc;
					$ar['ph_spm_year']=$row->ph_spm_year;
					$ar['ph_1st_inq']=$row->ph_1st_inq;
					if($ar['ph_1st_inq']=="0000-00-00 00:00:00")
						$ar['ph_1st_inq']="";
					$ar['ph_avail']=$row->ph_avail;
					if($ar['ph_avail']=="0000-00-00 00:00:00")
						$ar['ph_avail']="";
					$ar['ph_cv_date']=$row->ph_cv_date; 
					if($ar['ph_cv_date']=="0000-00-00 00:00:00")
						$ar['ph_cv_date']="";
					$ar['ph_phone_date']=$row->ph_phone_date;
					$ar['ph_ctr_date']=$row->ph_ctr_date;
					$ar['ph_start_date']=$row->ph_start_date;
					$ar['ph_cv_url']=$row->ph_cv_url;
					$ar['ph_dea']=$row->ph_dea;
					$ar['ph_dea_exp']=$row->ph_dea_exp;
					$ar['ph_upin']=$row->ph_upin;
					$ar['ph_usmle']=$row->ph_usmle;
					$ar['ph_ref_submit']=$row->ph_ref_submit;
					$ar['ph_ref_client']=$row->ph_ref_client;
					$ar['ph_ama_submit']=$row->ph_ama_submit;
					$ar['ph_assesm']=$row->ph_assesm;
					$ar['ph_completed']=$row->ph_completed;
					$ar['ph_preint_got']=$row->ph_preint_got;
					$ar['ph_sub']=$row->ph_sub;
					$ar['ph_subspec']=$row->ph_subspec;
					$ar['ph_date_mod']=$row->ph_date_mod;
					$ar['ph_user_mod']=$row->ph_user_mod;
					$ar['ph_ref_recr']=$row->ph_ref_recr;
					$ar['ph_ref_hold']=$row->ph_ref_hold;
					$ar['ph_ca_not']=$row->ph_ca_not;
					$ar['ph_practice']=$row->ph_practice;
					$ar['ph_ama_client']=$row->ph_ama_client;
					$ar['ph_skill']=$row->ph_skill;
					$ar['sk_name']=$row->sk_name;
					$ar['ph_cv_text']=$row->ph_cv_text;
					$ar['ph_pref_region']=$row->ph_pref_region;
					$ar['ph_workaddr']=$row->ph_workaddr;
					$ar['ph_nonewsletter']=$row->ph_nonewsletter;
					$ar['ph_nospeclist']=$row->ph_nospeclist;
					$ar['ph_locums']=$row->ph_locums;
					$ar['ctct_bounces']=$row->ctct_bounces;
					//$ar['ph_citizen']=$row->ph_citizen;
					$ar['ctct_company']=$row->ctct_company;
					
				}
				$arr = $this->getPhysicianCV($id);
					$ar["cv_id"] = $arr["cv_id"];
					$ar["filename"] = $arr["filename"];
			}			
			
		return $ar;	
    }

	//new get comments
	public function getPhysicianComments($id = 0, $noq='') {         
            
			$this->table='allnotes';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('note_type',3)
			->where->equalTo('note_ref_id',$id);
			$select->order('note_dt DESC');
			$resultSet = $this->selectWith($select);
			
			$result = $this->adapter->query('select* from allnotes where note_type = 3 and note_ref_id = ?   order by note_dt desc',
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
	
	//new get piplalert
	public function getPiplAlert($id = 0) {       		
			
			$result = $this->adapter->query('select * from vphpiplalert where ph_id = ? order by pipl_date desc',
            array($id));
			$i=0;
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					$ar[$i]['pipl_status']=$row->pipl_status;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['prio']=$row->prio;
					$i+=1;
				}
			}
			
			$result = $this->adapter->query('SELECT * FROM tctrpipl as t left join lstemployees as e on e.emp_id=t.pipl_emp_id where pipl_ph_id=? and pipl_status=11',  array($id));		//get locums stuff	
			
			if($result)
			{			
				foreach ($result as $row) {
					$ar[$i]['pipl_status']=$row->pipl_status;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['prio']=$row->pipl_status;
					$i+=1;
				}
			}
			//echo var_dump($ar);
			return $ar;
    }
	
	//new source types
	public function getSources() {        
            		
			$ar = array();	
			$ar2 = array();	
			$result = $this->adapter->query("select src_id, src_name, src_price, src_pricing, src_rating, src_sp_code, src_type from tsources where src_type<>0 order by src_type, src_name")->execute();
			$i=0;
			$type="";
			$type2="";			
			foreach ($result as $row) 
			{
				//$type=$row['src_type'];
				if($type=="")
						$type=$row['src_type'];
				if($type!=$row['src_type']&&$type!="")
				{		
					//if($type=="")
						//$type=$row['src_type'];
					
					$i=0;
					$ar[$type]=$ar2;
					$ar2 = array();
					$type=$row['src_type'];
				}
				$ar2[$i]['src_id']=$row['src_id'];
				$ar2[$i]['src_name']=$row['src_name'];
				$ar2[$i]['src_price']=$row['src_price'];
				$ar2[$i]['src_pricing']=$row['src_pricing'];
				$ar2[$i]['src_rating']=$row['src_rating'];
				$ar2[$i]['src_sp_code']=$row['src_sp_code'];
				$ar2[$i]['src_type']=$row['src_type'];
				
				/*$ar[$i]['srt_id']=$row->srt_id;
				$ar[$i]['srt_name']=$row->srt_name;
				$ar[$i]['srt_order']=$row->srt_order;*/
				$i+=1;			
			}	
			$ar[$type]=$ar2;
			
			return $ar;
    }
	
	//drop down for popup
	public function getSources2($id=0) {    		
			
			$result = $this->adapter->query('select src_id, src_name from tsources where src_type <> 0 order by src_name',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['src_id']=$row->src_id;
					$ar[$i]['src_name']=$row->src_name;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getActivities($id=0, $type=0) {    	//type 0=dr, 1=mid, 2=client	
			
			if($type==1)
				$result = $this->adapter->query('select act_code,act_name,act_need_note from dctactivity where act_need_ref=1 and act_nurse=1 and act_hidden=0',array($id));
			if($type==2)
				$result = $this->adapter->query('select act_code,act_name,act_need_note from dctactivity where act_need_ref=1 and act_client=1 and act_hidden=0',array($id));			
			else
				$result = $this->adapter->query('select act_code,act_name,act_need_note from dctactivity where act_need_ref=1 and act_phys=1 and act_hidden=0',array($id));
		
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
	
	public function getPhyStatus($id = 0) {         
            
			$this->table='dctstatus';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('st_physician',1);			
			
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			
			$i=0;
				foreach ($resultSet as $row) {
					
					$ar[$i]['st_id']=$row->st_id;
					$ar[$i]['st_name']=$row->st_name;									
					$i+=1;
				}
			}			
			return $ar;
    }
	
	public function getPracticeTypes() {         
            
			$result = $this->adapter->query("select * from dctpracticetypes order by pt_id")->execute();
			
			$ar = array();
			if($result)
			{			
			$i=0;
				foreach ($result as $row) {
					
					$ar[$i]['pt_id']=$row["pt_id"];
					$ar[$i]['pt_name']=$row["pt_name"];									
					$i+=1;
				}
			}			
			return $ar;
    }
	
	//new get specialties
	public function getSpecialtyOptions() {         
            			
			$ar = array();
		
			$result = $this->adapter->query("select * from vspecial order by sp_code,skill")->execute();
			if($result)
			{
			$i=0;
			foreach ($result as $row) 
			{
				$ar[$i]['sp_code']=$row["sp_code"];	
				$ar[$i]['skill']=$row["skill"];	
				$ar[$i]['spec']=$row["spec"];	
				$ar[$i]['sp_name']=$row["sp_name"];	
				$i+=1;
			}
			}			
			
			return $ar;
    }
	
	public function getRegions() {         
            			
			$ar = array();
		
			$result = $this->adapter->query("select * from dctregions order by reg_id")->execute();
			if($result)
			{
			$i=0;
			foreach ($result as $row) 
			{
				$ar[$i]['reg_id']=$row["reg_id"];	
				$ar[$i]['reg_name']=$row["reg_name"];																		
				$i+=1;
			}
			}			
			
			return $ar;
    }
	
	public function editPhysician($post, $id) {	
		$id  = (int) $id;       
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
			$note_user=$username;
		
            $phar = array();
			$ctar = array();
			$wctar = array();
		
			/*$this->table='lstemployees';
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
			}*/
			//trim ph_spec_main
			$ph_spec_main=$post["ph_spec_main"]; //save
			$post["ph_spec_main"] = substr($post["ph_spec_main"] , 0, 3);
			$post["ph_spec_main"]=str_replace('&nbsp;','',trim($post["ph_spec_main"]));
			$post["ph_spec_main"] = str_replace(' ','',$post["ph_spec_main"]);
			//filter phone
			$post["ctct_phone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_phone"])));
			$post["ctct_hphone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_hphone"])));
			$post["ctct_hfax"] = str_replace('-','',$post["ctct_hfax"]);
			$post["ctct_cell"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_cell"])));
			$post["ctct_fax"] = str_replace('-','',$post["ctct_fax"]);
			$post["ctct_pager"] = str_replace('-','',$post["ctct_pager"]);
			
			if($post["ph_1st_inq"]=='')
				$post["ph_1st_inq"] = date('Y-m-d');
			if($post["ph_avail"]=='')
				$post["ph_avail"] = date('Y-m-d');
			
			foreach($post as $key=>$val)
			{
				if($key!="ph_id" && $key!="ph_cv_datetxt" && $key!="ph_availtxt" && $key!="ph_1st_inqtxt" && $key!="submitedit" && $key!="ph_xchg" && $key!="srs" && $key!="ph_workaddr"){
					//$updatestr.=$key."='".$val."',";
					//$updatestr.=$key."=?,";
					if(strpos($key,"ph_")!==false)
					{					
						$phar[]=$val;
						$ph_updatestr.=$key."=?,";
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
			$wct_updatestr = " ctct_addr_1= '".$post["ctct_waddr_1"]."', ctct_addr_2= '".$post["ctct_waddr_2"]."', ctct_addr_c= '".$post["ctct_waddr_c"]."', ctct_st_code= '".$post["wctct_st_code"]."', ctct_addr_z= '".$post["ctct_waddr_z"]."' , ctct_company= '".$post["ctct_company"]."' ";
			$phar[]=$note_user;
			$ctar[]=$note_user;
			$phar[]=$id;
			$ctar[]=$id;
			$ph_updatestr .= "ph_user_mod = ?, ph_date_mod = now()";
			$ct_updatestr .= "ctct_user_mod = ?, ctct_date_mod = now()";
			 //$ph_updatestr .= var_dump($phar);
			//$ct_updatestr .= var_dump($ctar);
		$result = $this->adapter->query('update lstphysicians set '.$ph_updatestr.' where ph_id= ? LIMIT 1', $phar);	
		$result = $this->adapter->query('update lstcontacts set '.$ct_updatestr.' where ctct_id= (select ph_ctct_id from lstphysicians where ph_id = ? ) and ctct_id <> 9 LIMIT 1', $ctar);
		
		if(($post["ph_workaddr"]=="" || $post["ph_workaddr"]=="0") && ($post["ctct_waddr_1"]!="" || $post["ctct_waddr_2"]!="" || $post["ctct_waddr_c"]!="" || $post["ctct_waddr_z"]!="" || $post["wctct_st_code"]!="") )
		{
		
			$result = $this->adapter->query('call AddAContactNoId(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, NOW(), ? )', 
			array($post["ctct_name"], $post["ctct_title"], $post["ctct_company"], $post["ctct_phone"], $post["ctct_ext1"], $post["ctct_fax"], 
			$post["ctct_ext2"], $post["ctct_cell"], $post["ctct_pager"], $post["ctct_ext3"], $post["ctct_hphone"], $post["ctct_hfax"], 
			$post["ctct_email"], $post["ctct_waddr_1"], $post["ctct_waddr_2"], $post["ctct_waddr_c"], $post["ctct_waddr_z"], $post["wctct_st_code"],
			'0', '0', '0', $note_user, $id));
			foreach ($result as $row) {
					$wctct_id=$row->id;										
			}
			if($wctct_id!="" && $wctct_id>0)
				$result = $this->adapter->query('update lstphysicians set ph_workaddr=?, ph_user_mod= ?, ph_date_mod=NOW() WHERE ph_id=? LIMIT 1', array($wctct_id, $note_user, $id));
		}
		else{
			$result = $this->adapter->query('update lstcontacts set '.$wct_updatestr.' where ctct_id= (select ph_workaddr from lstphysicians where ph_id = ? ) and ctct_id <> 9 LIMIT 1', array($id));
			if($post["ctct_company"]!='')
				$result = $this->adapter->query('update lstcontacts set ctct_company= "'.$post["ctct_company"].'" where ctct_id= (select ctct_id from lstphysicians where ph_id = ? ) and ctct_id <> 9 LIMIT 1', array($id));
		
		//echo $wct_updatestr;
		//echo $id;
		//exit();
		}
		//$result = $this->adapter->query('update lstPhysicians set xxxx, ph_user_mod = ?, ph_date_mod = now() where ph_id= ? LIMIT 1',
            //array($note_user, $id));	
        // prepare sqlupdate lstPhysicians set $fphs,ph_user_mod=$usr,ph_date_mod=\'$ctme\' where ph_id = $id
      
		
        return true; //$ct_updatestr; 
    }
	
	public function sendCV($post, $id) {	
		if($post["nurse"]==0)
			$dr= " Dr.";
		$cname=$post["phname"];
		$phemail = $post["phemail"];
		$from = "speclistret@phg.com";	
		
		$recid=$post["recid"];
		
		$result = $this->adapter->query("select ctct_name, ctct_email, ctct_title, ctct_phone from lstemployees join lstcontacts on emp_ctct_id = ctct_id where emp_id = '".$recid."' LIMIT 1")->execute();
				
		if($result)
		{				
			foreach ($result as $row){
				$recname = $row["ctct_name"];
				$recemail = $row["ctct_email"];				
				$rectt = $row["ctct_title"];
				$recph = $row["ctct_phone"];
			}
		}
		
		if($recemail!="")
			$from = $recemail;
			$to = $phemail; //"nturner@phg.com"; //CHANGE/remove LATER***** //$phemail;
			
			$subject = "CV Request from Pinnacle Health Group";
			$headers = "From: ".$from."\r\n";
			$headers .= "BCC: nturner@phg.com\r\n";
			$headers .=  'X-Mailer: PHP/'.phpversion()."\r\n";
			$message = "Dear".$dr." ".$cname.":\n\n";
			$message .= "\n\n";
						
			
			$message .= "We recently received your information and know you may be currently looking for a new position. PhysicianCareer.com is one of the largest and most trusted job boards on the market. We offer a wide variety of opportunities across the country in all specialties. We ask that you send us your CV and a description of your job preferences. Your CV helps us match you to the right opportunity in a great community.\n";
			$message .= "Thank you for your time in working with us and we will look forward to receiving further information so we can start working for you! If you have any questions, please call us at 800-789-6684. \n\n";
			$message .= "Sincerely,\n";
			$message .= "".$recname." \n";
			$message .= "".$rectt." \n\n";
			$message .= "PhysicianCareer.com \n";
			$message .= "Phone: ".$recph." \n";
			$message .= "Email: tbroxtermancv@phg.com";
			
		
		mail ($to, $subject, $message, $headers);
		//header("location: /contract/view/".$ctr_id."\n\n");
	}
	
	public function searchPhysicians($post) {      //return array for ajax   
            $sql="";
			$ar = array();
			$fname=$post["fname"];
			$lname=$post["lname"];
			$path =$post["path"];
			$table = "<table class='instasearchtbl'><tr><th>ID#</th><th>Name</th><th>Spec</th><th>City, State</th></tr>";
			
			if(strlen($lname)>=2)
			{
				$sql .= " ctct_name LIKE '%".$lname."%' ";
			}
			if(strlen($fname)>=2)
			{
				if(strlen($lname)>=2)
					$sql .= " AND ";
				$sql .= " ctct_name LIKE '%".$fname."%' ";
			}
			$q = "select * from vPhForAdd WHERE ".$sql." LIMIT 10";
		
			$result = $this->adapter->query("select * from vphforadd WHERE ".$sql." LIMIT 10")->execute();
			if($result)
			{
			$i=0;
			foreach ($result as $row) 
			{
				$table .= "<tr><td><a href='/public/physician/edit/".$row["ph_id"]."'>".$row["ph_id"]."</a></td><td>".$row["ctct_name"]."</td><td>".$row["sp_name"]."</td><td>".$row["ctct_addr_c"].", ".$row["ctct_st_code"]."</td></tr>";																		
				$i+=1;
			}			}	
			$table .= "</table>";
			if($i==0)
				$table=""; //return if no results
			return $table;
    }
	
	public function addPhysician($post) {	
		$id  = (int) $id;      
		
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
			$note_user=$username;
			
		$skill = "NULL";
		$name = $post["ph_fname"]." ".$post["ph_lname"]; //name...?
		$arr = preg_split("/\|/",$post["ph_spec_main"]);
		$spec = $arr[0];//substr($post["ph_spec_main"], 0, 3);
		//$skill = trim(str_replace($spec,"",$post["ph_spec_main"]));
		$skill = $arr[1];
		//$skill = str_replace(urldecode("&nbsp;"),"",$skill);
		//$skill = str_replace(" ","",trim(substr($post["ph_spec_main"], -2)));
		//$skill = str_replace(" ","x",$post["ph_spec_main"]);
		//preg_match("/^[A-Z]{1,2}/",substr($post["ph_spec_main"], -2),$match);
		//$skill = $match[1];
		$sub=0;
		if($post["ph_subspec"]!="")
			$sub=1;
		$cvtext = $post["ph_cv_text"];
		$cvtext = str_replace("<","&lt;",$cvtext);
		$cvtext = str_replace(">","&gt;",$cvtext);
		
		if($post["ph_1st_inq"]=='')
				$post["ph_1st_inq"] = date('Y-m-d');
		
		//filter phone
			$post["ctct_phone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_phone"])));
			$post["ctct_hphone"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_hphone"])));
			$post["ctct_hfax"] = str_replace('-','',$post["ctct_hfax"]);
			$post["ctct_cell"] = str_replace('-','',str_replace('(','',str_replace(')','',$post["ctct_cell"])));
			$post["ctct_fax"] = str_replace('-','',$post["ctct_fax"]);
			$post["ctct_pager"] = str_replace('-','',$post["ctct_pager"]);
		
		if($name!="")
		{
			$result = $this->adapter->query('call AddAPhysician(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)', 
			array($name, $post["ctct_title"], $post["ctct_company"], $post["ctct_phone"], $post["ctct_ext1"], $post["ctct_fax"], $post["ctct_ext2"],
			$post["ctct_cell"], $post["ctct_pager"], $post["ctct_ext3"], $post["ctct_hphone"], $post["ctct_hfax"], $post["ctct_email"], 
			$post["ctct_addr_1"], $post["ctct_addr_2"], $post["ctct_addr_c"], $post["ctct_addr_z"], $post["ctct_st_code"],
			$post["ctct_waddr_1"], $post["ctct_waddr_2"], $post["ctct_waddr_c"], $post["ctct_waddr_z"], $post["wctct_st_code"],
			"", //wurl - what is that? doesn't seem to be used
			$spec, $post["ph_spm_bc"], $post["ph_spm_year"], $post["ph_med_school"], $post["ph_status"], $post["ph_cv_url"], 
			$post["ph_cv_date"], $post["ph_recruiter"], $post["ph_practice"], $post["ph_DOB"], $post["ph_sex"], $post["ph_locums"], 
			$post["ph_citizen"], $post["ph_lang"], $post["ph_v_skills"], $post["ph_licenses"], $post["ph_1st_inq"], $post["ph_avail"], 
			$post["ph_pref_state"], $post["ph_pref_region"], $note_user, //'NOW()',
			$sub, $post["ph_subspec"], $skill, $cvtext
			));
			foreach ($result as $row) {
					$id=$row->id;										
			}
			
			/*if(($post["ph_workaddr"]=="" || $post["ph_workaddr"]=="0") && ($post["ctct_waddr_1"]!="" || $post["ctct_waddr_2"]!="" || $post["ctct_waddr_c"]!="" || $post["ctct_waddr_z"]!="" || $post["wctct_st_code"]!="") )
			{
			//echo $wct_updatestr;
			$result = $this->adapter->query('call AddAContactNoId(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, NOW(), ? )', 
			array($post["ctct_name"], $post["ctct_title"], $post["ctct_company"], $post["ctct_phone"], $post["ctct_ext1"], $post["ctct_fax"], 
			$post["ctct_ext2"], $post["ctct_cell"], $post["ctct_pager"], $post["ctct_ext3"], $post["ctct_hphone"], $post["ctct_hfax"], 
			$post["ctct_email"], $post["ctct_waddr_1"], $post["ctct_waddr_2"], $post["ctct_waddr_c"], $post["ctct_waddr_z"], $post["wctct_st_code"],
			'0', '0', '0', $note_user, $id));
			foreach ($result as $row) {
					$wctct_id=$row->id;										
			}
				if($wctct_id!="" && $wctct_id>0)
					$result = $this->adapter->query('update lstphysicians set ph_workaddr=?, ph_user_mod= ?, ph_date_mod=NOW() WHERE ph_id=? LIMIT 1', array($wctct_id, $note_user, $id));
			}*/
		}	
		return $id; //$post["ph_spec_main"];
	}
	
	public function getPlacementInfo($id = 0, $ph_id) {           
			
			
			$result = $this->adapter->query('select * from vplacement where pipl_ctr_id = ? and pipl_ph_id = ? order by pl_date desc, pipl_cancel ',
            array($id, $ph_id));
			
			$ar = array();
			if($result)
			{
			
				foreach ($result as $row) {
					$ar['ctr_id=']=$ctr_id;
					$ar['ph_id']=$ph_id;
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
					$ar['pipl_ph_id']=$row->pipl_ph_id;	
					$ar['pl_text1']=$row->pl_text1;
					$ar['pl_text2']=$row->pl_text2;
					$ar['pl_text3']=$row->pl_text3;	
					$ar['pl_text4']=$row->pl_text4;					
					
				}
			}
			
			return $ar;
    }
	
	public function getHistory($id = 0) {           
			
			
			$result = $this->adapter->query('SELECT * FROM vphpipl where pipl_ph_id = ? order by pipl_date',
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
					$ar[$i]['pipl_ph_id']=$row->pipl_ph_id;
					$ar[$i]['pipl_nurse']=$row->pipl_nurse;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function getPasses($id = 0) {           
			
			
			$result = $this->adapter->query('SELECT * FROM vphpasseslist where pp_ph_id = ? order by pp_date desc',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['pp_date']=$row->pp_date;
					$ar[$i]['rec_from']=$row->rec_from;
					$ar[$i]['rec_to']=$row->rec_to;
					
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
			if(trim($post["act_ret"])=="" || trim($post["act_ret"])==null)
				$post["act_ret"]=0;
			
			if($post["schedact_date"]=="")
				$post["schedact_date"]=date('Y-m-d');
				
			if($post["schedact_hrtxt"]=="")
				$post["schedact_hrtxt"]="12";
			if($post["schedact_mintxt"]=="")
				$post["schedact_mintxt"]="00";
			
			$post["sched_act_notes"].="\r\n".$post["ph_name"];
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
				$post["act_ret"], //ref_type - default 0
				$post["sched_act_notes"], //notes, 
				$post["act_ct"]
				               
			));			
			
			if($post["activ_type"]!=8)//add link to dr			
				$post["sched_act_notes"].="\r\nhttp://testdb.phg.com/public/physician/view/".$post["act_ref"]."\r\n";
			
			$hour = sprintf('%02d', ltrim($post["schedact_hrtxt"],'0')+5); //fix 4 hours behind...
			$startdate = str_replace("-","",$post["schedact_date"])."T".$hour.$post["schedact_mintxt"]."00Z";
			//echo $startdate;
			//echo "M**".$post["schedact_mintxt"];
			
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
//$headers .= "Cc:nturner@phg.com\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/calendar; method=REQUEST;\n";
$headers .= '        charset="UTF-8"';
$headers .= "\n";
$headers .= "Content-Transfer-Encoding: 7bit";

/*mail content , attaching the ics detail in the mail as content*/
$subject = "[DB] - Scheduling Notification from ".$realname;
$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

//echo $subject;
//echo $to_email;
//exit();
/*mail send*/
if($post["activ_type"]!=2) //not present
	mail($to_email, $subject, $message, $headers);
				
			return true;
    }
	
	//pass to recruiter
	public function passTo($post, $identity) {             		
		$userid = $_COOKIE["phguid"];		
		$realname = $_COOKIE["realname"];			
		
		foreach ($post["to"] as $key=>$val)
		{
		
			$to_id = $val;
			if($to_id>0 && $userid!='' && $post["ph_id"]!='')
			{
			$result = $this->adapter->query('insert into tphpasses (pp_ph_id,pp_emp_from,pp_date,pp_src_id,pp_emp_to) values (?,?,NOW(),?,?)', 
			array(
			$post["ph_id"],
			$userid,
			//$post[""],
			$post["pass_source"],
			$to_id
			));	
			
			$post["activ_type"]="8";
			$post["sched_act_notes"]="Physician Pass from ".$realname." \r\n http://testdb.phg.com/public/physician/view/".$post["ph_id"]." \r\n";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$to_id;//$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ct"]=$post["ph_ct"];
			$post["act_tx"]=$post["ph_tx"];	
		
			$this->addActivity($post, $identity);
			}
			else { return false; }
		}
		
		
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
			$cname = $post["ph_name"];
			$cid = $post["ph_id"]; //change to ph_id for dr
			//$post["cliname"];				
			$recid = $post["recid"];
			$recname = $post["recname"];	
			$recfx = $post["recfax"];	
			$recph = $post["recphone"];	
			$recem = $post["recemail"];	
			$rectt = $post["rectitle"];	
			$retref = ''; //not sure what this is for...blank in old system
			$cno = $post["ctrno"];		
			if($post["pres_date"]!="")
				$idate = $post["pres_date"];
			elseif($post["int_date"]!="")
				$idate = $post["int_date"];
			//$post["act_user"]; //to
			$realuser = urldecode($_COOKIE["realname"]);
			$bnu = $post["nurse"];
			$dr = 'Dr. '; //not a dr.
			//$ext = 'ext'; //for a nurse...part of the link below
			$post["schedact_date"]=$idate;
			
			//echo "HERE".$idate;
			
		$rr00 = 'rrector@phg.com';
		$rr01 = 'jpolver@phg.com';
		//$to = "preinterview@phg.com";
		$cemail = 'jpolver@phg.com'; //not really used for pre-int
		
		if($post["addon"]=='')
			$to = $useremail; //"nturner@phg.com"; //FOR NOW, CHANGE LATER to 
		else
			$to = $post["addon"];
		
		
		/*$hascv='';
		if($addon=='YES')
		{
			$hascv = 'has a CV'; 
			$cemail = $rr01;
		}
		if($addon=='NO'||$addon=='')
		{
			$hascv = 'does not have CV'; 
			$cemail = $rr00;
		}*/
		if($useremail!='')
			$from=$useremail;
		else
			$from="speclistret@phg.com";
		
			$headers = "From: ".$from." \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail.",".$cemail.",jcouvillon@phg.com\n";
			$headers .= "BCC: nturner@phg.com\r\n";
			
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

http://www.phg.com/pre_interview$ext.php?a=$cid&b=$recid&c=$cno\n 

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
			
			//send reminder email
			$hour = sprintf('%02d', ltrim('09','0')+5); //fix 4 hours behind... //9am
			$startdate = str_replace("-","",date('Y-m-d',strtotime($idate . ' -5 day')))."T".$hour."00"."00Z";
			//echo $startdate;
			//echo "M**".$post["schedact_mintxt"];
			
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
DESCRIPTION: ".$post["sched_act_notes"]." for $dr $cname on $idate
LOCATION: US
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY: Upcoming interview
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR";

/*Setting the header part, this is important */
$headers = "From:info@phg.com\n";
//$headers .= "Cc:nturner@phg.com\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/calendar; method=REQUEST;\n";
$headers .= '        charset="UTF-8"';
$headers .= "\n";
$headers .= "Content-Transfer-Encoding: 7bit";

/*mail content , attaching the ics detail in the mail as content*/
$subject = "[DB] - Scheduling Notification from ".$realname;
$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
$to_email = 'nturner@phg.com';

	mail($to_email, $subject, $message, $headers);
	
	} //end send pre interview
	
	//send pre interview email, etc.
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
			$cname = $post["ph_name"];
			$cid = $post["ph_id"]; //change to ph_id for dr
			//$post["cliname"];				
			$recid = $post["recid"];
			$recname = $post["recname"];	
			$recfx = $post["recfax"];	
			$recph = $post["recphone"];	
			$recem = $post["recemail"];	
			$rectt = $post["rectitle"];	
			$retref = ''; //not sure what this is for...blank in old system
			$cno = $post["ctrno"];		
			if($post["pres_date"]!="")
				$idate = $post["pres_date"];
			elseif($post["int_date"]!="")
				$idate = $post["int_date"];
			//$post["act_user"]; //to
			$realuser = urldecode($_COOKIE["realname"]);
			$bnu = $post["nurse"];
			$dr = 'Dr. '; //not a dr.
			//$ext = 'ext'; //for a nurse...part of the link below
			$post["schedact_date"]=$idate;
			
			//echo "HERE".$idate;
			
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
		if($addon=='NO' ||$addon=='')
		{
			$hascv = 'does not have CV'; 
			$cemail = $rr00;
		}
		
		
			$headers = "From: speclistret@phg.com \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail.",".$cemail.",jcouvillon@phg.com\n";
			//$headers .= "BCC: nturner@phg.com\r\n";
			
			//$subject = "Pre-Interview Form";
			//$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			
		
		
			$subject = "[DB] New Present Notification";
			
			$message = "New present has been made in the database. Recruiter $hascv.\n
			Client: $cliname
			Date: $idate
			Contract: $cno
			Ph.ID#: $cid
			Name: $cname

			Recruiter $recname $hascv
			Please email $recem for details.";
		
		

			//echo $subject;
			/*mail send*/
			mail($to, $subject, $message, $headers); //add john to this
	}
	
	
	//createPresent
	public function createPresent($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
								
			
		if($post["ph_id"]!='' && $post["pres_status"]!=7 && $post["pres_ctr_id"]!='') //7 is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["pres_ctr_id"],
			$userid,
			$post["ph_id"],
			$post["pres_status"],
			$post["pres_date"],
			0
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
			$post["sched_act_notes"]="Physician Present from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=0;
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
		
			$this->addActivity($post, $identity);
			
			
			$this->sendPresent($post); 
		}
			else { return false; }		
		
		
		return true;
	}
	
	
	//createPending
	public function createPending($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
								
			
		if($post["ph_id"]!='' && $post["pend_status"]!=7 && $post["ctr_id"]!='') // is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["ctr_id"],
			$userid,
			$post["ph_id"],
			$post["pend_status"],
			$post["pend_date"],
			0
			));	
			
		}
			else { return false; }		
		
		
		return true;
	}
	
	//update HotList table
	public function updateHotList($post, $identity) {             		
		$userid = $_COOKIE["phguid"];								
			
		$post["pending"] =0; //pending not used now?
		if($post["ph_id"]!='' && $userid!='' && $post["hot_req"]!='') 
		{
			$result = $this->adapter->query('select COUNT(*) AS cnt from tphhotlist where phh_ph_id = ? AND phh_emp_id=?', array($post["ph_id"],$post["hot_req"]));
			
			foreach ($result as $row) 
			{							
				$cnt=$row->cnt;			
							
			}
			
		$post["ph_hotdoc"]= $post["ph_hotdoc"]==1 ? 1 : 0;
		$post["ph_pending"]= $post["ph_pending"]==1 ? 1 : 0;
		$post["ph_lead_1"]= $post["ph_lead_1"]==1 ? 1 : 0;
		$post["ph_lead_2"]= $post["ph_lead_2"]==1 ? 1 : 0;
			
			
			if($cnt>0)
			{	//echo "test";
				$result = $this->adapter->query('update tphhotlist set phh_ph_id=?, phh_emp_id=?, phh_hot_doc=?, phh_pending=?, phh_lead_1=?, phh_lead_2=?, phh_date_mod= NOW() WHERE phh_ph_id = ? AND phh_emp_id=? LIMIT 1', 
				array(			
				$post["ph_id"],
				$post["hot_req"],
				$post["ph_hotdoc"],
				$post["pending"], ///wut
				$post["ph_lead_1"],
				$post["ph_lead_2"],
				$post["ph_id"],
				$post["hot_req"]
				));
			}
			else{
		
				$result = $this->adapter->query('insert into tphhotlist (phh_ph_id, phh_emp_id, phh_hot_doc, phh_pending, phh_lead_1, phh_lead_2, phh_date_mod)  values (?,?,?,?,?,?, NOW())', 
				array(			
				$post["ph_id"],
				$post["hot_req"],
				$post["ph_hotdoc"],
				$post["pending"], ///wut
				$post["ph_lead_1"],
				$post["ph_lead_2"]
				));	
			}
			
		}
		else { return false; }				
		
		return true;
	}
	
	public function updateLocumsDoc($post, $identity) {             		
		$userid = $_COOKIE["phguid"];				
		$phid = $post["ph_id"];
		if($post["locums_doc"]==1)
		{
			//echo "here";			
			$result = $this->adapter->query('insert into tctrpipl (pipl_emp_id, pipl_ph_id, pipl_status, pipl_date)  values (?,?,?,NOW())', 
				array(		
				$userid,
				$phid,
				11
				));	
		}
		elseif($phid!='' && $phid!=0){
			$result = $this->adapter->query('delete from tctrpipl where pipl_ph_id=? and pipl_status=11 limit 1', 
				array(					
				$phid			
				));	
		}
		
		return true;
	}
	
	//createInterview
	public function createInterview($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		
				
			
		if($post["ph_id"]!='' && $post["int_status"]!=7 && $post["int_ctr_id"]!='') //7 is a fake from somewhere?
		{
			if(!isset($post["resend_int_submit_btn"])) //if not resending
			{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["int_ctr_id"],
			$userid,
			$post["ph_id"],
			$post["int_status"],
			$post["int_date"],
			0 //is nurse
			));	
			}
			
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
			$post["sched_act_notes"]="Physician Interview from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=0;
			//$post["addon"]; //blank for this one
					
			
			$this->sendPreInterview($post); 
			if(!isset($post["resend_int_submit_btn"])) //if not resending
				$this->addActivity($post, $identity);
		}
			else { return false; }		
		
		
		return true;
	}
	
	
	//create Locum Tenens Present
	public function createLocumsPresent2($post, $identity) {             		
		$userid = $_COOKIE["phguid"];
		
		
			
		if($post["ph_id"]!='' && $post["pres_status"]!=7 && $post["loc_pres_ctr_id"]!='' && $post["loc_pres_date"]!='') //7 is a fake from somewhere?
		{
			$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["loc_pres_ctr_id"],
			$userid,
			$post["ph_id"],
			$post["pres_status"],
			$post["loc_pres_date"],
			0 //not nurse
			));	
			
			$result = $this->adapter->query('select * from vphquestemail where ctr_id = ?', array($post["loc_pres_ctr_id"]));
			
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
			$post["sched_act_notes"]="Physician Locums Present from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=0;
			//$post["addon"];			
		
			//$this->addActivity($post, $identity);
			
			$this->sendPresent($post); 
		}
		else { return false; }			
		
		return true;
	}
	
	//create Locum Tenens Present
	public function createLocumsPresent($post, $identity) {             		
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
			
		if($post["ph_id"]!='' && $post["pres_status"]!=7 && $post["loc_pres_date"]!='') //7 is a fake from somewhere?
		{
			/*$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date, pipl_nurse)  values (?,?,?,?,?,?)', 
			array(			
			$post["loc_pres_ctr_id"],
			$userid,
			$post["ph_id"],
			$post["pres_status"],
			$post["loc_pres_date"],
			0 //not nurse
			));*/
			
			$result = $this->adapter->query('insert into ltpresents (ph_id, location, work_site, present_date, spec, user_mod, user_mod_id, ctr_id)  values (?,?,?,?,?,?,?,?)', 
			array(			
			$post["ph_id"],
			$post["loc_location"],
			$post["loc_work_site"],
			$post["loc_pres_date"],
			$post["loc_spec"],
			$username,			
			$userid,
			$post["loc_pres_ctr_id"]
			));	
			
			/*$result = $this->adapter->query('select * from vphquestemail where ctr_id = ?', array($post["loc_pres_ctr_id"]));
			
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
			$post["sched_act_notes"]="Physician Locums Present from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ct"]=$post["an_ct"];
			$post["act_tx"]=$post["an_tx"];
			$post["nurse"]=0;*/
			//$post["addon"];			
		
			//$this->addActivity($post, $identity);
			
			//$this->sendPresent($post); 
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
		
		$result = $this->adapter->query('insert into allnotes (note_user, note_ref_id, note_type, note_emp_id, note_text, note_reserved, note_update, note_dt)  values (?,?,?,?,?,?,?, NOW())', 
			array(			
				$username,
				$post["ph_id"],
				3,
				$userid,
				$note,
				NULL,
				0
			));
			
		$result = $this->adapter->query('insert into tvistapasses (vp_type, vp_ref_id, vp_emp_id)  values (?,?,?)', 
			array(			
			3,			
			$post["ph_id"],
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
REF_ID#".$post["ph_id"]."";
		
		mail($to, $subject, $message, $headers);
		
		
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
		
		$phid = $post["ph_id"]; //ph_id / ph_id
		$plid = $post["pl_id"]; //placement id  / pipl ID
		
		//echo var_dump($post);
		
		$phnm = $post["ph_nm"];
		$ctrno = $post["ctr_no"];
		$ctrspec = $post["ctr_spec"];
		$ctrreq = $post["ctr_req"];
		$ctrlocc = $post["ctr_locc"];
		$ctrlocs = $post["ctr_locs"];
		
		/*$post["pl_guar_net"] = $post["pl_guar_net"] == null ? 0 : 1;
		$post["pl_guar_gross"] = $post["pl_guar_gross"] == null ? 0 : 1;
		$post["pl_annual"] = $post["pl_annual"] == null ? 0 : 1;
		$post["pl_guar"] = $post["pl_guar"] == null ? 0 : 1;
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
		$post["pl_oth_ben"] = $post["pl_oth_ben"] == null ? 0 : 1;*/
		
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
$post["ph_id"], //null, //ph_id
null //$post["ph_id"] 			
));	

if($result)
{			
	foreach ($result as $row) 
	{
		$plid=$row["pl_id"];				
	}
}
			

			
			$to = "placements@phg.com, phgmarketing@phg.com, allusers@phg.com"; //FOR NOW, CHANGE LATER to 	//$useremail; 
		
			$headers = "From: ".$useremail." \n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail."\n";				
			$headers .= "BCC: nturner@phg.com\r\n";
			$subject = "Subject: DB Placement Notification from ".$username;
			
			$message = "New placement has been entered into the Database\nPlacement Info:\n";
			$message .= "Contract: $ctrno ($ctrspec) $ctrlocc, $ctrlocs\nCandidate: $phnm\n\n";
			$message .= "Click on the link below to get the Placement Report:\n\n";
			$message .= "http://testdb.phg.com/public/report/placement2/$plid\n";
		}
			//echo $subject;
			/*mail send*/
			mail($to, $subject, $message, $headers); //add john to this
			
			//$this->addActivity($post, $identity);
			
			return true;
	}
	
	
	public function handleCtrChange($post, $identity) {             		
				$userid = $_COOKIE["phguid"];
			$username = $_COOKIE["realname"];
				
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
			$result = $this->adapter->query('update lstphysicians set ph_ref_submit=?, ph_ref_client=?, ph_ref_recr=?, ph_ref_hold=?,ph_user_mod=?,ph_date_mod=NOW() where ph_id = ?', 
			array(			
			$post["cdate_date"],			
			$post["client_tmp"],
			$post["client2_tmp"],
			$post["client4_tmp"],			
			$username,
			$post["ph_id"]
			));
		}
		if(isset($_POST["ama_date_submit_btn"])){			
			$client_ama = $post["client3_tmp"];
			$result = $this->adapter->query('update lstphysicians set ph_ama_submit=?,ph_ama_client=?,ph_user_mod=?,ph_date_mod=NOW() where ph_id = ?', 
			array(			
			$post["cdate_date"],
			$client_ama,
			$username,
			$post["ph_id"]
			));
		}
		if(isset($_POST["preint_date_submit_btn"])){
			$result = $this->adapter->query('update lstphysicians set ph_preint_got=?,ph_user_mod=?,ph_date_mod=NOW() where ph_id = ?', 
			array(			
			$post["cdate_date"],			
			$username,
			$post["ph_id"]
			));
		}
		if(isset($_POST["packet_date_submit_btn"])){
			$result = $this->adapter->query('update lstphysicians set ph_completed=?,ph_user_mod=?,ph_date_mod=NOW() where ph_id = ?', 
			array(			
			$post["cdate_date"],			
			$username,
			$post["ph_id"]
			));
		}
		
		/*$result = $this->adapter->query('update lstcontacts set ctct_user_mod=?,ctct_date_mod=NOW()  from lstphysicians where ph_id = ? and ph_workaddr = ctct_id and ctct_id <> 9', 
			array(							
			$username,
			$post["ph_id"]
			));
		$result = $this->adapter->query('update lstcontacts set ctct_user_mod=?,ctct_date_mod=NOW()  from lstphysicians where ph_id = ? and ph_ctct_id = ctct_id and ctct_id <> 9', 
			array(							
			$username,
			$post["ph_id"]
			));*/
		$result = $this->adapter->query('update lstcontacts set ctct_user_mod=?,ctct_date_mod=NOW() where ctct_id=(select ph_ctct_id from lstphysicians where ph_id = ? ) and ctct_id <> 9', 
			array(							
			$username,
			$post["ph_id"]
			));
		
		return true;
	}//end
	
	
	public function getAssessment($id, $ctr_id) {             		
			
			$ar = array();			
		   
		   $result = $this->adapter->query('select as_date,as_motiv,as_goals,as_family,as_hobby,as_finobj,as_items from tassesments where as_ph_id = ?  and as_ctr_id = ?   ', array($id, $ctr_id));			
			
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
			$result = $this->adapter->query('select * from vphassessfrm where ph_id = ?  and ctr_id = ?   ', array($id, $ctr_id));			
			
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
		
		$ar = $this->getAssessment($post["ph_id"],$post["ctr_id"]);
		
		if($post["ph_ca_not"]==1)
		{		
			$result = $this->adapter->query('update lstphysicians set ph_ca_not=1,ph_user_mod=?,ph_date_mod= NOW() where ph_id = ?', 
			array(			
			$username,
			$post["ph_id"],
			));			
		}
		else
		{
		
		if($ar["ph_id"]!='') { //there is record - update
			$result = $this->adapter->query('update tassesments set  as_nurse=?, as_date=?, as_motiv=?, as_goals=?, as_family=?, as_hobby=?, as_finobj=?, as_items=?, as_idx=?, as_user_mod=?, as_date_mod=NOW() where as_ph_id=? and as_ctr_id=? limit 1', 
		array(		
		//$post["ph_id"],
		//$post["ctr_id"],
		0, //not nurse
		$post["as_date"],
		$post["as_motiv"],
		$post["as_goals"],
		$post["as_family"],
		$post["as_hobby"],
		$post["as_finobj"],
		$post["as_items"],
		$post["as_idx"]=1, //what is this
		$username,
		$post["ph_id"],
		$post["ctr_id"]
		));
		}
		else{
		$result = $this->adapter->query('insert into tassesments (as_ph_id, as_ctr_id, as_nurse, as_date, as_motiv, as_goals, as_family, as_hobby, as_finobj, as_items, as_idx, as_user_mod, as_date_mod) values (?,?,?,?,?,?,?,?,?,?,?,?,NOW())', 
		array(		
		$post["ph_id"],
		$post["ctr_id"],
		0, //not nurse
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
		} //end else
		
		$result = $this->adapter->query('update lstphysicians set ph_assesm=?,ph_ca_not=0,ph_user_mod=?,ph_date_mod= NOW() where ph_id = ?', 
		array(
		$post["as_date"],
		$username,
		$post["ph_id"],
		));				
			if($result)
			{		
			}
		}
		return true;
	}
	
	
	//new get sources - ajax
	public function getSourceList($id) {             
			
			
			$result = $this->adapter->query('select distinct src_name from vctrsrchistshort where csr_ctr_id = ? order by src_name',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['src_name']=$row->src_name;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
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
		
		/*if Request("is_dm") = "1" then
	     src = "'Direct Mail'"
	     if Request("dm_code") <> "" then dco = "'" & Left(Request("dm_code"),2) & "'"
'	  elseif Request("is_dm") = "2" then
'	     src = "'NAPR'"
	  elseif Request("is_dm") = "3" then
	     src = "'www.phg.com'"
	  elseif Request("is_dm") = "4" then
	     src = "'Fuzion'"
	  elseif Request("is_dm") = "5" then
	     src = "'Physician Work'"
	  elseif Request("is_dm") = "6" then
	     src = "'Email Campaigns'"
	  elseif Request("source") <> "" then
	     src = "'"&Replace(Request("source"),"'","`")&"'"
	  end if*/
	  
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
		
		
			
			$result = $this->adapter->query('insert into tphsourcesn (psr_ctr_id,psr_ph_id,psr_date,psr_source,psr_emp_id,psr_dm_code) values (?,?,?,?,?,?)', 
			array(			
			$post["addsource_ctr_id"],
			$post["ph_id"],
			$post["addsource_date"],
			$src,
			$userid,
			$dm_code,
			));			
		
		
		return true;
	}
	
	public function getSourceHistory($id) {             
			
			
			$result = $this->adapter->query('select * from vphsourcesn where psr_ph_id = ?',
            array($id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['psr_ph_id']=$row->psr_ph_id;
					$ar[$i]['psr_date']=$row->psr_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['psr_source']=$row->psr_source;
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get billing
	public function getBilling($ctr_id, $ph_id) {             
			
			
			$result = $this->adapter->query('call GetReferenceReq(?,?)',
            array($ph_id, $ctr_id));
			
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
	
	public function getAMACheck($ctr_id, $ph_id) {             
			
			
			$result = $this->adapter->query('SELECT * FROM vphreqama WHERE ph_id=? AND ctr_id=? ',
            array($ph_id, $ctr_id));
			
			$ar = array();
			if($result)
			{			
				foreach ($result as $row) {
					$ar['ph_id']=$row->ph_id;
					$ar['ph_spm_year']=$row->ph_spm_year;
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
	
	public function searchClients($post) {             
			
			$cstate=$post["cstate"];
			$ccity=$post["ccity"];
			
			if($ccity!="" && $cstate!=""){
				$result = $this->adapter->query('select * from vclientlookupsmall where cli_status = 1 and ctct_addr_c=? and ctct_st_code = ? order by ctct_name',
				array($ccity, $cstate));
			}
			else if($cstate!=""){
				$result = $this->adapter->query('select * from vclientlookupsmall where cli_status = 1 and ctct_st_code = ? order by ctct_name',
				array($cstate));
			}
			else if($ccity!=""){
				$result = $this->adapter->query('select * from vclientlookupsmall where cli_status = 1 and ctct_addr_c = ? order by ctct_name',
				array($ccity));
			}
			
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['city_state']=$row->city_state;
					
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function addContPresent($post) {             		
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
		
		if($post["ph_id"]=='' || $post["cli_id"]=='' || $post["req"]=='' )
			return false;
	  
			$result = $this->adapter->query('select ctr_id,ctr_status from allcontracts where ctr_cli_id = ? and ctr_type="CP" ', 
			array(			
			$post["cli_id"]
			));		
			if($result)
			{			
				foreach ($result as $row) {
					$ctrid=$row->ctr_id;
					$ctrstu=$row->ctr_status;
				}
			}
			if($ctrid==0 || $ctrid=='' || $ctrid==null) //if not contract create new
			{
				$phspec = "US ";
				//sql = "select cli_emp_id,ctct_addr_c,ctct_st_code from vClient where cli_id = " & cliid
				$result = $this->adapter->query('select cli_emp_id,ctct_addr_c,ctct_st_code from vclient where cli_id = ?', 
				array(			
				$post["cli_id"]
				));		
				if($result)
				{			
					foreach ($result as $row) {
						$market=$row->cli_emp_id;
						$ccity=$row->ctct_addr_c;
						$cstate=$row->ctct_st_code;
						
					}
				}
				if($market=="" || $ccity==""){ 
					$market=51;
					$ccity="Not Found";
					$cstate="--";
				}

				$result = $this->adapter->query('call AddContCont(?,?,?,?,?,?,?,?,?) ', 
				array(			
				$post["cli_id"],
				$phspec,
				1,
				$market,
				$post["req"],
				0,
				$ccity,
				$cstate,
				$username
				));		
				
				$result = $this->adapter->query('select ctr_id,ctr_status from allcontracts where ctr_cli_id = ? and ctr_type="CP" ', 
				array(			
				$post["cli_id"]
				));		
				if($result)
				{			
					foreach ($result as $row) {
						$ctrid=$row->ctr_id;
						$ctrstu=$row->ctr_status;
					}
				}
				if($ctrid==0 || $ctrid=='' || $ctrid==null) //if not contract create new
				{
					$ctrid=0;						
				}
			}//end if create new contract
								
				if($ctrstu!=1 && $ctrid!=0)
				{
					$result = $this->adapter->query('update allcontracts set ctr_status=1, ctr_chs_re=NOW(), ctr_user_mod=?, ctr_date_mod=NOW(), ctr_recruiter=? where ctr_id= ? LIMIT 1', 
					array(			
					$username,
					$post["req"],
					$ctrid						
					));	
				}
				if($ctrid==0||$ctrid==''){
					echo "Could not get contract ID!";
				}
				else{
					$result = $this->adapter->query('insert into tctrpipl (pipl_ctr_id, pipl_emp_id, pipl_ph_id, pipl_status, pipl_date) values (?,?,?,?,NOW())', 
					array(	
					$ctrid,
					$post["req"],
					$post["ph_id"],
					8					
					));	
				}			
				
				
			
		
		
		return true;
	}
	
	
	public function passPhysCareer($post, $identity) {             
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
			
			$result = $this->adapter->query('insert into tmp41 (phiid) values (?)',
            array($post["ph_id"]));	
			
			$result = $this->adapter->query('insert into tphpasses (pp_ph_id,pp_emp_from,pp_date,pp_src_id,pp_emp_to) values (?, ?, NOW(), ?, ?)',
            array($post["ph_id"],$userid,$post["src"],$post["to"]));			
			
			
			
			//add note
			$note = "<em>Automatic Note:</em> Passed to PhysicianCareer Data Entry by <b>".$realname."</b>";
		
			$result = $this->adapter->query('insert into allnotes (note_user, note_ref_id, note_type, note_emp_id, note_text, note_reserved, note_update, note_dt)  values (?,?,?,?,?,?,?, NOW())', 
			array(			
				$username,
				$post["ph_id"],
				3,
				$userid,
				$note,
				NULL,
				0
			));
	
	
			$post["activ_type"]="8";
			$post["sched_act_notes"]="Physician Pass from the Database form";
			$post["schedact_date"]=date('Y-m-d');
			$post["act_user"]=$userid; //to
			$post["act_ref"]=$post["ph_id"];
			$post["act_ret"]=3;
			$post["act_ct"]=$post["ph_ct"];
			$post["act_tx"]="Dr. ".$post["ph_tx"];	
		
			$this->addActivity($post, $identity);
			
			return true;
    }
	
	public function getCandidateAssessment($post) {             
			
			
			$result = $this->adapter->query('select * from vphassessrpt where ph_id = ?',
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
					$ar['as_date']= date('F j, Y', strtotime( $row->as_date));		
					$ar['motiv']=str_replace('’',"'",$row->motiv);		
					$ar['goals']=trim(str_replace('-','-',str_replace("’","'",$row->goals)));	
					$ar['fam']=str_replace('’',"'",$row->fam);		
					$ar['hob']=str_replace('’',"'",$row->hob);	
					$ar['items']=$row->items;		
					$ar['lic']=$row->lic;						
				}
			}
			
			return $ar;
    }
	
	public function enableFuzion($post) {             //put back on fuzion export list
		
		if($post["ph_id"]!='' && $post["ph_id"]>0){
			
			$result = $this->adapter->query('delete from tmp4 where phid=? limit 1',
            array($post["ph_id"]));	
		}	
			
		return true;
			
	}
	
	
	
}
