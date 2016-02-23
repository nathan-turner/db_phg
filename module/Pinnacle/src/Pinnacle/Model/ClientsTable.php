<?php
// module/Pinnacle/src/Pinnacle/Model/ClientsTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;
use Zend\Json\Json;

class ClientsTable extends Report\ReportTable
{

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vclientlookup',
            '`cli_id`, `ctct_name`, `ctct_addr_c`, `ctct_phone`, `ctct_st_code`, `st_name`, `cli_sys`, `cli_beds`, `cli_type`, `cli_status`, `cli_source`, `ctct_addr_z`, `city_state`, ctct_id, `cli_locumactive`'); // `ch_emp_id`, `ch_hot_prospect`, `ch_pending`, `ch_lead_1`, `ch_lead_2`, 
    }
    
    /**
     * @param array $ar     keys: see ClientsForm
    */
    public function buildQuery(array $ar = null) {
        if( is_array($ar) && $ar['emp_id'] ) {
            if( !$ar['cli_hotlist'] ) $this->table = 'vclientlookupsmall'; // eeeek!
            $where = new Where();
            $nduh = 0;
            if( $ar['cli_id'] ) { $where->equalTo('cli_id',$ar['cli_id']); return $where; }
            
            if( strlen(trim($ar['cli_name'])) >= 3 ) {
                $like = addslashes(trim($ar['cli_name']));
                $where->like('ctct_name',"%$like%"); $nduh++;
            }
            if( trim($ar['cli_city']) ) {
                $like = addslashes(trim($ar['cli_city']));
                $where->like('ctct_addr_c',"$like%"); $nduh++;
            }
            if( trim($ar['cli_zip']) ) {
                $like = addslashes(trim($ar['cli_zip']));
                $where->like('ctct_addr_z',"$like%"); $nduh++;
            }
            if( trim($ar['cli_sys']) ) {
                $like = addslashes(trim($ar['cli_sys']));
                $where->like('cli_sys',"$like%"); $nduh++;
            }
            if( $ar['cli_type'] ) { $where->equalTo('cli_type',$ar['cli_type']); $nduh++; }
            if( trim($ar['cli_phone']) ) {
                $like = addslashes(trim($ar['cli_phone']));
                $where->literal('cast(ctct_phone as varchar) like ?',array("$like%")); $nduh++;
            }
            if( $ar['cli_state'] ) { $where->equalTo('ctct_st_code',$ar['cli_state']); $nduh++; }
            if( $ar['cli_beds'] ) { $where->greaterThanOrEqualTo('cli_beds',$ar['cli_beds']); $nduh++; }
            if( $ar['cli_status'] >= 0 ) { $where->equalTo('cli_status',$ar['cli_status']); $nduh++; }
            elseif( $ar['cli_status'] == -2 ) { $where->in('cli_status',array(1,10)); $nduh++; }
            elseif( $ar['cli_status'] == -3 ) { $where->equalTo('cli_locumactive',1); $nduh++; }
			else { $where->notEqualTo('cli_status',12); $nduh++; }
            if( $ar['cli_source'] >= 0 ) { $where->equalTo('cli_source',$ar['cli_source']); $nduh++; }
            if( $ar['cli_hotlist'] ) {
                switch( $ar['cli_hotlist'] ) {
                    case 1: $where->equalTo('ch_hot_prospect',1); break;
                    case 2: $where->equalTo('ch_lead_1',1); break;
                    case 4: $where->equalTo('ch_lead_2',1); break;
                    case 8: $where->equalTo('ch_pending',1); break;
                }
                $where->equalTo('ch_emp_id',$ar['emp_id']); $nduh++;
            }
           
            return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
    }

    public function fetchAll($id = 0, array $ar = null) {
        $where = $this->buildQuery($ar);
        if( $where ) {
            $select = new Select($this->table);
            $select->where($where);
            $select->order('primary_record desc, city_state, ctct_name');
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
    
    public function saveResortPages($uid,$lid,array $ar = null) {
        $where = $this->buildQuery($ar);
        $uid = (int) $uid; $lid = (int) $lid;
        if( $where && $uid && $lid ) {
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->from($this->table);
            $select->where($where);
            $select->columns(array('*'), false);
            $str = @$sql->getSqlStringForSqlObject($select);
            $param = "INSERT IGNORE INTO custlistsus (owneruid,listid,ctype,memberuid) SELECT $uid,$lid,2,ctct_id ";
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
            $ar['cliurl'] = $urls->fromRoute('client', array('action'=>'view', 'part'=>'go', 'id' => $row->cli_id));
            if( !$row->city_state ) $row->city_state = ' ';

            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }
	
	//new get client data
	public function selectClient($id = 0, array $ar = null) {   
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
			
			$this->table='vclient';			
			$select = new Select($this->table);
			$select
			//->from(array('c'=>'vclient'))
			//->where('cli_id = ?',$id);
			->where->equalTo('cli_id',$id);
			$resultSet = $this->selectWith($select);
			
			if($resultSet)
			{
				foreach ($resultSet as $row) {
					$ar['cli_id']=$id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ctct_company']=$row->ctct_company;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$row->ctct_fax;
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_email']=$row->ctct_email;
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_url']=$row->ctct_url;
					$ar['cli_sys']=$row->cli_sys;
					$ar['cli_beds']=$row->cli_beds;
					$ar['cli_grp']=$row->cli_grp;
					$ar['st_name']=$row->st_name;
					$ar['cli_status']=$row->cli_status;
					$ar['cli_emp_id']=(string)$row->cli_emp_id;
					$ar['cli_rating']=$row->cli_rating;
					$ar['cs_name']=$row->cs_name;
					$ar['cli_population']=$row->cli_population;
					$ar['cli_specialty']=$row->cli_specialty;
					$ar['cli_step1']=$row->cli_step1;
					$ar['cli_locumactive']=$row->cli_locumactive;
					$ar['cli_fuzion']=$row->cli_fuzion;
					//$ar['cli_']=$row->cli_;cli_ctct_id
					$ar['cli_ctct_id']=$row->cli_ctct_id;
					
					$ar['cli_city']=$row->ctct_addr_c;
					$ar['fu_status']=$row->fu_status;
					$ar['fu_client']=$row->fu_client;
					$ar['fu_date']=$row->fu_date;
					$ar['fu_renewal']=$row->fu_renewal;
					$ar['primary_record']=$row->primary_record;
				}
			}
			//if($id!=0)
			//$ar['cli_city']=$id;
			//$sql = $select->toString();
			//echo "$sql\n";
            /*$select->where($where);
            $select->order('city_state, ctct_name');
            $limit = (int) $ar['pg_size']; if( !$limit || $limit > 200 ) $limit = 25;
            $select->limit($limit);
            $id--; if( $id < 0 ) $id = 0;
            $offset = $id * $limit;
            $select->offset($offset);
            $resultSet = $this->selectWith($select);
            return $resultSet;        */
			return $ar;
    }
	
	//new get client data
	public function selectEditClient($id = 0, array $ar = null) {         
            
			$this->table='veditclient';			
			$select = new Select($this->table);
			$select
			//->from(array('c'=>'vclient'))
			//->where('cli_id = ?',$id);
			->where->equalTo('cli_id',$id);
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
				foreach ($resultSet as $row) {
					$ar['cli_id']=$id;
					$ar['cli_xid']=$row->cli_xid;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ctct_company']=$row->ctct_company;
					$ar['ctct_phone']=$row->ctct_phone;
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$row->ctct_fax;
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_email']=$row->ctct_email;
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_url']=$row->ctct_url;
					$ar['cli_sys']=$row->cli_sys;
					$ar['cli_source']=$row->cli_source;
					$ar['cli_beds']=$row->cli_beds;
					$ar['cli_grp']=$row->cli_grp;
					$ar['cli_type']=$row->cli_type;
					$ar['st_name']=$row->st_name;
					$ar['cli_status']=$row->cli_status;
					$ar['cli_emp_id']=(string)$row->cli_emp_id;
					$ar['cli_rating']=$row->cli_rating;
					$ar['cs_name']=$row->cs_name;
					$ar['cli_population']=$row->cli_population;
					$ar['cli_specialty']=$row->cli_specialty;
					$ar['cli_step1']=$row->cli_step1;
					$ar['cli_locumactive']=$row->cli_locumactive;
					$ar['cli_specnote']=$row->cli_specnote;
					$ar['cli_fed_tax']=$row->cli_fed_tax;
					$ar['cli_fuzion']=$row->cli_fuzion;
					$ar['cli_rating']=$row->cli_rating;
					//$ar['cli_']=$row->cli_;
					
					
					$ar['cli_city']=$row->ctct_addr_c;
					
				}
			}
			
			return $ar;
    }
	
	//save edit client
	public function saveClient(Array $client, $id, $identity) {
	
	$id  = (int) $id;       
		 //echo $_COOKIE["phguid"];
		 /*echo 'Call EditAClient ('.$id.', '.$client["ctct_name"].', '.$client["ctct_title"].', 
			'.$client["ctct_company"].', '.$client["ctct_phone"].',  '.$client["ctct_ext1"].', '.$client["ctct_fax"].',  '.$client["ctct_ext2"].',
			'.$client["ctct_email"].', '.$client["ctct_addr_1"].',  '.$client["ctct_addr_2"].',  '.$client["ctct_addr_c"].', '.$client["ctct_addr_z"].', '.$client["ctct_st_code"].',
			'.$client["ctct_url"].', '.$client["cli_xid"].', '.$client["cli_sys"].', '.$client["cli_beds"].', '.$client["cli_type"].', '.$client["cli_emp_id"].', '.$_COOKIE["phguid"].',  
			'.$client["ctobe"].', '.$client["cli_source"].', '.$client["cli_population"].', '.$client["cli_specialty"].', '.$client["cli_fuzion"].', 
			'.$client["cli_specnote"].' , '.$client["cli_locumactive"].', '.$client["cli_fed_tax"].', now())';*/
        // prepare sql
        $result = $this->adapter->query('Call EditAClient (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())',
            array($id, $client["ctct_name"], $client["ctct_title"], 
			$client["ctct_company"], $client["ctct_phone"],  $client["ctct_ext1"], $client["ctct_fax"],  $client["ctct_ext2"],
			$client["ctct_email"], $client["ctct_addr_1"],  $client["ctct_addr_2"],  $client["ctct_addr_c"], $client["ctct_addr_z"], $client["ctct_st_code"],
			$client["ctct_url"], $client["cli_xid"], $client["cli_sys"], $client["cli_beds"], $client["cli_status"], $client["cli_type"], $client["cli_emp_id"], $_COOKIE["phguid"],  
			$client["ctobe"], $client["cli_source"], $client["cli_population"], $client["cli_specialty"], $client["cli_fuzion"], 
			$client["cli_specnote"] , $client["cli_locumactive"], $client["cli_fed_tax"]));
/*		
(id, name0, title, company, phone,  ext1, fax,  ext2,
email, addr_1,  addr_2,   addr_c, addr_z,  st_code,
url, xid, sys, beds, grp, emp_id, user_mod,  date_mod, todo, source0,
popu, spec, fuz, snot , locum, fedtax)*/
		//echo $client['ctct_name'];
		
        return $result; 
    }
	
	//add client
	public function addClient(Array $client, $identity) {	
		 $outid=0;
		 $userid = $_COOKIE["phguid"];
        // prepare sql
		if($userid!="" && $userid>0)
		{
        $result = $this->adapter->query('CALL AddAClient (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())',
            array($client["ctct_name"], $client["ctct_title"], 
			$client["ctct_company"], $client["ctct_phone"],  $client["ctct_ext1"], $client["ctct_fax"],  $client["ctct_ext2"],
			$client["ctct_email"], $client["ctct_addr_1"],  $client["ctct_addr_2"],  $client["ctct_addr_c"], $client["ctct_addr_z"], $client["ctct_st_code"],
			$client["ctct_url"], $client["cli_xid"], $client["cli_sys"], $client["cli_beds"], $client["cli_type"], $client["cli_emp_id"],   
			1, $userid));
			/*$result = $this->adapter->query('CALL AddAClient2 (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, now())',
            array($client["ctct_name"],
			$client["ctct_title"],
			$client["ctct_company"],
			$client["ctct_phone"],  $client["ctct_ext1"], $client["ctct_fax"],  $client["ctct_ext2"],
			$client["ctct_email"], $client["ctct_addr_1"],  $client["ctct_addr_2"],  $client["ctct_addr_c"], $client["ctct_addr_z"], $client["ctct_st_code"],
			$client["ctct_url"], $client["cli_xid"], $client["cli_sys"],
			$client["cli_beds"], $client["cli_type"],
			$client["cli_emp_id"]
			));*/
			//echo var_dump($result);
		}
		$userid=0;
		foreach ($result as $row) 
			{				
				$id=$row["id"];
			}
		//$result = $this->adapter->query('select @out');
/*		
(id, name0, title, company, phone,  ext1, fax,  ext2,
email, addr_1,  addr_2,   addr_c, addr_z,  st_code,
url, xid, sys, beds, grp, emp_id, user_mod,  date_mod, todo, source0,
popu, spec, fuz, snot , locum, fedtax)*/
		//echo $client['ctct_name'];
		
        return $id; 
    }
	
	//get marketers for drop down
	public function getMarketers(){
		
		$ar = array();
		
		$result = $this->adapter->query("select * from vemplist WHERE emp_dept LIKE '%M%'  order by ctct_name")->execute();
		
		foreach ($result as $row) 
			{
				$ar[$row['emp_id']]=$row['ctct_name'];
			}
		
		return $ar;
	}
	
	//get marketers for drop down
	public function getLocumsMarketers(){
		
		$ar = array();
		
		$result = $this->adapter->query("select * from vemplist WHERE emp_dept LIKE '%M%' OR emp_id=234 OR emp_id=240 order by ctct_name")->execute();
		
		foreach ($result as $row) 
			{
				$ar[$row['emp_id']]=$row['ctct_name'];
			}
		
		return $ar;
	}
	
	//get types for drop down
	public function getTypes(){
		
		$ar = array();
		
		$result = $this->adapter->query("select * from dctclienttypes order by ct_name")->execute();
		
		foreach ($result as $row) 
			{
				$ar[$row['ct_id']]=$row['ct_name'];
			}
		
		return $ar;
	}
	
	//new get client contacts
	public function selectClientContacts($id = 0, array $ar = null) {         
            
			$dept = $_COOKIE["dept"];
			
			$this->table='lstcontacts';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('ctct_type',5)
			->where->equalTo('ctct_backref',$id);
			if (strpos($str, 'R') !== FALSE)
				$select->order('ctct_recruiting DESC, ctct_secondary DESC, ctct_name DESC');
			elseif (strpos($str, 'M') !== FALSE)
				$select->order('ctct_marketing DESC, ctct_secondary DESC, ctct_name DESC');
			elseif (strpos($str, 'L') !== FALSE)
				$select->order('ctct_locum1 DESC, ctct_locum2 DESC, ctct_name DESC');
			elseif (strpos($str, 'F') !== FALSE)
				$select->order('ctct_fuzion1 DESC, ctct_fuzion2 DESC, ctct_name DESC');
			else
				$select->order('ctct_marketing DESC, ctct_recruiting DESC, ctct_fuzion1 DESC, ctct_name DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['cli_id']=$id;
					$ar[$i]['ctct_id']=$row->ctct_id;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_title']=$row->ctct_title;
					$ar[$i]['ctct_company']=$row->ctct_company;
					$ar[$i]['ctct_phone']=$row->ctct_phone;
					$ar[$i]['ctct_ext1']=$row->ctct_ext1;
					$ar[$i]['ctct_fax']=$row->ctct_fax;
					$ar[$i]['ctct_ext2']=$row->ctct_ext2;
					$ar[$i]['ctct_email']=$row->ctct_email;
					$ar[$i]['ctct_addr_1']=$row->ctct_addr_1;
					$ar[$i]['ctct_addr_2']=$row->ctct_addr_2;
					$ar[$i]['ctct_addr_c']=$row->ctct_addr_c;
					$ar[$i]['ctct_addr_z']=$row->ctct_addr_z;
					$ar[$i]['ctct_st_code']=$row->ctct_st_code;
					$ar[$i]['ctct_url']=$row->ctct_url;					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get contact statuses
	public function getContactStatus($id = 0) {         
            
			$this->table='dctstatus';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('st_contact',1);
			
			//$select->order('ctct_name DESC');
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
	//new get contact statuses
	public function getStatusList($id = 0) {         
            
			$this->table='dctstatus';			
			$select = new Select($this->table);
			/*$select			
			->where->equalTo('st_contact',1);*/
			
			//$select->order('ctct_name DESC');
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
	
	//function to format phone numbers
	public function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.'-'.$lastFour;
    }

		return $phoneNumber;
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
	
	
	//new get client contacts - for ajax
	public function selectContactDetails($id = 0) {         
            
			$this->table='lstcontacts';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('ctct_id',$id);
			
			//$select->order('ctct_name DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{			
            
			$i=0;
				foreach ($resultSet as $row) {
					$ar['cli_id']=$id;
					$ar['ctct_id']=$row->ctct_id;
					$ar['ctct_name']=$row->ctct_name;
					$ar['ctct_title']=$row->ctct_title;
					$ar['ctct_company']=$row->ctct_company;
					$ar['ctct_phone']=$this->formatPhoneNumber($row->ctct_phone);
					$ar['ctct_phone2']=$this->stripPhoneNumber($row->ctct_phone);
					$ar['ctct_ext1']=$row->ctct_ext1;
					$ar['ctct_fax']=$this->formatPhoneNumber($row->ctct_fax);
					$ar['ctct_ext2']=$row->ctct_ext2;
					$ar['ctct_cell']=$this->formatPhoneNumber($row->ctct_cell);
					$ar['ctct_cell2']=$this->stripPhoneNumber($row->ctct_cell);
					$ar['ctct_ext3']=$row->ctct_ext3;
					$ar['ctct_pager']=$row->ctct_pager;
					
					$ar['ctct_email']=$row->ctct_email;
					$ar['ctct_addr_1']=$row->ctct_addr_1;
					$ar['ctct_addr_2']=$row->ctct_addr_2;
					$ar['ctct_addr_c']=$row->ctct_addr_c;
					$ar['ctct_addr_z']=$row->ctct_addr_z;
					$ar['ctct_st_code']=$row->ctct_st_code;
					$ar['ctct_url']=$row->ctct_url;		
					$ar['ctct_reserved']=$row->ctct_reserved;
					$ar['ctct_reserved2']=$row->ctct_reserved2;	
					$ar['ctct_type']=$row->ctct_type;
					$ar['ctct_status']=$row->ctct_status;
					$ar['ctct_user_mod']=$row->ctct_user_mod;	
					$ar['ctct_date_mod']=$row->ctct_date_mod;	
					$ar['ctct_hphone']=$row->ctct_hphone;
					$ar['ctct_hfax']=$row->ctct_hfax;
					$ar['ctct_recruiting']=$row->ctct_recruiting;
					$ar['ctct_marketing']=$row->ctct_marketing;
					$ar['ctct_secondary']=$row->ctct_secondary;
					$ar['ctct_bounces']=$row->ctct_bounces;
					$ar['ctct_fuzion1']=$row->ctct_fuzion1;
					$ar['ctct_fuzion2']=$row->ctct_fuzion2;
					$ar['ctct_locum1']=$row->ctct_locum1;
					$ar['ctct_locum2']=$row->ctct_locum2;
					//below not used?
					$ar['ctct_unsub']=$row->ctct_unsub;
					$ar['ctct_unsub2']=$row->ctct_unsub2;
					$ar['ctct_unsub3']=$row->ctct_unsub3;
					//$ar['ctct_note']=$row->ctct_note;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	public function addContact($json) {         
            
			$this->table='lstcontacts';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('ctct_id',$id);
			
			//$select->order('ctct_name DESC');
			$resultSet = $this->selectWith($select);
			
	}
	
	//new add client contacts - for ajax
	public function addNewContact($post, $identity) {         
            
			$this->table='lstcontacts';	
			$post["contact_cell"] = str_replace(' ','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace(' ','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('—','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('–','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('-','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('.','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('-','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace(')','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('(','',$post["contact_cell"]);
			$post["contact_fax"] = str_replace('-','',$post["contact_fax"]);
			$post["contact_fax"] = str_replace(')','',$post["contact_fax"]);
			$post["contact_fax"] = str_replace('(','',$post["contact_fax"]);
			$post["contact_cell"] = $this->stripPhoneNumber($post["contact_cell"]);
			$post["contact_phone"] = $this->stripPhoneNumber($post["contact_phone"]);

			$result = $this->adapter->query('INSERT INTO lstcontacts (ctct_name, ctct_title, ctct_company, ctct_phone, 
			ctct_ext1,
			ctct_fax,
			ctct_ext2,
			ctct_cell,
			ctct_ext3,
			ctct_email,
			ctct_addr_1,
			ctct_addr_2,
			ctct_addr_c,
			ctct_addr_z,
			ctct_st_code,
			ctct_url,
			ctct_reserved2, 
			ctct_status,
			ctct_user_mod,			
			ctct_hphone,
			ctct_hfax,
			ctct_recruiting,
			ctct_marketing,
			ctct_secondary,
			ctct_fuzion1,
			ctct_fuzion2,
			ctct_locum1,
			ctct_locum2,
			ctct_backref, ctct_type, ctct_date_mod) 
			VALUES(?, ?, ?, ?, ?, 
			?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  
			5, NOW())',
            array(
                $post["contact_name"], $post["contact_title"], $post["contact_company"], $post["contact_phone"], 
				$post["contact_ext1"],
			$post["contact_fax"],
			$post["contact_ext2"],
			$post["contact_cell"],
			$post["contact_ext3"],
			$post["contact_email"],
			$post["contact_address"],
			$post["contact_address2"],
			$post["contact_city"],
			$post["contact_zip"],
			$post["contact_state"],
			$post["contact_website"],			
			$post["contact_note"],//			
			$post["contact_status"],
			$identity->username, //usermod			
			$post["contact_home_phone"],
			$post["contact_home_fax"],			
			$post["contact_recruiting_primary"],
			$post["contact_marketing_primary"],
			$post["contact_marketing_secondary"],						
			$post["contact_fuzion_primary"],
			$post["contact_fuzion_secondary"],
			$post["contact_locums_primary"],
			$post["contact_locums_secondary"],
				$post["backref"]                
        ));
							
			return true;
    }
	
	//new update contact - for ajax
	public function updateContact($post, $identity) {         
            
			$this->table='lstcontacts';	
			if(strpos($post["contact_website"],'http')===false)
				$post["contact_website"]="http://".$post["contact_website"];
			$post["contact_cell"] = str_replace(' ','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('—','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('–','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('-','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('.','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('-','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace(')','',$post["contact_cell"]);
			$post["contact_cell"] = str_replace('(','',$post["contact_cell"]);
			$post["contact_fax"] = str_replace('-','',$post["contact_fax"]);
			$post["contact_fax"] = str_replace(')','',$post["contact_fax"]);
			$post["contact_fax"] = str_replace('(','',$post["contact_fax"]);
			
			$post["contact_cell"] = $this->stripPhoneNumber($post["contact_cell"]);
			$post["contact_phone"] = $this->stripPhoneNumber($post["contact_phone"]);
			
			$result = $this->adapter->query('UPDATE lstcontacts SET
			ctct_name=?, 
			ctct_title=?, 
			ctct_company=?, 
			ctct_phone=?, 
			ctct_ext1=?,
			ctct_fax=?,
			ctct_ext2=?,
			ctct_cell=?,
			ctct_ext3=?,
			ctct_email=?,
			ctct_addr_1=?,
			ctct_addr_2=?,
			ctct_addr_c=?,
			ctct_addr_z=?,
			ctct_st_code=?,
			ctct_url=?,
			ctct_reserved2=?, 
			ctct_status=?,
			ctct_user_mod=?,			
			ctct_hphone=?,
			ctct_pager=?,
			ctct_hfax=?,
			ctct_recruiting=?,
			ctct_marketing=?,
			ctct_secondary=?,
			ctct_fuzion1=?,
			ctct_fuzion2=?,
			ctct_locum1=?,
			ctct_locum2=?,
			ctct_backref=?, 			
			ctct_date_mod=NOW() 
			WHERE ctct_id= ?
			',
            array(
                $post["contact_name"], 
				$post["contact_title"], 
				$post["contact_company"], 
				$post["contact_phone"], 
				$post["contact_ext1"],
			$post["contact_fax"],
			$post["contact_ext2"],
			$post["contact_cell"],
			$post["contact_ext3"],
			$post["contact_email"],
			$post["contact_address"],
			$post["contact_address2"],
			$post["contact_city"],
			$post["contact_zip"],
			$post["contact_state"],
			$post["contact_website"],			
			$post["contact_note"],//			
			$post["contact_status"],
			$identity->username, //usermod			
			$post["contact_home_phone"],
			$post["contact_pager"],	
			$post["contact_home_fax"],			
			$post["contact_recruiting_primary"],
			$post["contact_marketing_primary"],
			$post["contact_marketing_secondary"],						
			$post["contact_fuzion_primary"],
			$post["contact_fuzion_secondary"],
			$post["contact_locums_primary"],
			$post["contact_locums_secondary"],
				$post["backref"],			$post["contact_id"]                 
        ));
				
			return true;
    }
	
	//new delete contact - for ajax
	public function deleteContact($post, $identity) {         
            
			$this->table='lstcontacts';	

			$result = $this->adapter->query('DELETE FROM lstcontacts 
			WHERE ctct_id= ? LIMIT 1',
            array(
                $post["contact_id"]                 
			));				
			
			return true;
    }

	//new get comments
	public function getComments($id = 0, array $ar = null) {         
            
			$this->table='allnotes';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('note_type',2)
			->where->equalTo('note_ref_id',$id);
			$select->order('note_dt DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['note_user']=$row->note_user;
					$ar[$i]['note_text']=$row->note_text;
					$ar[$i]['note_dt']=$row->note_dt;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new add comment - for ajax (also used by contracts)
	public function addNewComment($post, $identity) {         
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
			if($post["ctr_id"]=="")
				$ctr_id=0;
			else
				$ctr_id=$post["ctr_id"];
			$note_type = $post["note_type"];
			if($note_type=="")
				$note_type=2;
			if($post["ref_id"]!="" && $post["ref_id"]>0)	
				$id = $post["ref_id"];
			else
				$id = $post["id"];
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
			
			if($post["comments"]=="" && $post["commenttxt"]!='')
				$post["comments"]=$post["commenttxt"];
//adding 2 hours to date to compensate
			$result = $this->adapter->query('insert into allnotes (note_user,note_ref_id,note_type,note_emp_id,note_text,note_reserved,note_dept, note_dt) values (?,?,?,?,?,?,?, DATE_ADD(NOW(), INTERVAL 2 HOUR))'
			,
            array(
                $username, 
				$id, 
				$note_type, 
				$userid, 
				$post["comments"], 
				$ctr_id,
				$dept
				               
			));
			
			//$sendgrid = new SendGrid('mfollowell', 'Phg3356!');
				
			return true;
    }
	
	//new add comment - for ajax
	public function updateRating($post, $identity) {         
            $userid = $_COOKIE["phguid"];
			$username = $_COOKIE["username"];
			
			$this->table='lstclients';
			/*$select = new Select($this->table);
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

			$result = $this->adapter->query('update lstclients set cli_rating = ?, cli_user_mod = ?, cli_rat_date = now(), cli_date_mod = now() where cli_id=? LIMIT 1'
			,
            array(
                $post["rating"],
				$username,
				$post["id"]		
				               
			));
				
			return true;
    }
	
	//new get contracts
	public function getContracts($id = 0, array $ar = null) {         
            
			$this->table='vctr4clients';			
			$select = new Select($this->table);
			$where = new Where();
			//$select->where->equalTo('ctr_cli_id',$id);
			//$select->where->equalTo('ctr_cli_bill',$id);
			$where->equalTo('ctr_cli_id',$id);
			$where->equalTo('ctr_cli_bill',$id);
			$select->where($where);
			$select->order('st_name, ctr_date DESC');
			//$resultSet = $this->selectWith($select);
			$resultSet = $this->adapter->query('select * from vctr4clients where ctr_cli_id=? or ctr_cli_bill=? order by st_name, ctr_date DESC', array($id,$id));
			
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;
					$ar[$i]['ctr_type']=$row->ctr_type;
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['st_name']=$row->st_name;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get meetings
	public function getMeetings($id = 0, array $ar = null) {                      
			$this->table='vclimeetsmall';			
			$select = new Select($this->table);
			$select				
			->where->equalTo('cm_cli_id',$id);
			$select->order('cm_date DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['cm_id']=$row->cm_id;
					$ar[$i]['cm_date']=$row->cm_date;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['cm_user_mod']=$row->cm_user_mod;
					$ar[$i]['cm_date_mod']=$row->cm_date_mod;	
					$ar[$i]['cm_cancel']=$row->cm_cancel;
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new cancel meeting - for ajax -- USED??
	public function cancelMeeting($post, $identity) {         
            $userid = $_COOKIE["phguid"];
			$username = $_COOKIE["username"];
			
			if($userid!='' && $userid>0)
			{
			$this->table='lstclients';
			/*$select = new Select($this->table);
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
			$result = $this->adapter->query('update allactivities set aact_shortnote = ?, 
			aact_accepted = 1, aact_user_mod = ?
			where aact_ref1 = ? and aact_ref_type1 = 2 and aact_act_code in (6,7,9) 
			and aact_trg_dt=(SELECT cm_date FROM tCliMeetings WHERE cm_id=?)',
			array(
				"Canceled by ".$username,
                $username,
				$post["clientid"],					
				$post["id"]		
				               
			));

			$result = $this->adapter->query('update tclimeetings set cm_cancel = 1,cm_user_mod = ?, cm_date_mod = now() where cm_id = ?'
			,
            array(                
				$username,
				$post["id"]		
				               
			));
			}
				
			return true;
    }
	
	//new get activities
	public function getActivities($id = 0, array $ar = null) {         
            //sql = "select * from vActivityPerClient where aact_ref1 =  order by aact_trg_dt desc";
			$this->table='vactivityperclient';			
			$select = new Select($this->table);
			$select			
			->where->equalTo('aact_ref1',$id);			
			$select->order('aact_trg_dt DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['aact_trg_dt']=$row->aact_trg_dt;
					$ar[$i]['act_name']=$row->act_name;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['aact_shortnote']=$row->aact_shortnote;
					$ar[$i]['emp_uname']=$row->emp_uname;
					$ar[$i]['Expr1']=$row->Expr1;
					if($row->aact_accepted !=0)
						$ar[$i]['check']='x';
					else
						$ar[$i]['check']='';
						
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new add activity - for ajax
	public function scheduleActivity($post, $identity) {         
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
			
			$actdate = $post["actdate"];
			$timehr = $post["timehour"];
			$timemin = $post["timemin"];
			$timeampm = $post["timeampm"];
			
			/*if($timeampm=="PM")
				$timehr+=12;
			$timehr = sprintf("%02d", $timehr);
			
			$actdate = $actdate." ".$timehr.":".$timemin.":00";*/
			
			$actdate = $actdate." ".$timehr.":".$timemin.":00";
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$post["a_user"]);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					//$note_user=$row->emp_user_mod;
					//$dept=$row->emp_dept;		
					$to_email = $row->emp_uname;	
				}
			}

			$result = $this->adapter->query('CALL AddAnActivity (?,?,?,?,?,?,?,?,?)'
			,
            array(
                $username, 
				$post["a_activity"], 
				$userid,
				$post["a_user"], 
				$actdate, 
				$post["id"]	, //actref
				2, //reftype
				$post["act_notes"], 
				$post["cli_ctct_id"]               
			));
			
					$hour = sprintf('%02d', ltrim($post["timehour"],'0')+5); //fix 3 hours behind...
			$startdate = str_replace("-","",$post["actdate"])."T".$hour.$post["timemin"]."00Z";
			//echo $startdate;
	$post["act_notes"].= "\r\n http://testdb.phg.com/public/client/view/".$post["id"]." \r\n\r\n";
	
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
DESCRIPTION: ".$post["act_notes"]." 
LOCATION: US
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:".$post["a_activity"]." Scheduled
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
	
	
	//new pass to locum - for ajax
	public function passToLocums($post, $identity) {         
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
			
			$v = "<em>Automatic Note:</em>Client pass to locums by <b>".$emp_name."</b>";
			array_walk($post, create_function('&$val', '$val = urldecode($val);')); //decode post values
			
			
			$result = $this->adapter->query('insert into allnotes (note_user, note_ref_id,note_type,note_emp_id,note_text,note_reserved,note_update, note_dt)
			 values (?,?,?,?,?,?,?,NOW())'
			,
            array(    
				$username,				
				$post["id"], 				
				2,
				$userid,
				$v,
				'NULL',
				0
			));
			
			$result = $this->adapter->query('insert into tvistapasses (vp_type, vp_ref_id, vp_emp_id, vp_date) values (2,?,?, NOW())'
			,
            array(                
				$post["id"], 				
				$userid				     
			));
			
			$message =
			"Client Lead Pass - PHG to Pinnacle Locums\n\n
Pinnacle contact name: ".$emp_name."\r\n
Best time to call to discuss: ".$post["loc_emptime"]."\n\n
Step 1\r\n
	Client Name: ".$cli_name." \r\n
	Address 1: ".$post["loc_cliaddr1"]." \r\n
	Address 2: ".$post["loc_cliaddr2"]." \r\n
	City, State, Zip: ".$post["loc_cliaddr3"]." \r\n
	Jobsite location: ".$post["loc_location"]." \r\n
	Contact Name: ".$post["loc_contact"]." \r\n
	Contact Title: ".$post["loc_title"]." \r\n
	Phone Number: ".$post["loc_phone"]." \r\n
	Email Address: ".$post["loc_email"]." \r\n
Step 2 \r\n
	Specialty Needs: ".$post["loc_needs"]." \r\n
Step 3 \r\n
	Requested dates of coverage: ".$post["loc_coverage"]." \r\n
	Reason for coverage: ".$post["loc_reason"]." \r\n
	Practice details: ".$post["loc_details"]." \r\n
Step 4 \r\n
	Current PHG Client? ".$post["loc_current"]." \r\n
	If yes, go through PHG contact first? ".$post["loc_through"]." \r\n
Step 5 \r\n
	Urgency of Need: ".$post["loc_urgency"]." \r\n
Step 6 \r\n
	Other locum tenens groups used? ".$post["loc_other"]."\n\n
-- \r\n
This message was sent automatically. Please reply, if you have any questions.\n\n
REF_ID#".$post["id"]."";
		
$from = "postmaster@phg.com";	
$to = "locumpass@phg.com";	///CHANGE TO THIS WHEN LIVE/TESTING
//$to = "nturner@phg.com";
$subject = "Client Pass from PHG";
$headers = "From: ".$from."\r\n";
$headers .=  'X-Mailer: PHP/'.phpversion()."\r\n";
			
mail ($to, $subject, $message, $headers);
 			
			//return $post["loc_cliaddr3"];
			return true;
    }
	
	//new get activities drop down
	public function getActivityList() {         
            //sql = "select * from vActivityPerClient where aact_ref1 =  order by aact_trg_dt desc";
			$this->table='dctactivity';			
			$select = new Select($this->table);
			$select		
			->where->equalTo('act_need_ref',1)
			->where->equalTo('act_client',1)
			->where->equalTo('act_hidden',0);			
			//$select->order('act_trg_dt DESC');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['act_code']=$row->act_code;
					$ar[$i]['act_name']=$row->act_name;
					$ar[$i]['act_need_note']=$row->act_need_note;					
					
					$i+=1;
				}
			}			
			return $ar;
    }
	
	//new get activities
	public function getUsersList() {         
            //sql = ""select emp_id,ctct_name from vEmpList order by ctct_name"";
			$this->table='vemplist';			
			$select = new Select($this->table);
			/*$select					
			->where->equalTo('act_hidden',0);*/	
			$select->order('ctct_name');
			$resultSet = $this->selectWith($select);
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['emp_id']=$row->emp_id;
					$ar[$i]['ctct_name']=$row->ctct_name;									
					
					$i+=1;
				}
			}			
			return $ar;
    }
	
	//new add contract
	public function addContract($post) {         
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
			
			
			/*EXEC AddAContRact " . $Request->{'cli_id'}->Item .
        ",$cno,$rbill,$cdate,$cspec,$cstatus,$cmar" .
	",$creq,$camount,$cmont,0,$clocc,$clocs,'" .
	$Session->{"UserName"} . "','" . $ctme . "',$ctype,$crdate,$cnote,$nurse,$cnut*/
	
	/*PROCEDURE `AddAContRact`(cliid int, 
cno char(10),  cbill int,
cdate datetime, cspec char(3),
cstatus tinyint = 1, cmarketer int,
crecruiter int,  camount decimal(19,4),
cmonthly decimal(19,4), cguarantee int,
clocationc varchar(50), clocations char(2) //= '--',
cuser_mod char(32), cdate_mod datetime,
ctype char(2),

cretain datetime , csnote varchar(255)//= NULL,

nurse bit //= 0, 
nu_type char(10) */
		if($post["ctr_bill"]=="x" || $post["ctr_bill"]=='')
			$ctr_bill=NULL;
		else
			$ctr_bill=$post["ctr_bill"];
			
		if($post["ctr_type"]=="CP")
			$post["ctr_type"]="C";	//weird
		if($post["ctr_type"]=="LT")
			$post["ctr_retain"]=NULL; //set retain date to null if LT
		
		$contract["ctr_spec"] = str_replace('|','',trim($contract["ctr_spec"]));
		$post["ctr_spec"] = str_replace('&nbsp;','',trim($post["ctr_spec"]));
		$post["ctr_spec"] = str_replace(' ','',$post["ctr_spec"]);
			
		if($post["cli_id"]!='' && $post["ctr_no"]!='' && $userid!='')
		{		
			$result = $this->adapter->query('call AddAContRact (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?,?,?) '
			,
            array(    
				$post["cli_id"],
				$post["ctr_no"],
				$ctr_bill,
				$post["actdate"],
				$post["ctr_spec"],				
				$post["ctr_status"],
				$post["ctr_marketer"],
				$post["ctr_recruiter"],
				$post["ctr_amount"],
				$post["ctr_monthly"],
				0, //guarantee is 0 apparently
				$post["ctr_loc_c"],
				$post["ctr_loc_s"],
				$username,
				//'NOW()',
				$post["ctr_type"],
				$post["ctr_retain"],
				$post["ctr_snote"],
				$post["ctr_nurse"], //=0?
				$post["ctr_nu_type"]
			));
			return true;
			//echo $post["ctr_nurse"];
			
		}
		
		return false;
	}
	
	public function addFuzionContract($post) {         
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
		if($post["fu_acct"]!='' && $post["cli_id"]!='' && $post["fu_id"]=='0')
		{
			if($post["fu_nonew"]=='')
				$post["fu_nonew"]=0;
			$result = $this->adapter->query('call addAFuzion (?,?,?,?,?,?,?,?,?,?,?,?)'
			,
            array(     
				$post["cli_id"],
				$post["fu_acct"],
				$post["fu_date"],
				$post["fu_length"],
				$post["fu_renewal"],
				$post["fu_invoice"],
				$post["fu_status"],
				$post["fu_start"],
				$post["fu_amount"],
				$username,
				$post["fu_nonew"],
				$post["fu_note"]				               
			));
		}	
				
			return true;
    }
	
	public function editFuzionContract($post) {         
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
		if($post["fu_acct"]!='' && $post["fu_id"])
		{
			if($post["fu_nonew"]=='')
				$post["fu_nonew"]=0;
			$result = $this->adapter->query('update allfuzion set fu_acct=?, fu_date=?, fu_length=?, fu_renewal=?, fu_invoice=?, fu_status=?, fu_start=?, fu_amount=?, fu_usermod=?, fu_datemod=NOW(), fu_notes=?, fu_nonew=? where fu_id = ? LIMIT 1'
			,
            array(     				
				$post["fu_acct"],
				$post["fu_date"],
				$post["fu_length"],
				$post["fu_renewal"],
				$post["fu_invoice"],
				$post["fu_status"],
				$post["fu_start"],
				$post["fu_amount"],
				$username,
				$post["fu_notes"],
				$post["fu_nonew"],
				$post["fu_id"]				
			));
		}	
				
			return true;
    }
	
	public function getFuzionContract($id) {         
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

		$result = $this->adapter->query('select * from allfuzion where fu_id = ?'	,
        array(     				
			$id						
		));
		$ar = array();
		if($result)
		{
			$i=0;
				foreach ($result as $row) {
					$ar['fu_id']=$id;
					$ar['fu_acct']=$row->fu_acct;
					$ar['fu_client']=$row->fu_client;	
					$ar['fu_date']=$row->fu_date;	
					$ar['fu_length']=$row->fu_length;	
					$ar['fu_renewal']=$row->fu_renewal;	
					$ar['fu_invoice']=$row->fu_invoice;	
					$ar['fu_status']=$row->fu_status;	
					$ar['fu_start']=$row->fu_start;	
					$ar['fu_amount']=$row->fu_amount;	
					$ar['fu_usermod']=$row->fu_usermod;	
					$ar['fu_date_mod']=$row->fu_date_mod;	
					$ar['fu_notes']=$row->fu_notes;	
					$ar['fu_nonew']=$row->fu_nonew;	
					
					$i+=1;
				}
		}			
		return $ar;
    }
	
	public function getHotList($id) {         
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

		$result = $this->adapter->query('select * from tclihotlist where ch_cli_id = ? and ch_emp_id = ? ORDER BY ch_date_mod DESC LIMIT 1'	,
        array(     				
			$id, $userid						
		));
		$ar = array();
		if($result)
		{
			$i=0;
				foreach ($result as $row) {					
					$ar['ch_cli_id']=$row->ch_cli_id;
					$ar['ch_emp_id']=$row->ch_emp_id;	
					$ar['ch_hot_prospect']=$row->ch_hot_prospect;	
					$ar['ch_pending']=$row->ch_pending;	
					$ar['ch_lead_1']=$row->ch_lead_1;	
					$ar['ch_lead_2']=$row->ch_lead_2;	
					$ar['ch_date_mod']=$row->ch_date_mod;	
					$ar['ch_spec']=$row->ch_spec;	
					
					
					$i+=1;
				}
		}			
		return $ar;
    }
	
	public function updateHotList($post,$id) {         
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
		
		$post["ch_hot_prospect"]= $post["ch_hot_prospect"]==1 ? 1 : 0;
		$post["ch_pending"]= $post["ch_pending"]==1 ? 1 : 0;
		$post["ch_lead_1"]= $post["ch_lead_1"]==1 ? 1 : 0;
		$post["ch_lead_2"]= $post["ch_lead_2"]==1 ? 1 : 0;
		
		
		if($id!='' && $id!=0)
		{
			$result = $this->adapter->query('select * from tclihotlist where ch_cli_id = ? and ch_emp_id = ? ORDER BY ch_date_mod DESC LIMIT 1'	, array($id, $userid));
			$ar = array();
			if($result)
			{
				foreach ($result as $row) {		
					$cliid=$row->ch_cli_id;
				}				
				if($cliid!='' && $cliid!=0) //update
				{
					$result = $this->adapter->query('update tclihotlist set ch_hot_prospect=?, ch_pending=?, ch_lead_1=?, ch_lead_2=?, ch_spec=?, ch_date_mod=NOW() where ch_cli_id = ? and ch_emp_id = ? ORDER BY ch_date_mod DESC LIMIT 1'	, array($post["ch_hot_prospect"],$post["ch_pending"],$post["ch_lead_1"],$post["ch_lead_2"],$post["ch_spec"],$id, $userid));
			
				}
				else{ //insert
					$result = $this->adapter->query('insert into tclihotlist (ch_cli_id, ch_emp_id, ch_hot_prospect, ch_pending, ch_lead_1, ch_lead_2, ch_date_mod, ch_spec) values (?,?,?,?,?,?,NOW(),?)'	, array($id, $userid,$post["ch_hot_prospect"],$post["ch_pending"],$post["ch_lead_1"],$post["ch_lead_2"],$post["ch_spec"]));
			
				}
				
				$result = $this->adapter->query('update lstclients set primary_record=? where cli_id = ?  LIMIT 1'	, array($post["primary_record"],$id));
			
			}			
			return true;
		}
		
		return false;
    }
	
	
	public function getWeeklyClientList($post) {         
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
		//echo 'select * from vcliwkupdate where ctr_recruiter = ? or ctr_manager = ? order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no';
		$userid=105; ///**************************REMOVE
		$resultSet = $this->adapter->query('select * from vcliwkupdate where ctr_recruiter = ? or ctr_manager = ? order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no', array($userid,$userid));
			
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;					
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['ctr_recruiter']=$row->ctr_recruiter;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_email']=$row->ctct_email;
					$ar[$i]['ctct_addr_c']=$row->ctct_addr_c;
					$ar[$i]['ctct_st_code']=$row->ctct_st_code;
					$ar[$i]['ctct_recruiting']=$row->ctct_recruiting;
					$ar[$i]['sp_name']=$row->sp_name;
					$ar[$i]['ctr_manager']=$row->ctr_manager;
					$ar[$i]['cli_bill']=$row->cli_bill;
					$ar[$i]['cli_child']=$row->cli_child;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;
					$ar[$i]['at_name']=$row->at_name;
					$ar[$i]['ctr_wkupdate']=$row->ctr_wkupdate;
					$ar[$i]['at_abbr']=$row->at_abbr;					
										
					$i+=1;
				}
			}
		
		return $ar;
	}
	
	public function handleWeeklyForm1($post) {
		$userid = $_COOKIE["phguid"];
		 $userid=105; ///**************************REMOVE
		foreach($post as $key=>$val)
		{
			//echo $key." - ".$val."<br/>";
			if(strpos($key,"wh_")!==false){ //client id
				$cli_id=str_replace("wh_",'',$key);
				$cont_id=$val;
				//echo $cli_id." : ".$cont_id."<br/>";
				$client_ids[]="'".$cli_id."'";
				$contract_ids[]="'".$cont_id."'";
			}
		}
		
		$clientids=implode(",",$client_ids);
		$contractids=implode(",",$contract_ids);
		
		//$sql = "select * from vcliwkupdate where (ctr_recruiter = $userid or ctr_manager = $userid) and ctr_id in ($contractids) and cli_id in ($clientids)  order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no";
		//echo $sql;
		$resultSet = $this->adapter->query("select * from vcliwkupdate where (ctr_recruiter = ? or ctr_manager = ?) and ctr_id in ($contractids) and cli_id in ($clientids)  order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no", array($userid,$userid));
			
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;					
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['ctr_recruiter']=$row->ctr_recruiter;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_email']=$row->ctct_email;
					$ar[$i]['ctct_addr_c']=$row->ctct_addr_c;
					$ar[$i]['ctct_st_code']=$row->ctct_st_code;
					$ar[$i]['ctct_recruiting']=$row->ctct_recruiting;
					$ar[$i]['sp_name']=$row->sp_name;
					$ar[$i]['ctr_manager']=$row->ctr_manager;
					$ar[$i]['cli_bill']=$row->cli_bill;
					$ar[$i]['cli_child']=$row->cli_child;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;
					$ar[$i]['at_name']=$row->at_name;
					$ar[$i]['ctr_wkupdate']=$row->ctr_wkupdate;
					$ar[$i]['at_abbr']=$row->at_abbr;					
					$ar[$i]['savedtxt']=$this->getSavedForm($row->ctr_id); //get any saved text
					
					$contacts = $this->selectClientContacts($row->cli_id); //get contacts
					$ar[$i]['contacts']=$contacts;
					//echo var_dump($contacts);
					
					$i+=1;
				}
			}
		
		return $ar;
	}

	public function getSavedForm($ctr_id) {
		$userid = $_COOKIE["phguid"];		
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		$resultSet = $this->adapter->query("SELECT * FROM tupdates where tu_user_mod=? and tu_ctr_id=? limit 1", array($username,$ctr_id));
			
			$ar = array();
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$txt=$row->tu_text;
				}
			}
		return $txt;
	}
	
	
	public function saveWeeklyForm($post) {
		$userid = $_COOKIE["phguid"];
		 $userid=105; ///**************************REMOVE 268
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
			//echo $key." - ".$val."<br/>";
			if(strpos($key,"cn_")!==false){ //client id				
				$cont_id=str_replace("cn_",'',$key);
				//echo "HERE".$cont_id;
				$cont_num=$val;
				//echo $cli_id." : ".$cont_id."<br/>";
				//$client_ids[]="'".$cli_id."'";
				$contract_ids[]="'".$cont_id."'";
			}
			if($key=="cli_".$cont_id){
				//echo $val."<br/>";
				$cli_id = $val;
				$client_ids[]="'".$cli_id."'";
			}			
			
		}
		foreach ($contract_ids as $key=>$val)
		{			
			$cont_id = str_replace("'",'',$val);
			$cli_id = $post["cli_".$cont_id];
			$text = $post["tx_".$cont_id];
			
			//update saved info
			if($cont_id!='' && $text!=''){
				$sql = "delete from tupdates where tu_ctr_id = $cont_id";
				$resultSet = $this->adapter->query("delete from tupdates where tu_ctr_id = ? limit 1", array($cont_id));
				$sql = "insert into tupdates (tu_ctr_id, tu_user_mod, tu_text) values ($cont_id, $username, $text)";
				$resultSet = $this->adapter->query("insert into tupdates (tu_ctr_id, tu_user_mod, tu_date_mod, tu_text) values (?,?,NOW(),?)", array($cont_id, $username, $text));
				//echo $sql;
				
			}
			
		}
		
		$clientids=implode(",",$client_ids);
		$contractids=implode(",",$contract_ids);
		
		//echo var_dump($clientids);
		//$sql = "select * from vcliwkupdate where (ctr_recruiter = $userid or ctr_manager = $userid) and ctr_id in ($contractids) and cli_id in ($clientids)  order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no";
		//echo $sql;
		$resultSet = $this->adapter->query("select * from vcliwkupdate where (ctr_recruiter = ? or ctr_manager = ?) and ctr_id in ($contractids) and cli_id in ($clientids)  order by ctct_st_code, ctct_addr_c, ctct_name, cli_id, ctr_no", array($userid,$userid));
			
			$ar = array();
			if($resultSet)
			{
			$i=0;
				foreach ($resultSet as $row) {
					$ar[$i]['ctr_id']=$row->ctr_id;
					$ar[$i]['ctr_no']=$row->ctr_no;					
					$ar[$i]['ctr_spec']=$row->ctr_spec;
					$ar[$i]['cli_id']=$row->cli_id;
					$ar[$i]['ctr_recruiter']=$row->ctr_recruiter;
					$ar[$i]['ctr_location_c']=$row->ctr_location_c;
					$ar[$i]['ctr_location_s']=$row->ctr_location_s;
					$ar[$i]['ctct_name']=$row->ctct_name;
					$ar[$i]['ctct_email']=$row->ctct_email;
					$ar[$i]['ctct_addr_c']=$row->ctct_addr_c;
					$ar[$i]['ctct_st_code']=$row->ctct_st_code;
					$ar[$i]['ctct_recruiting']=$row->ctct_recruiting;
					$ar[$i]['sp_name']=$row->sp_name;
					$ar[$i]['ctr_manager']=$row->ctr_manager;
					$ar[$i]['cli_bill']=$row->cli_bill;
					$ar[$i]['cli_child']=$row->cli_child;
					$ar[$i]['ctr_nurse']=$row->ctr_nurse;
					$ar[$i]['ctr_nu_type']=$row->ctr_nu_type;
					$ar[$i]['at_name']=$row->at_name;
					$ar[$i]['ctr_wkupdate']=$row->ctr_wkupdate;
					$ar[$i]['at_abbr']=$row->at_abbr;					
					$ar[$i]['savedtxt']=$this->getSavedForm($row->ctr_id); //get any saved text
					
					$contacts = $this->selectClientContacts($row->cli_id); //get contacts
					$ar[$i]['contacts']=$contacts;
					//echo var_dump($contacts);
					
					$i+=1;
				}
			}
			
			
		
		return $ar;
	}
	
	public function sendWeeklyForm($post) {
		$userid = $_COOKIE["phguid"];
		 $userid=105; ///**************************REMOVE
		$username = $_COOKIE["username"];
		$realname = $_COOKIE["realname"];
		$title = $_COOKIE["title"];
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}
		else {
			$useremail = $username.'@phg.com';				
		}
		$resultSet = $this->adapter->query("SELECT ctct_title FROM lstemployees as e left join lstcontacts as c on ctct_id=emp_ctct_id where emp_id=?", array($userid));			
			
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$title=$row->ctct_title;
				}
			}
		
		$from = $useremail;			
		$to = $useremail; //remove later
		$subject = "Pinnacle Health Group Update";
		$headers = "From: ".$from."\r\n";
		$headers .=  'X-Mailer: PHP/'.phpversion()."\r\n";
		//echo var_dump($post);
		foreach($post as $key=>$val)
		{
			//echo $key." - ".$val."<br/>";
			if(strpos($key,"cn_")!==false){ //client id				
				$cont_id=str_replace("cn_",'',$key);
				//echo "HERE".$cont_id;
				$cont_num=$val;
				//echo $cli_id." : ".$cont_id."<br/>";
				//$client_ids[]="'".$cli_id."'";
				$contract_ids[]="'".$cont_id."'";
			}
			if($key=="cli_".$cont_id){
				//echo $val."<br/>";
				$cli_id = $val;
				$client_ids[]="'".$cli_id."'";
			}			
		}
		//echo var_dump($contract_ids);
		foreach ($contract_ids as $key=>$val)
		{			
			$cont_id = str_replace("'",'',$val);
			$cli_id = $post["cli_".$cont_id];
			$text = $post["tx_".$cont_id];
			$cont_num=$post["cn_".$cont_id];
			$spec = $post["cs_".$cont_id];
			$loc = $post["cl_".$cont_id];
			//echo $cont_num."#";
			//update saved info
			$contactstr = $post["sel_".$cli_id];
			$contarr=explode(";", $contactstr);
			$ccid=$contarr[0];
			$ccname=$this->shuffleName($contarr[1]);
			$ccemail=$contarr[2];
			
			$contactstr = $post["se2_".$cli_id];
			$contarr=explode(";", $contactstr);
			$ccid2=$contarr[0];
			$ccname2=$this->shuffleName($contarr[1]);
			$ccemail2=$contarr[2];
			
			$contactstr = $post["se3_".$cli_id];
			$contarr=explode(";", $contactstr);
			$ccid3=$contarr[0];
			$ccname3=$this->shuffleName($contarr[1]);
			$ccemail3=$contarr[2];
			
			if($cont_id!='' && $text!=''){
								
$msg="
Below is a weekly update report.
Please take a look.

*$cont_num* $spec – $loc

$text

Regards,
--
$realname
$title
Pinnacle Health Group
Phone: 1-800-492-7771 Fax: 404-591-4266
Email: $useremail
";
				if($ccemail!='')
				{					
					if($ccname!='')
						$message = "Dear $ccname,".$msg;
					else
						$message = "Dear $ccemail,".$msg;
					//$to=$ccemail;
					//echo "mail($to, $subject, $message, $headers)";
					mail($to, $subject, $message, $headers);
				}
				if($ccemail2!='')
				{
					if($ccname2!='')
						$message = "Dear $ccname2,".$msg;
					else
						$message = "Dear $ccemail2,".$msg;
					//$to=$ccemail;
					mail($to, $subject, $message, $headers);
				}
				if($ccemail3!='')
				{
					if($ccname3!='')
						$message = "Dear $ccname3,".$msg;
					else
						$message = "Dear $ccemail3,".$msg;
					//$to=$ccemail;
					mail($to, $subject, $message, $headers);
				}

			}//end if
			//echo $ccname3."##";
		}
		return true;
	}
	
	
	
	public function shuffleName($name)
	{
		if(strpos($name,",")!==false){
		$namearr = explode(",",$name);
		$name = $namearr[1]." ".$namearr[0];
		}
		return $name;
	}
	
	//new function to merge old client into new one
	public function mergeClient($post) {         
        $userid = $_COOKIE["phguid"];
		$username = $_COOKIE["username"];		
		if(strpos($username, '@phg.com')!==false){
			$useremail = $username;
			$username = str_replace('@phg.com', '', $username);
		}		
				
		if($post["cli_id"]!='' && $post["master_client_id"]!='')
		{
			$resultSet = $this->adapter->query('call MergeClients(?, ?, ?, ?)', array($post["master_client_id"], $post["cli_id"], $username,$userid));					
			if(!$resultSet)
				return false;
			else
				return true;
			
		}
		else { return false; }
		
	}

}
