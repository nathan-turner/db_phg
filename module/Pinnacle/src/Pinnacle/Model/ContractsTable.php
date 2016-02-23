<?php
// module/Pinnacle/src/Pinnacle/Model/ContractsTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;



class ContractsTable extends Report\ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vctrlookup',
            '`ctr_id`, `ctr_no`, `ctr_cli`, `ctr_date`, `ctr_date0`, `ctr_spec`, `st_name`, `ctr_recruiter`, `cli_id`, `ctr_type`, `ctr_city`, `ctr_state`, `ctr_status`, `uname`, `ctr_nurse`, `ctr_nu_type`');
    }
    
    /**
     * @param array $ar     keys: see ContractsForm
    */
    public function buildQuery(array $ar = null) {
        if( is_array($ar) && $ar['emp_id'] ) {
            $where = new Where();
            $nduh = 0;

            if( trim($ar['ctr_no']) ) {
                $like = addslashes(trim($ar['ctr_no']));
                $where->like('ctr_no',"$like%"); $nduh++;
            }
            if( strlen(trim($ar['ctr_cli'])) > 3 ) {
                $like = addslashes(trim($ar['ctr_cli']));
                $where->like('ctr_cli',"%$like%"); $nduh++;
            }
            if( trim($ar['ctr_city']) ) {
                $like = addslashes(trim($ar['ctr_city']));
                $where->like('ctr_city',"$like%"); $nduh++;
            }
            if( trim($ar['ctr_type']) ) {
                $like = addslashes(trim($ar['ctr_type']));
                $where->like('ctr_type',"$like%"); $nduh++;
            }
            if( $ar['ctr_state'] ) { $where->equalTo('ctr_state',$ar['ctr_state']); $nduh++; }
            if( $ar['ctr_nurse'] ) {
                if( $ar['nu_type'] && $ar['nu_type'] !== '0000000000' ) {
                    $like = addslashes(trim($ar['nu_type']));
                    $where->like('ctr_nu_type',"$like%");
                }
                $where->equalTo('ctr_nurse',1); $nduh++;
            }
            elseif( $ar['ctr_spec'] ) { $where->equalTo('ctr_spec',$ar['ctr_spec']); $nduh++; }
            if( $ar['ctr_recruiter'] ) { $where->equalTo('ctr_recruiter',$ar['ctr_recruiter']); $nduh++; }
            if( $ar['ctr_status'] >= 0 ) { $where->equalTo('ctr_status',$ar['ctr_status']); $nduh++; }
            if( $ar['date1'] && $ar['date2'] ) { $where->between('ctr_date',$ar['date1'],$ar['date2']);$nduh++; }
            elseif( $ar['date1'] ) { $where->greaterThanOrEqualTo('ctr_date',$ar['date1']); $nduh++; }
            elseif( $ar['date2'] ) { $where->lessThanOrEqualTo('ctr_date',$ar['date2']); $nduh++; }

            return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
    }

    public function fetchAll($id = 0, array $ar = null) {
        $where = $this->buildQuery($ar);
        if( $where ) {
            $select = new Select($this->table);
            $select->where($where);
            $select->order('ctr_date DESC');
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
            $str = @$sql->getSqlStringForSqlObject($select);
            $str = str_replace('SELECT * ','SELECT count(*) as cnt ',$str); // awful
            $rowSet = $this->adapter->query($str)->execute();
            foreach ($rowSet as $rec)
                $total = $rec['cnt'];
            if( $total ) $total += $pgsize;
            return intval($total / $pgsize);
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
            $ar['cliurl'] = $urls->fromRoute('client', array('action'=>'view', 'part'=>'go', 'id' => $row->cli_id));
            $ar['ctrurl'] = $urls->fromRoute('contract', array('action'=>'view', 'part'=>'go', 'id' => $row->ctr_id));
            if( !$row->ctr_city ) $row->ctr_city = ' ';

            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }
	
	//new get contract data
	public function selectContract($id = 0, array $ar = null) {   
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
			
			$this->table='vcontracts1';			
			$select = new Select($this->table);
			$select
			//->from(array('c'=>'vclient'))
			//->where('cli_id = ?',$id);
			->where->equalTo('ctr_id',$id);
			$resultSet = $this->selectWith($select);			
	
			if($resultSet)
			{
				foreach ($resultSet as $row) {
					$ar['ctr_id']=$id;
					$ar['ctr_no']=$row->ctr_no;
					//$ar['ctr_nu_type']=$row->ctr_nu_type;
					$ar['ctr_marketer']=$row->ctr_marketer; 
					$ar['ctr_src_termdt']=$row->ctr_src_termdt;
					$ar['ctct_name']=$row->ctct_name;
					$ar['cli_ctct_id']=$row->cli_ctct_id;
					$ar['cli_id']=$row->cli_id;
					$ar['ctr_cli_bill']=(string)$row->ctr_cli_bill;
					$ar['ctr_status']=$row->ctr_status;
					$ar['st_name']=$row->st_name;
					$ar['ctr_date']=$row->ctr_date;
					$ar['ctr_date2']=$row->ctr_date2; //formatted Y-m-d 
					$ar['datepicker']=$row->ctr_date2;
					$ar['ctr_retain_date']=$row->ctr_retain_date;
					$ar['ctr_retain_date2']=$row->ctr_retain_date2;
					$ar['retaindatepicker']=$row->ctr_retain_date2;
					//$ar['ctr_spec']=$row->ctr_spec;
					$ar["ctr_spec"] = str_replace('|','',$row->ctr_spec);
					$ar['ctr_manager']=$row->ctr_manager;
					$ar['ctr_shortnote']=$row->ctr_shortnote;
					$ar['ctr_wkupdate']=$row->ctr_wkupdate;
					$ar['st_name']=$row->st_name;
					$ar['emp_uname']=$row->emp_uname;
					$ar['ctr_type']=$row->ctr_type;
					$ar['ctr_amount']=$row->ctr_amount;
					$ar['ctr_monthly']=$row->ctr_monthly;
					$ar['ctr_guarantee']=$row->ctr_guarantee;
					$ar['sp_name']=$row->sp_name;
					$ar['ctr_status']=$row->ctr_status;
					$ar['ctr_pro_date']=$row->ctr_pro_date;
					$ar['ctr_pro_date2']=$row->ctr_pro_date2;
					$ar['ctr_pro_sces']=$row->ctr_pro_sces;
					$ar['ctr_pro_sheet']=$row->ctr_pro_sheet;
					$ar['ctr_pro_lett']=$row->ctr_pro_lett;
					$ar['ctr_src_cnt']=$row->ctr_src_cnt;
					$ar['ctr_src_cnt2']=$row->ctr_src_cnt2;
					$ar['ctr_src_sent']=$row->ctr_src_sent;
					$ar['ctr_src_appr']=$row->ctr_src_appr;
					$ar['ctr_src_appr2']=$row->ctr_src_appr2;
					$ar['ctr_src_writer']=(string)$row->ctr_src_writer;
					$ar['ctr_src_list']=$row->ctr_src_list;
					$ar['ctr_src_list2']=$row->ctr_src_list2;
					$ar['ctr_src_print']=$row->ctr_src_print;
					$ar['ctr_src_print2']=$row->ctr_src_print2;
					$ar['ctr_src_dmdate']=$row->ctr_src_dmdate;
					$ar['ctr_src_dmdate2']=$row->ctr_src_dmdate2;
					$ar['ctr_tqc_wl_1']=$row->ctr_tqc_wl_1;
					$ar['ctr_tqc_wl_2']=$row->ctr_tqc_wl_2;
					$ar['ctr_tqc_45_1']=$row->ctr_tqc_45_1;
					$ar['ctr_tqc_45_2']=$row->ctr_tqc_45_2;
					$ar['ctr_tqc_180_1']=$row->ctr_tqc_180_1;
					$ar['ctr_tqc_180_2']=$row->ctr_tqc_180_2;
					$ar['ctr_tqc_ann_1']=$row->ctr_tqc_ann_1;
					$ar['ctr_tqc_ann_2']=$row->ctr_tqc_ann_2;
					$ar['ctr_tqc_hold_1']=$row->ctr_tqc_hold_1;
					$ar['ctr_tqc_hold_2']=$row->ctr_tqc_hold_2;
					$ar['ctr_tqc_canc_1']=$row->ctr_tqc_canc_1;
					$ar['ctr_tqc_canc_2']=$row->ctr_tqc_canc_2;
					$ar['ctr_tqc_pl_1']=$row->ctr_tqc_pl_1;
					$ar['ctr_tqc_pl_2']=$row->ctr_tqc_pl_2;
					$ar['ctr_chs_hold']=$row->ctr_chs_hold;
					$ar['ctr_chs_canc']=$row->ctr_chs_canc;
					$ar['ctr_chs_spec']=$row->ctr_chs_spec;
					$ar['ctr_chs_re']=$row->ctr_chs_re;
					$ar['ctr_src_dmurl']=$row->ctr_src_dmurl;
					$ar['ctr_responses']=$row->ctr_responses;
					$ar['ctr_location_c']=$row->ctr_location_c;
					$ar['ctr_location_s']=$row->ctr_location_s;
					$ar['bill_id']=$row->bill_id;
					$ar['bill_name']=$row->bill_name;
					$ar['ctr_nurse']=$row->ctr_nurse;
					$ar['ctr_nu_type']=$row->ctr_nu_type;
					$ar['at_name']=$row->at_name;	
					$ar['ctr_nomktg']=$row->ctr_nomktg;						
					
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['cd_text']=$row->cd_text;
					$ar['cd_html']=$row->cd_html;
					$ar['ctr_src_term']=$row->ctr_src_term;
					$ar['ctr_recruiter']=$row->ctr_recruiter;
					$ar['ctr_rec']=$row->ctr_recruiter;
				//$ar['ctr_manager']=$row->ctr_manager;
					
				}
			}
			
			$result = $this->adapter->query('call GetPIPLR(?)',
            array($id));			
			
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar['icpl']=$row->icpl;
					$ar['s2pl']=$row->s2pl;
					$ar['pre']=$row->pre;
					$ar['i2pl']=$row->i2pl;
					$ar['pr2i']=$row->pr2i;
					$ar['ican']=$row->ican;
					
					$i+=1;
				}
			}
			
			
		return $ar;	
    }
	
	//new get contract data
	public function selectPipl($id = 0, array $ar = null) {   
		$ar = array();
		//$result = $this->adapter->query('select * from vphpipl where ctr_id = ? order by pipl_date', array($id));			
		$result = $this->adapter->query('select * from vpiplins where pipl_ctr_id = ? order by pipl_date', array($id));			
			
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['pipl_nurse']=$row->pipl_nurse;
					$ar[$i]['an_id']=$row->an_id;
					$ar[$i]['ph_id']=$row->ph_id;
					$ar[$i]['nu_name']=$row->nu_name;
					$ar[$i]['nu_title']=$row->nu_title;
					$ar[$i]['at_abbr']=$row->at_abbr;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_title']=$row->ctct_title;
					$ar[$i]['ph_spec_main']=$row->ph_spec_main;
					$ar[$i]['pipl_date']=$row->pipl_date;
					$ar[$i]['pist_name']=$row->pist_name;
					$ar[$i]['pipl_id']=$row->pipl_id;
					$ar[$i]['pipl_cancel']=$row->pipl_cancel;
										
					$i+=1;
				}
			}
			
			return $ar;
	
	}
	
	
	//save edit contract
	public function saveContract(Array $contract, $id, $identity) {
	
		$id  = (int) $id; 
		$userid = $_COOKIE["phguid"];		
		$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$emp_user_mod=$row->emp_user_mod;
					$dept=$row->emp_dept;
					$emp_name=$row->emp_realname;
				}
			}
		//$contract["ctr_spec"] = substr($contract["ctr_spec"] , 0, 3);
		$contract["ctr_spec"] = str_replace('|','',trim($contract["ctr_spec"]));
		$contract["ctr_spec"] = str_replace('&nbsp;','',trim($contract["ctr_spec"]));
		$contract["ctr_spec"] = str_replace(' ','',$contract["ctr_spec"]);
			
		if($contract["ctr_no"]!='' && $contract["ctr_status"]>0)
		{
        // prepare sql
        $result = $this->adapter->query('Call EditAContRact (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array($id, $contract["cli_id"], $contract["ctr_no"], $contract["ctr_cli_bill"],
				$contract["ctr_date"], str_replace('|','',$contract["ctr_spec"]), $contract["ctr_status"], $contract["ctr_rec"],
				$contract["ctr_manager"], $contract["ctr_amount"], $contract["ctr_monthly"], '0', //guarantee hard-coded - used for what??
				$contract["ctr_location_c"], $contract["ctr_location_s"], $emp_user_mod, 'NOW()',
				$contract["ctr_type"], $contract["ctr_marketer"], $contract["ctr_retain_date"], $contract["ctr_shortnote"],
				$contract["ctr_nomktg"], $contract["ctr_nurse"], $contract["ctr_mid"], $contract["ctr_wkupdate"]));

		}
        return result; 
    }
	
	//new get comments
	public function getComments($id = 0, $cli_id) {         
            
			$this->table='allnotes';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('note_type',2)
			->where->equalTo('note_ref_id',$cli_id);
			$select->order('note_dt DESC');
			$resultSet = $this->selectWith($select);
			
			$result = $this->adapter->query('select* from allnotes where note_type = 2 and note_ref_id = ?  and (note_reserved = ? OR note_reserved =0) order by note_dt desc',
            array($cli_id, $id));
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['note_user']=$row->note_user;
					$ar[$i]['note_text']=str_replace('ctrchange4.asp','/public/contract/ctrchange4',$row->note_text);
					$ar[$i]['note_dt']=$row->note_dt;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new pass to locum - for ajax
	public function addChangeRequest($post, $identity) {         
            $userid = $_COOKIE["phguid"];
			if($post["chg_spec"]=="")
				$post["chg_spec"]="---";
			if($post["pipl"]==0)
				$post["pipl"]=NULL;
			
			$cli_name = $post["loc_cliname"];
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
					$emp_name=$row->emp_realname;
				}
			}
			//"EXEC AddAChgRequest $ctrid,$chtyp,$reas,$comm,$spec,$req,\'$ctme\',$unam,$pipl,$nut";
			$result = $this->adapter->query('CALL AddAChgRequest( ?,?,?,?,?,?,?,?,?,?)'
			,
            array(                
				$post["ctr_id"], 			
				$post["ctr_type"],
				$post["stat_reason"],
				$post["stat_comments"],
				$post["chg_spec"], //specialty
				$userid, //recruiter
				'NOW()',
				$post["chg_rec"],
				$post["pipl"],
				$post["chg_nut"]
						     
			));
			if($result)
			{			
				foreach ($result as $row) {
					$plid=$row->chg_id;					
				}
			}		
	 		
			$message =
			"This is an automatically generated message\n\nA Change Contract Status Request has been placed by ".$emp_name."\r\n
			Please click on the link below to review the changes requested: \r\n
			http://testdb.phg.com/public/contract/approvectrchange?chg_id=".$plid."\n\n\n".$post["stat_comments"];
			//http://db.phg.com/pinnacle/ctrchange2.asp?chg_id=".$plid."\n\n\n".$post["statcomments"];
	
		
$from = "postmaster@phg.com";	
//$to = "jcouvillon@phg.com";
$to = "mbroxterman@phg.com";
//$to = "nturner@phg.com";
$subject = "Subject: DB: Contract Status Change Request from ".$note_user;
$headers = "From: ".$from."\r\n";
$headers .= "CC: nturner@phg.com\r\n";
$headers .=  'X-Mailer: PHP/'.phpversion()."\r\n";
			
mail ($to, $subject, $message, $headers);
 			
			//return $note_user;
			return true;
    }
	
	//new get specialties
	public function getSpecialties() {         
            
			$this->table='dctspecial';			
			$select = new Select($this->table);			
			$select->order('sp_code ASC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{		
				$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['sp_code']=$row->sp_code;
					$ar[$i]['sp_name']=$row->sp_name;
					$ar[$i]['sp_group']=$row->sp_group;
					$i+=1;			
				}
			}		
			
			
			return $ar;
    }
	
	//get recruiters for drop down
	public function getRecruiters(){
		
		$ar = array();
		
		$result = $this->adapter->query("select * from vemplist WHERE emp_dept LIKE '%R%' OR emp_dept LIKE '%LR%' OR emp_dept LIKE '%LA%' order by ctct_name")->execute();
		
		foreach ($result as $row) 
			{
				$ar[$row['emp_id']]=$row['ctct_name'];
			}
		
		return $ar;
	}
	
	//get recruiters for drop down
	public function getUsers(){
		
		$ar = array();
		
		$result = $this->adapter->query("select * from vemplist  order by ctct_name")->execute();
		
		foreach ($result as $row) 
			{
				$ar[$row['emp_id']]=$row['ctct_name'];
			}
		
		return $ar;
	}
	
	//new get specialties
	public function getSpecialtyOptions() {         
            
			/*$this->table='dctSpecial';			
			$select = new Select($this->table);			
			$select->order('sp_code ASC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{						
				foreach ($resultSet as $row) {
					$ar[$row->sp_code]=$row->sp_name;
					//$ar[$i]['sp_name']=$row->sp_name;
					//$ar[$i]['sp_group']=$row->sp_group;
						
				}
			}	*/
			
			$ar = array();
		
			$result = $this->adapter->query("select * from dctspecial order by sp_code")->execute();
		
			foreach ($result as $row) 
			{
				$ar[$row['sp_code']]=$row['sp_name'];
			}
			
			
			return $ar;
    }
	
	//new get specialties
	public function getMidlevelOptions() {         
            			
			
			$ar = array();
		
			$result = $this->adapter->query("select * from dctalliedtypes order by at_code")->execute();
		
			foreach ($result as $row) 
			{
				//$name=$row['at_name'];
				$len = strlen($row['at_code'])-1;
				$name = str_repeat("=", $len);
				$name.=$row['at_name'];
				$ar[$row['at_code']]=$name;
			}
			
			
			return $ar;
    }
	
	//new update direct mail 
	public function updateDirectMail($post, $identity) {            
            $userid = $_COOKIE["phguid"];
			$ctr_id=$post["ctr_id"];
			$ctr_src_cnt=$post["ctr_src_cnt"];
			$ctr_src_appr=$post["ctr_src_appr"];
			$ctr_src_list=$post["ctr_src_list"];
			$ctr_src_print=$post["ctr_src_print"];
			$ctr_src_dmdate=$post["ctr_src_dmdate"];
			$ctr_src_dmurl=$post["ctr_src_dmurl"];
			$src_descr=$post["src_descr"];
			$src_dhtml=$post["src_dhtml"];
			
			if($ctr_id!="")
			{
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
						$emp_name=$row->emp_realname;
					}
				}
			
			$result = $this->adapter->query('update allcontracts set ctr_src_cnt=?, ctr_src_appr=?, ctr_src_list=?, ctr_src_print=?, ctr_src_dmdate=?, ctr_src_dmurl=? where ctr_id = ? LIMIT 1',
            array($ctr_src_cnt, $ctr_src_appr, $ctr_src_list, $ctr_src_print, $ctr_src_dmdate, $ctr_src_dmurl,   $ctr_id));
			
			$ar = array();
			if($result)
			{
			//do nothing
			}
			
			$result = $this->adapter->query('select * from tctrdescription where cd_ctr_id = ? ', array($ctr_id));
			$num=$result->count();
			if($num>0)
			{
				$result = $this->adapter->query('update tctrdescription set cd_text=?, cd_html=?, cd_user_mod=?, cd_date_mod=NOW() where cd_ctr_id = ? LIMIT 1',
				array($src_descr, $src_dhtml, $note_user, $ctr_id));
			}
			else{
				$result = $this->adapter->query('insert into tctrdescription (cd_ctr_id, cd_text, cd_html, cd_user_mod, cd_date_mod) VALUES (?,?,?,?,NOW()) ',
				array($ctr_id, $src_descr, $src_dhtml, $note_user));
			}			
			
			}
			return true;
    }
	
	//new get responses
	public function getResponses($id) {        
            
			$ctr_id=$id;
			
			$ar = array();
		
			$result = $this->adapter->query("select  * from vctrsourcesrpt where psr_ctr_id = ? LIMIT 1000 union select  * from vctrsourcesrptnu where nsr_ctr_id = ? order by psr_date desc LIMIT 1000", array($ctr_id, $ctr_id));
			$i=0;
			foreach ($result as $row) 
			{
				$ar[$i]['ph_id']=$row->ph_id;
				$ar[$i]['ctct_name']=$row->ctct_name;
				$ar[$i]['ctct_title']=$row->ctct_title;
				$ar[$i]['ph_spec_main']=$row->ph_spec_main;
				$ar[$i]['psr_date']=$row->psr_date;
				$ar[$i]['psr_source']=$row->psr_source;
				$ar[$i]['psr_dm_code']=$row->psr_dm_code;
				$i+=1;			
			}
			
			
			return $ar;
    }
	
	//new get responses
	public function getResponseCount($id) {        
            
			$ctr_id=$id;
			
			$ar = array();
		
			$result = $this->adapter->query("select count(psr_id) as cnt, psr_source from vctrsourcesrpt where psr_ctr_id =? group by psr_source order by cnt desc", array($ctr_id));
			$i=0;
			$cnt=0;
			foreach ($result as $row) 
			{
				$ar[$i]['cnt']=$row->cnt;
				$ar[$i]['psr_source']=$row->psr_source;
				$cnt+=$row->cnt;
				$i+=1;			
			}
			$ar[$i-1]['total']=$cnt;
			
			return $ar;
    }
	
	//new source types
	public function getSourceTypes($opt=0) {        
            		
			$ar = array();
			if($opt==0)
				$result = $this->adapter->query("select * from dctsourcetypes where srt_id <> 0 and srt_id <> 7 order by srt_order")->execute();
			else
				$result = $this->adapter->query("select * from dctsourcetypes  order by srt_order")->execute();
			
			$i=0;			
			foreach ($result as $row) 
			{
				$ar[$i]['srt_id']=$row['srt_id'];
				$ar[$i]['srt_name']=$row['srt_name'];
				//$ar[$i]['srt_order']=$row->srt_order;
				/*$ar['srt_id']=$row->srt_id;
				$ar['srt_name']=$row->srt_name;
				$ar['srt_order']=$row->srt_order;*/
				
				$i+=1;			
			}	
			
			return $ar;
    }
	
	//new source types
	public function getSources() {        
            		
			$ar = array();	
			$ar2 = array();	
			$result = $this->adapter->query("select src_id, src_name, src_price, src_pricing, src_rating, src_sp_code, src_type from tsources where (src_id <> 5 and src_type<>0) order by src_type, src_name")->execute();
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
	
	public function OrderNewSource()
	{
		$userid = $_COOKIE["phguid"];
		$ctr_id = $_POST["ctr_id"];
		$term = $_POST["campaign_term"];
		$mon = $_POST["campaign_month"];
		//NEW		
		$dte = date('Y-m-d', mktime(0, 0, 0, $mon  , 1, date("Y")));
		
		if($ctr_id!="" && $term!="" && $mon!="" && $ctr_id!=0 && $term!=0 && $mon!=0)
		{
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
				$emp_name=$row->emp_realname;
			}
		}
		foreach($_POST["sourcechk"] as $key=>$val)
		{
			$sid=$val;
			/*echo $_POST["ctr_id"]."<br/>";
			echo $_POST["campaign_term"]."<br/>";
			echo $_POST["campaign_month"]."<br/>";
			echo $note_user."<br/>";*/
			$result = $this->adapter->query("call OrderNewSource (?,?,?,?,?,?)", array($sid,$ctr_id,$note_user,$term,0,$dte));
			//$result = $this->adapter->query("call OrderNewSource (?,?,?,?,?,?)", array($sid,$ctr_id,$note_user,$term,0,$mon));
		}
			return true;
		}
		else { echo "Contract ID and Term and Month required"; }
	}
	
	//new get sources
	public function getSourceDetails($id) {        
            $ctr_id = $id;		
			$ar = array();
			if($id!=""&&$id>0)
			{		
			$result = $this->adapter->query("select ctr_no,ctr_location_c,ctr_location_s,ctr_spec,ctr_src_term, ctr_src_termdt,ctr_nurse, MONTH(ctr_src_termdt) AS term_month, at_abbr, emp_realname from allcontracts as c left join dctalliedtypes as d ON d.at_code=c.ctr_nu_type left join lstemployees as e ON e.emp_id=c.ctr_recruiter where ctr_id =?", array($ctr_id));
			$i=0;
			foreach ($result as $row) 
			{
			//$ar[$i]['csr_id']=$row->csr_id;
				$ar['ctr_no']=$row->ctr_no;
				$ar['ctr_location_c']=$row->ctr_location_c;
				$ar['ctr_location_s']=$row->ctr_location_s;
				$ar['ctr_spec']=$row->ctr_spec;
				$ar['ctr_src_term']=$row->ctr_src_term;
				$ar['ctr_src_termdt']=$row->ctr_src_termdt;
				$ar['ctr_nurse']=$row->ctr_nurse;
				$ar['term_month']=$row->term_month; //term starting in month	
				$ar['at_abbr']=$row->at_abbr;
				$ar['emp_realname']=$row->emp_realname;
				$i+=1;			
			}		
			
			}
			return $ar;
    }
	
	//new get sources
	public function getSourceHistory($id) {        
            $ctr_id = $id;		
			$ar = array();
			if($id!=""&&$id>0)
			{		
			$result = $this->adapter->query("select * from vctrsrcshedhistory where csr_ctr_id = ? and src_type not in (0) order by csr_add_date desc,csd_date desc ", array($ctr_id));
			$i=0;
			foreach ($result as $row) 
			{
			//$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['csr_ctr_id']=$row->csr_ctr_id;
				$ar[$i]['src_id']=$row->src_id;
				$ar[$i]['csr_add_date']=$row->csr_add_date;
				$ar[$i]['csr_appr_date']=$row->csr_appr_date;
				$ar[$i]['csr_rating']=$row->csr_rating;
				$ar[$i]['csr_status']=$row->csr_status;
				$ar[$i]['src_name']=$row->src_name;
				$ar[$i]['src_type']=$row->src_type; 		
				$ar[$i]['csr_price']=$row->csr_price; 
				$ar[$i]['csr_startyear']=$row->csr_startyear; 
				$ar[$i]['csr_dm_piece']=$row->csr_dm_piece; 
				$ar[$i]['csr_dm_ama']=$row->csr_dm_ama; 
				$ar[$i]['csr_dm_postage']=$row->csr_dm_postage; 
				$ar[$i]['csr_dm_count']=$row->csr_dm_count; 
				$ar[$i]['csr_dm_code']=$row->csr_dm_code;
				$i+=1;			
			}		
			
			}
			return $ar;
    }
	
	//new get sources
	public function getActiveSources($id, $status='') {        
            if($id!=""&&$id>0)
				$ctr_id=$id;
			else
				$ctr_id = $_POST["ctr_id"];		
			$ar = array();
			if ($status==1 || $status==16)
				$result = $this->adapter->query("select *, FORMAT(csr_price, 2) AS csr_price2, DATE_FORMAT(csr_date_placed, '%m/%d/%Y') AS csr_date_placed2, FORMAT(csr_dm_postage, 2) AS csr_dm_postage2, FORMAT(csr_dm_ama, 2) AS csr_dm_ama2 from vsourceshedactive where src_id <> 764 AND csr_ctr_id = ? order by src_type,csr_id", array($ctr_id));			
			else
				$result = $this->adapter->query("select *, FORMAT(csr_price, 2) AS csr_price2, DATE_FORMAT(csr_date_placed, '%m/%d/%Y') AS csr_date_placed2, FORMAT(csr_dm_postage, 2) AS csr_dm_postage2, FORMAT(csr_dm_ama, 2) AS csr_dm_ama2 from vsourceshedactive where csr_ctr_id = ? order by src_type,csr_id", array($ctr_id));
			$i=0;
			foreach ($result as $row) 
			{
			//$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['src_id']=$row->src_id;
				$ar[$i]['csr_ctr_id']=$row->csr_ctr_id;
				$ar[$i]['csr_appr_date']=$row->csr_appr_date;
				$ar[$i]['csr_term']=$row->csr_term;
				$ar[$i]['csr_price']=str_replace(',','',$row->csr_price2);
				$ar[$i]['csr_dm_count']=$row->csr_dm_count;
				$ar[$i]['csr_rating']=$row->csr_rating;
				$ar[$i]['csr_status']=$row->csr_status;
				$ar[$i]['csr_dm_code']=$row->csr_dm_code;
				$ar[$i]['csr_dm_piece']=$row->csr_dm_piece;
				$ar[$i]['src_name']=$row->src_name;
				$ar[$i]['src_type']=$row->src_type;
				$ar[$i]['src_rating']=$row->src_rating;
				$ar[$i]['src_quota']=$row->src_quota;
				$ar[$i]['src_estprice']=$row->src_estprice;
				$ar[$i]['csr_dm_ama']=$row->csr_dm_ama2;
				$ar[$i]['csr_startyear']=$row->csr_startyear;
				$ar[$i]['csr_schedule']=$row->csr_schedule;
				$ar[$i]['csr_note']=$row->csr_note;
				$ar[$i]['csr_date_placed']=$row->csr_date_placed2;
				$ar[$i]['csr_dm_postage']=$row->csr_dm_postage2;
				$i+=1;			
			}		
			//$ar["test"]=11;
			return $ar;
    }
	
	public function updateSourcesTable($id, $post) {
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		//echo var_dump($post);
		foreach ($post as $key=>$val)
		{
			if(strpos($key, 'shi_')!==false) //find shed keys/months to go through
			{
				
				$shed="";
				$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
				$csrid = $csrid[1];
				$shids = str_split($val);
				//$val = $post["shi_".$csrid];
				//echo $val;
				foreach ($shids as $k=>$shid)
				{
					//echo $shid."** ";
					$mml = $post["sh_".$csrid."_".$shid];
					//echo $mml;
					if($mml!="" && isset($mml))
						$shed.=$mml;						
				}
				//echo "SHED: ".$shed;
				if($csrid!="")
				$result = $this->adapter->query('update tctrsourcesn set csr_schedule = ?, csr_user_mod = ? where csr_id = ? LIMIT 1',array($shed, $note_user, $csrid));
				//echo 'update tCtrSourcesN set csr_schedule = '.$shed.', csr_user_mod = '.$note_user.' where csr_id = '.$csrid.' LIMIT 1';
								
			}
			///source dates section doesn't appear to be used but may be added later...
			if(strpos($key, 'act_')!==false) //find activate checkboxes
			{
				$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
				$csrid = $csrid[1];
				$result = $this->adapter->query('call SourceStop(?,?,?,?)',array( $csrid, 0, NULL, $note_user));
				
			}
			if(strpos($key, 'hld_')!==false) //find hold checkboxes
			{
				if($key==1 || $key==true)
				{
					$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
					$csrid = $csrid[1];
					$why = $post["why_".$csrid];
					$why = str_replace("`","'",$why);
					$why = str_replace("'","``", $why);
					$why = str_replace("`","'", $why);					
					if($csrid!="")
					$result = $this->adapter->query('call SourceStop(?,?,?,?)',array( $csrid, 5, $why, $note_user));				
				}
			}
			if(strpos($key, 'dea_')!==false) //find deactivate checkboxes
			{
				$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
				$csrid = $csrid[1];
				if($key==1 || $key==true)
				{
					$why = $post["why_".$csrid];
					$why = str_replace("`","'",$why);
					$why = str_replace("'","``", $why);
					$why = str_replace("`","'", $why);					
					if($csrid!="")
					$result = $this->adapter->query('call SourceStop(?,?,?,?)',array( $csrid, 0, $why, $note_user));
				}
			}
			if(strpos($key, 'can_')!==false) //find cancel checkboxes
			{
				$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
				$csrid = $csrid[1];
				if($key==1 || $key==true)
				{
					if($csrid!="")
					$result = $this->adapter->query('call SourceStop(?,?,?,?)',array( $csrid, 4, 'Draft', $note_user));	
				}
			}
			if(strpos($key, 'pri_')!==false) //find pri checkboxes
			{
				$csrid = preg_split("/_/", $key); //get csrid from field name ex shi_3223
				$csrid = $csrid[1];
				$price = $val;
				$sql1="";
				$dcd = $post["dcd_".$csrid];
				if($dcd!="")
					$sql1 .=",csr_dm_code = '".$dcd."' ";
				$pie = $post["pie_".$csrid];
				if($pie!="" && $pie>0)
					$sql1 .=",csr_dm_piece = '".$pie."' ";
				$pst = $post["pst_".$csrid];
				if($pst!="" && $pst>0)
					$sql1 .=",csr_dm_postage = '".$pst."' ";
				$ama = str_replace(',','',$post["ama_".$csrid]);
				if($ama!="")
					$sql1 .=",csr_dm_ama = '".$ama."' ";
				$adpl = $post["adpl_".$csrid];
				if($adpl !=""){ //date
					$datearr = preg_split("/\//", $adpl);
					$adpl = $datearr[2]."-".$datearr[0]."-".$datearr[1];
					$sql1 .=",csr_date_placed = '".$adpl."' ";
				}
				$note = $post["note_".$csrid];
				if($note!=""){
					$sql1 .=",csr_note = '".htmlspecialchars($note)."' ";
				}
				$dmc = $post["dmc_".$csrid];
				if($dmc>0 && $dmc!="")
					$sql1 .=",csr_dm_count = '".$dmc."' ";
				
				if($csrid>0 && $csrid!="")				
				$result = $this->adapter->query("update tctrsourcesn set csr_price = '".$price."' ".$sql1.", csr_user_mod = '".$note_user."' where csr_id = ".$csrid." LIMIT 1")->execute();
				//echo "update tCtrSourcesN set csr_price = ".$price." ".$sql1.", csr_user_mod = '".$note_user."' where csr_id = ".$csrid;
				//echo "update tctrsourcesn set csr_price = '".$price."' ".$sql1.", csr_user_mod = '".$note_user."' where csr_id = ".$csrid." LIMIT 1";
			}			
		}		
		return true;
	}
	
	public function discardCampaign($id, $post, $auto=0)	//auto is for auto run discard...
	{
		$ctr_id = $id;
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		if(isset($post["discard_chk"]))
		{
			$this->adapter->query('call SourcingEnd (?,NOW(),?,?)',array( $ctr_id,$note_user,$auto));	
		}
		return true;
				
	}
	
	public function extendCampaign($id, $post)	
	{
		$ctr_id = $id;
		$ext_term = $post["ext_term"];
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		$this->adapter->query('call SourcingExt (?,?,?)',array( $ctr_id,$ext_term,$note_user));
		return true;
	}
	
	public function SetProfileDate($id, $post)	
	{
		$ctr_id = $id;
		$profile_date = $post["profile_date"];
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		if($profile_date!="")
		{
		$this->adapter->query('call SetCtrProDate (?,?,?,NOW(),?)',array( $ctr_id,$profile_date,$note_user,0));
		return true;
		}
	}
	
	public function approveSourcing($id, $post)	
	{
		$ctr_id = $id;
		$profile_date = $post["profile_date"];
		$donotemail = $post["donotemail"];
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		foreach($post["csr_id"] as $key=>$val)
		{
			//echo $key."-".$val."-".$post["approve_datetxt"][$key]."<br/>";
			$csr_id=$val;
			$date = $post["approve_datetxt"][$key];			
			if($csr_id!='' && $csr_id!=0 && $date!="" && $ctr_id!=""){ 
				$this->adapter->query('call SetSourceDate (?,?,?,?,?)',array( $csr_id,$ctr_id,'',$date,$note_user)); //not sure what 3rd sht param is
			}
			else {	return false; }
		}		
		if($donotemail!=1 && $donotemail!='1') //if not checked, send email
		{
			$from = "sourcing@phg.com";	
			$to = "jcouvillon@phg.com";
			//$to = "nturner@phg.com"; //CHANGE LATER*****
			$subject = "New Sourcing for Contract# ".$post["ctr_no"];
			$headers = "From: ".$from."\r\n";
			$headers .= "CC: nturner@phg.com \r\n";
			$headers .=  'X-Mailer: PHP/'.phpversion()."\r\n";
			$message = "Sourcing was ordered or rescheduled for the following contract:\n\n";
			
			$result = $this->adapter->query("select * from vctr4source where ctr_id = ?", array($ctr_id));			
			foreach ($result as $row) 
			{
				$ctct_email = $row->ctct_email;
				$ctct_fax = $row->ctct_fax;
				$ctr_nurse = $row->ctr_nurse;
				$ctr_spec = $row->ctr_spec;
				$descr = $row->cd_text;
				if($ctr_nurse)
					$ctr_spec = "Mid-level";
				$message .= "Contract # ".$post["ctr_no"]." - ".$ctr_spec." " . $row->ctr_location_c . ", " . $row->ctr_location_s . "\n";
				$message .= "   http://testdb.phg.com/public/contract/view/".$ctr_id."\n\n"; ///change link
				$message .= "Text-only description:\n\n".htmlspecialchars_decode($descr)."\n\nHTML version:\n\n".$descr."\n\n";
				$message .= $row->ctct_name." ".$row->ctct_title."\nPhone: 1-800-492-7771 Fax: ".$ctct_fax."\nEmail: ".$row->ctct_email."\n";
			}			
			
			mail ($to, $subject, $message, $headers);
			header("location: /contract/view/".$ctr_id."\n\n");
		}		 
		return true;
		
	}
	
	public function getContact($id, $ctr_id) {        
            	
			$ar = array();
			if($id!=""&&$id>0)
			{		
			$result = $this->adapter->query("select ctct_name,ctct_title from lstcontacts where ctct_id =?", array($id));
			
			foreach ($result as $row) 
			{
			//$ar[$i]['csr_id']=$row->csr_id;
				$ar['ctct_name']=$row->ctct_name;
				$ar['ctct_title']=$row->ctct_title;							
							
			}
			$result = $this->adapter->query("select * from vsrccampaign1 where ctr_id = ?", array($ctr_id));
			
			foreach ($result as $row) 
			{			
				$ar['ctr_no']=$row->ctr_no;
				$ar['cli_id']=$row->cli_id;
				$ar['cli_ctct_id']=$row->cli_ctct_id;				
				$ar['cli_name']=$row->cli_name;
				$ar['ctr_date']=$row->ctr_date;
				$ar['ctr_spec']=$row->ctr_spec;
				$ar['emp_name']=$row->emp_name;
				$ar['sp_name']=$row->sp_name;
				$ar['ctr_location_c']=$row->ctr_location_c;
				$ar['ctr_location_s']=$row->ctr_location_s;
				$ar['ctr_src_term']=$row->ctr_src_term;
				$ar['ctr_src_termdt']=$row->ctr_src_termdt;
				$ar['ctr_nurse']=$row->ctr_nurse;
				$ar['at_name']=$row->at_name;	
				//$ar['ctr_recruiter']=$row->ctr_recruiter;
				//$ar['ctr_manager']=$row->ctr_manager;
			}	
			}
			return $ar;
    }
	
	public function getCampaign($id, $type) {        
            	
			$ar = array();
			if($id!=""&&$id>0)
			{		
			if($type==1)
				$result = $this->adapter->query("select * from vsrccampaign2 where csr_ctr_id = ? and src_type = 1", array($id));
			else
				$result = $this->adapter->query("select * from vsrccampaign2 where csr_ctr_id = ? order by src_hack", array($id));
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['src_id']=$row->src_id;
				$ar[$i]['csr_ctr_id']=$row->csr_ctr_id;				
				$ar[$i]['csr_startyear']=$row->csr_startyear;
				$ar[$i]['csr_schedule']=$row->csr_schedule;
				$ar[$i]['csr_price']=$row->csr_price;
				$ar[$i]['csr_dm_count']=$row->csr_dm_count;
				$ar[$i]['csr_status']=$row->csr_status;
				$ar[$i]['csr_dm_piece']=$row->csr_dm_piece;
				$ar[$i]['src_name']=$row->src_name;
				$ar[$i]['src_type']=$row->src_type;
				$ar[$i]['src_rating']=$row->src_rating;
				$ar[$i]['src_quota']=$row->src_quota;
				$ar[$i]['src_estprice']=$row->src_estprice;
				$ar[$i]['src_monthly']=$row->src_monthly;
				$ar[$i]['src_mp_descr']=$row->src_mp_descr;
				$ar[$i]['src_hack']=$row->src_hack;
				$ar[$i]['csr_dm_ama']=$row->csr_dm_ama;
				$ar[$i]['csr_dm_postage']=$row->csr_dm_postage;
				$i+=1;					
			}	
			}
			return $ar;
    }
	
	public function getContacts($ctr_id,$id) {        
            	
			$ar = array();
			if($id!=""&&$id>0)
			{		
			$result = $this->adapter->query("select ctct_id,ctct_name,ctct_title from lstcontacts where (ctct_backref = ? and ctct_type = 4) or (ctct_backref = ? and ctct_type = 5) order by ctct_name", array($ctr_id,$id));
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['ctct_id']=$row->ctct_id;				
				$ar[$i]['ctct_name']=$row->ctct_name;
				$ar[$i]['ctct_title']=$row->ctct_title;							
				$i+=1;				
			}		
				
			}
			return $ar;
    }
	
	public function getSource($id) {        
            	
			$ar = array();
			if($id!=""&&$id>0)
			{		
			$result = $this->adapter->query("select * from vsource where src_id = ?", array($id));
			$i=0;
			foreach ($result as $row) 
			{			
				$ar['src_id']=$row->src_id;				
				$ar['srt_name']=$row->srt_name;
				$ar['src_ctct_id']=$row->src_ctct_id;							
				$ar['src_name']=$row->src_name;	
				$ar['src_sp_code']=$row->src_sp_code;
				$ar['src_price']=$row->src_price;	
				$ar['src_pricing']=$row->src_pricing;	
				$ar['src_type']=$row->src_type;	
				$ar['src_rating']=$row->src_rating;	
				$ar['ctct_name']=$row->ctct_name;	
				$ar['ctct_title']=$row->ctct_title;	
				$ar['ctct_company']=$row->ctct_company;	
				$ar['ctct_phone']=$row->ctct_phone;	
				$ar['ctct_ext1']=$row->ctct_ext1;	
				$ar['ctct_ext2']=$row->ctct_ext2;	
				$ar['ctct_ext3']=$row->ctct_ext3;	
				$ar['ctct_email']=$row->ctct_email;	
				$ar['ctct_addr_1']=$row->ctct_addr_1;	
				$ar['ctct_addr_2']=$row->ctct_addr_2;	
				$ar['ctct_addr_c']=$row->ctct_addr_c;	
				$ar['ctct_addr_z']=$row->ctct_addr_z;	
				$ar['ctct_st_code']=$row->ctct_st_code;	
				$ar['ctct_url']=$row->ctct_url;
				$ar['ctct_type']=$row->ctct_type;	
				$ar['ctct_hphone']=$row->ctct_hphone;	
				$ar['ctct_hfax']=$row->ctct_hfax;	
				$ar['src_quota']=$row->src_quota;	
				$ar['src_proposal']=$row->src_proposal;	
				$ar['src_target']=$row->src_target;	
				$ar['src_webuser']=$row->src_webuser;	
				$ar['src_webpass']=$row->src_webpass;	
				$ar['src_webcv']=$row->src_webcv;	
				$ar['src_circulation']=$row->src_circulation;	
				$ar['src_published']=$row->src_published;	
				$ar['src_mp_descr']=$row->src_mp_descr;	
				$ar['src_estprice']=$row->src_estprice;	
				$ar['src_monthly']=$row->src_monthly;				
			}		
				
			}
			return $ar;
    }
	
	//new get source comments
	public function getSourceComments($src_id) {         
            	
			
			$result = $this->adapter->query('select* from allnotes where note_type = 11 and note_ref_id = ?  order by note_dt desc',
            array($src_id));
			
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
	
	public function editSource($post)	
	{		
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		if(isset($post["src_id"]))
		{
			$specstr = implode(",",$post["spec"]);
			$result = $this->adapter->query('call EditSource (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
			array( $post["src_id"],$post["srcname"],
			$post["rating"],
			$post["price"],
			$post["pricing"],
			$specstr,
			$post["srctype"],
			$post["lfname"],
			$post["titul"],
			$post["comp"],
			$post["phone"],
			$post["phext"],
			$post["fax"],
			$post["fext"],
			$post["phone2"],
			$post["phext2"],
			$post["fax2"],
			$post["email"],
			$post["addr1"],
			$post["addr2"],
			$post["city"],
			$post["zip"],
			$post["state"],
			$post["url"],
			$note_user,
			'NOW()',
			$post["quota"],
			$post["propos"],
			$post["target"],
			$post["wuser"],
			$post["wpass"],
			$post["webcv"],
			$post["circa"],
			$post["publi"],
			$post["mpdesc"],
			$post["presto"],
			$post["monthly"]
			));
			
 
		}
		return true;
				
	}
	public function addSource($post)	
	{		
		$userid = $_COOKIE["phguid"];
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
				$emp_name=$row->emp_realname;
			}
		}
		
			$specstr = implode(",",$post["spec"]);
			$result = $this->adapter->query('call AddASource (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
			array( $post["srcname"],
			$post["rating"],
			$post["price"],
			$post["pricing"],
			$specstr,
			$post["srctype"],
			$post["lfname"],
			$post["titul"],
			$post["comp"],
			$post["phone"],
			$post["phext"],
			$post["fax"],
			$post["fext"],
			$post["phone2"],
			$post["phext2"],
			$post["fax2"],
			$post["email"],
			$post["addr1"],
			$post["addr2"],
			$post["city"],
			$post["zip"],
			$post["state"],
			$post["url"],
			$note_user,
			'NOW()',
			$post["quota"],
			$post["propos"],
			$post["target"],
			$post["wuser"],
			$post["wpass"],
			$post["webcv"],
			$post["circa"],
			$post["publi"],
			$post["mpdesc"],
			$post["presto"],
			$post["monthly"]
			));	
			if($result)
			{			
				foreach ($result as $row) {
					$id=$row->id;
				}
			}
			else { $id=false; }
			/*$result = $this->adapter->query('select 2 as id ',array( '123'));
			if($result)
			{			$id=1;
				foreach ($result as $row) {
					$id=$row->id;
				}
			}*/
		
		return $id;
				
	}
	
	public function getContracts($nurse=0) {        
            	
			$ar = array();
			$qry = "select ctr_id, ctr_no, ctr_spec, ctr_nu_type, ctr_location_c, ctr_location_s, ctr_nurse, ctr_type from allcontracts  where ctr_status = 1 ";
				
			if($nurse==1)
				$qry.= " and (ctr_nurse = 1 or ctr_cli_id=6) ";
			$qry.= " order by ctr_no";
			
			$result = $this->adapter->query($qry, array());
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['ctr_id']=$row->ctr_id;				
				$ar[$i]['ctr_no']=$row->ctr_no;
				$ar[$i]['ctr_spec']=$row->ctr_spec;	
				$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;	
				$ar[$i]['ctr_location_c']=$row->ctr_location_c;	
				$ar[$i]['ctr_location_s']=$row->ctr_location_s;	
				$ar[$i]['ctr_nurse']=$row->ctr_nurse;	
				$ar[$i]['ctr_type']=$row->ctr_type;					
				$i+=1;				
			}		
				
			
			return $ar;
    }
	
	public function getMyContracts($nurse=0) {        
			$userid = $_COOKIE["phguid"];			
			$ar = array();
			//$userid = 24;
			
			$qry = "select ctr_id, ctr_no, ctr_spec, ctr_nu_type, ctr_location_c, ctr_location_s, ctr_nurse, ctr_type from allcontracts  where (ctr_status in (1,16) and (ctr_recruiter = ".$userid." OR ctr_manager = ".$userid.") ) ";
			//$qry = "select ctr_id, ctr_no, ctr_spec, ctr_nu_type, ctr_location_c, ctr_location_s, ctr_nurse, ctr_type from allcontracts  where ctr_status = 1 ";
				
			if($nurse==1)
				$qry.= " and (ctr_nurse = 1 or ctr_cli_id=6) ";
			$qry.= " order by ctr_no";
			
			$result = $this->adapter->query($qry, array());
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['ctr_id']=$row->ctr_id;				
				$ar[$i]['ctr_no']=$row->ctr_no;
				$ar[$i]['ctr_spec']=$row->ctr_spec;	
				$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;	
				$ar[$i]['ctr_location_c']=$row->ctr_location_c;	
				$ar[$i]['ctr_location_s']=$row->ctr_location_s;	
				$ar[$i]['ctr_nurse']=$row->ctr_nurse;	
				$ar[$i]['ctr_type']=$row->ctr_type;					
				$i+=1;				
			}		
				
			
			return $ar;
    }
	
	public function getAssessmentContracts($ph_id, $nurse=0) {        
            	
			$ar = array();
			$qry = "select * from vctrassesslist where as_nurse = ? and as_ph_id = ?";
				
						
			$result = $this->adapter->query($qry, array($nurse, $ph_id));
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['ctr_id']=$row->ctr_id;				
				$ar[$i]['ctr_no']=$row->ctr_no;
				$ar[$i]['ctr_spec']=$row->ctr_spec;	
				//$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;	
				$ar[$i]['ctr_location_c']=$row->ctr_location_c;	
				$ar[$i]['ctr_location_s']=$row->ctr_location_s;	
				$ar[$i]['as_ph_id']=$row->as_ph_id;	
				$ar[$i]['as_nurse']=$row->as_nurse;		
				$ar[$i]['ctr_nurse']=$row->ctr_nurse;				
				$i+=1;				
			}		
				
			
			return $ar;
    }
	
	public function getLocumsContracts($nurse=0) {        
            	
			$ar = array();
			$qry = "select ctr_id, ctr_no, ctr_spec, ctr_nu_type, ctr_location_c, ctr_location_s, ctr_nurse, ctr_type from allcontracts  where ctr_status = 1 and ctr_no LIKE '%LT' ";
				
			if($nurse==1)
				$qry.= " and ctr_nurse = 1 ";
			$qry.= " order by ctr_no";
			
			$result = $this->adapter->query($qry, array());
			$i=0;
			foreach ($result as $row) 
			{			
				$ar[$i]['ctr_id']=$row->ctr_id;				
				$ar[$i]['ctr_no']=$row->ctr_no;
				$ar[$i]['ctr_spec']=$row->ctr_spec;	
				$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;	
				$ar[$i]['ctr_location_c']=$row->ctr_location_c;	
				$ar[$i]['ctr_location_s']=$row->ctr_location_s;	
				$ar[$i]['ctr_nurse']=$row->ctr_nurse;	
				$ar[$i]['ctr_type']=$row->ctr_type;					
				$i+=1;				
			}		
				
			
			return $ar;
    }
	
	/////FUNCTIONS MOVED FROM MIDLEVELS TABLE
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
			$message = "This is an automatically generated message\n\nA Change Contract Status Request has been placed by ".$username." for contract #".$post["ctr_no"].".\r\n";
			$message .= "Reason: ".$reason."\r\n";
			$message .= "Please click on the link below to review the changes requested:\n\n";
			$message .= "http://testdb.phg.com/public/contract/approvectrchange?chg_id=".$chg_id."&pipl_id=".$pipl."&ctr_id=".$id."&type=".$type."\n\n";

			//echo $subject;
			/*mail send*/
			mail("mbroxterman@phg.com", $subject, $message, $headers); //add john to this
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
			$reason=$post["chg_reason"];
			$comments=$post["comments"];
			$spec=$post["chg_spec"];
			$req=$post["chg_req"];
			if($req=='' || $req==null)
				$req=31; //house
			$pipl=$post["chg_pipl"];
			$chg_nut=$post["chg_nut"];
			$client=$post["client"];
			
			
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

			if($id>0 && $id!='')
			{
				//send email
				/*Setting the header part, this is important */
			$headers = "From:info@phg.com\n";
			$headers .= "MIME-Version: 1.0\n";	
			$headers .= "CC:".$useremail.",allusers@phg.com\n";
			$headers .= "BCC:nturner@phg.com\n";

			/*mail content , attaching the ics detail in the mail as content*/
			$subject = "DB: Contract ".$ctr_no." Decision ";
			$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
			$message = "A Decision for " . $chits[$type] . " has been made:\n".$decision."\n\n";
			$message .= "Reason: ".$reason."\n\n";
			$message .= "Comments: ".$comments."\n\n";
			$message .= "Client: ".$client."\n\n";
			$message .= "Please click on the link below to review the decision:\n\n";
			$message .= "http://testdb.phg.com/public/contract/ctrchange4?chg_id=".$id."\n\n";

			//echo $subject;
			/*mail send*/
			mail("mbroxterman@phg.com", $subject, $message, $headers); //add john to this
			}
			
				
			return true;
    }
	
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
	
	public function getPreProfile($ctrid, $contid) {           
			
			if($contid!=0 && $contid!='')
				$contstr = " and cont_id = ".$contid;
			else 
				$contstr = "";
				
			$result = $this->adapter->query('SELECT * FROM vctrpreprofilefrm WHERE ctr_id=? '.$contstr,
            array($ctrid));
			
			$ar = array();
			if($result)
			{
			//$i=0;
				foreach ($result as $row) {
					$ar['ctr_id']=$row->ctr_id;				
					$ar['ctr_no']=$row->ctr_no;
					$ar['ctr_pro_date']=$row->ctr_pro_date;
					$ar['sp_name']=$row->sp_name;
					$ar['ctct_name']=$row->ctct_name;
					
					$ar['addr1']=$row->addr1;
					$ar['addr2']=$row->addr2;
					$ar['city']=$row->city;
					$ar['zip']=$row->zip;
					$ar['stat']=$row->stat;
					$ar['cont_id']=$row->cont_id;					
					$ar['cont_name']=$row->cont_name;
					$ar['cont_title']=$row->cont_title;
					$ar['req_name']=$row->req_name;
					$ar['req_title']=$row->req_title;
					$ar['pro_practice']=$row->pro_practice;
					$ar['pro_commun']=$row->pro_commun;
					$ar['ctr_nurse']=$row->ctr_nurse;
					$ar['ctr_nu_type']=$row->ctr_nu_type;
					$ar['at_name']=$row->at_name;
					
					
				}
			}
			
			return $ar;
    }
	
	public function endSourcing($ctrid) {        
			$username = urldecode($_COOKIE["username"]);
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			
				//SourcingEnd`( ctr decimal, dt datetime, user char(32), automat int )
			if($ctrid!=0 && $ctrid!="" && $username!=""){	
				$result = $this->adapter->query('call SourcingEnd (?,NOW(),?,1)',  array($ctrid,$username));						
				if($result)
				{
					return true;
				}
			}
			else{ return false; }
	}
	
	//new get sources
	public function getApprovalSources($ctr_id) {        
            
			$ar = array();
			
			//echo "select *, FORMAT(csr_price, 2) AS csr_price2, DATE_FORMAT(csr_date_placed, '%m/%d/%Y') AS csr_date_placed2, FORMAT(csr_dm_postage, 2) AS csr_dm_postage2, FORMAT(csr_dm_ama, 2) AS csr_dm_ama2 from vsourceshedactive where csr_status = 0 and csr_ctr_id = $ctr_id order by src_type,csr_id";
			$result = $this->adapter->query("select *, FORMAT(csr_price, 2) AS csr_price2, DATE_FORMAT(csr_date_placed, '%m/%d/%Y') AS csr_date_placed2, FORMAT(csr_dm_postage, 2) AS csr_dm_postage2, FORMAT(csr_dm_ama, 2) AS csr_dm_ama2 from vsourceshedactive where csr_status = 0 and csr_ctr_id = ? order by src_type,csr_id", array($ctr_id));			
			$i=0;
			foreach ($result as $row) 
			{
			//$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['csr_id']=$row->csr_id;
				$ar[$i]['src_id']=$row->src_id;
				$ar[$i]['csr_ctr_id']=$row->csr_ctr_id;
				$ar[$i]['csr_appr_date']=$row->csr_appr_date;
				$ar[$i]['csr_term']=$row->csr_term;
				$ar[$i]['csr_price']=str_replace(',','',$row->csr_price2);
				$ar[$i]['csr_dm_count']=$row->csr_dm_count;
				$ar[$i]['csr_rating']=$row->csr_rating;
				$ar[$i]['csr_status']=$row->csr_status;
				$ar[$i]['csr_dm_code']=$row->csr_dm_code;
				$ar[$i]['csr_dm_piece']=$row->csr_dm_piece;
				$ar[$i]['src_name']=$row->src_name;
				$ar[$i]['src_type']=$row->src_type;
				$ar[$i]['src_rating']=$row->src_rating;
				$ar[$i]['src_quota']=$row->src_quota;
				$ar[$i]['src_estprice']=$row->src_estprice;
				$ar[$i]['csr_dm_ama']=$row->csr_dm_ama2;
				$ar[$i]['csr_startyear']=$row->csr_startyear;
				$ar[$i]['csr_schedule']=$row->csr_schedule;
				$ar[$i]['csr_note']=$row->csr_note;
				$ar[$i]['csr_date_placed']=$row->csr_date_placed2;
				$ar[$i]['csr_dm_postage']=$row->csr_dm_postage2;
				$i+=1;			
			}		
			//$ar["test"]=11;
			return $ar;
    }
	
	public function approveSubmitSourcing($post,$ctrid) {        
			$username = urldecode($_COOKIE["username"]);
			if(strpos($username, '@phg.com')!==false){
				$useremail = $username;
				$username = str_replace('@phg.com', '', $username);
			}
			else {
				$useremail = $username.'@phg.com';				
			}
			$success=true;
			$sht=0; //not sure what this is for
			//echo var_dump($post);
			foreach ($post as $key=>$val)
			{
				//echo $key."<br/>";
				if(strpos($key, 'da_')!==false){
					$csrid = str_replace('da_','',$key);
					$csrdate = $val;
					//echo "call SetSourceDate ($csrid,$ctrid,$sht,$csrdate,$username)";
				
					if($ctrid!=0 && $ctrid!="" && $username!="" && $csrid!="")
					{	
						//csr decimal, ctr decimal, sht tinyint, dt datetime, user
						$result = $this->adapter->query('call SetSourceDate (?,?,?,?,?)',  array($csrid,$ctrid,$sht,$csrdate,$username));						
						//echo "call SetSourceDate ($csrid,$ctrid,$sht,$csrdate,$username)<br/>";
						if($result)
						{
							//return true;
						}
						else{ $success=false; }
					}
					//else{ $success=false; }
				}//end if
			}//end for
				//exit();
			return $success;			
	}
	
}
