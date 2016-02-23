<?php
// module/Pinnacle/src/Pinnacle/Model/Report/LookupTable.php:
// !!! abstract table !!!
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LookupTable extends ReportTable
{
    protected $paginator = null;
    
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'lstcontacts',
            '`ctct_id`, `ctct_name`, `ctct_title`, `ctct_company`, `ctct_phone`, `ctct_ext1`, `ctct_fax`, `ctct_ext2`, `ctct_cell`, `ctct_pager`, `ctct_ext3`, `ctct_email`, `ctct_addr_1`, `ctct_addr_2`, `ctct_addr_c`, `ctct_addr_z`, `ctct_st_code`, `ctct_url`, `ctct_reserved`, `ctct_reserved2`, `ctct_type`, `ctct_status`, `ctct_user_mod`, `ctct_date_mod`, `ctct_hphone`, `ctct_hfax`, `ctct_backref`, `ctct_email_mod`, `ctct_recruiting`, `ctct_marketing`, `ctct_secondary`, `ctct_bounces`, `ctct_fuzion1`, `ctct_fuzion2`, `ctct_unsub`, `ctct_unsub2`, `ctct_unsub3`');
    }

    /**
     * @param array $ar     keys: see LookupForm
    */
    public function buildQuery(array $ar = null) {
        if( is_array($ar) && $ar['emp_id'] ) {
            if( !$ar['cli_hotlist'] ) $this->table = 'vclientlookupsmall'; // eeeek!
            $where = new Where();
            $nduh = 0;
            if( $ar['cli_id'] ) { $where->equalTo('cli_id',$ar['cli_id']); return $where; }
            
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
            if( trim($ar['phone']) ) {
				$phonenum=trim($ar['phone']);
				$phonenum1=str_replace('-','',str_replace('(','',str_replace(')','',$phonenum)));
                $like = addslashes(trim($phonenum1));
				echo $like;
				$like2p = formatPhoneNumber(trim($phonenum)); //added
				//$like2p=$like;
                $where->literal('cast(ctct_phone as varchar) like ? or cast(ctct_phone as varchar) like ?',array("$like%", "$lik2p%")); $nduh++;
            }
            if( $ar['cli_state'] ) { $where->equalTo('ctct_st_code',$ar['cli_state']); $nduh++; }
            if( $ar['cli_beds'] ) { $where->greaterThanOrEqualTo('cli_beds',$ar['cli_beds']); $nduh++; }
            if( $ar['cli_status'] >= 0 ) { $where->equalTo('cli_status',$ar['cli_status']); $nduh++; }
            elseif( $ar['cli_status'] == -2 ) { $where->in('cli_status',array(1,10)); $nduh++; }
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
			$where->notEqualTo('ctct_status',12); $nduh++;
           
            return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
    }
	
	

    public function getPages($id = 1, array $ar = null) {
        if( !$this->paginator ) {
            $where = $this->buildQuery($ar);
            if( $where ) {
                $select = new Select($this->table);
                $select->where($where);
                $select->order('ctct_name,ctct_st_code,ctct_addr_c');				
                $limit = (int) $ar['pg_size']; if( !$limit || $limit > 200 ) $limit = 25;
                $this->initPaginator($select)->setItemCountPerPage($limit);
            }
            else return false;
        }
        $this->paginator->setCurrentPageNumber($id);
        return $this->paginator;
    }
	
	//build lookup query
	public function buildQuery2(array $ar = null) {
        if( is_array($ar) ) {
		//echo "test";
            $this->table = 'lstcontacts'; // eeeek!
            $where = new Where();
            $nduh = 0;
			$valid=true;
            
			if(isset($ar["ntype_0"]))
			{
				//echo "type 0";
				if($ar["name"]!="")
				{
					$like=$ar["name"];
					//$where->like('ctct_name',"$like%");
					
					$names=explode(" ",$like);
					if(count($names)>1){
						$fn = $names[0];
						$ln = $names[1];
						$like1 = addslashes("$ln%, $fn%");
						$like2 = addslashes("$fn% $ln%");
						$where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
					}
					else{
						$like1 = addslashes("$like%");
						$like2 = addslashes("%$like");
						//$where->like('ctct_name',"$like%");
						$where->like('ctct_name',$like2);
						//$where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;
					}
					if($ar["type"]==1)
						$where->in('ctct_type',array(2,4,5));
					if($ar["type"]==2)
						$where->in('ctct_type',array(3,7,9));
					/*$like1 = addslashes("$ln%, $fn%");
					$like2 = addslashes("$fn% $ln%");
					$where->nest->like('ctct_name',$like1)->or->like('ctct_name',$like2)->unnest;*/
				}
				else { $valid=false; }
			}
			if(isset($ar["ntype_1"])) //phone
			{
				if(strlen(trim($ar["phone"]))>=3)
				{		
//echo addslashes(trim($ar['phone']));			
					$like = $ar['phone']; //addslashes(trim($ar['phone']));
					//$where->like('ctct_phone',"$like%");
					
					//$where->literal('ctct_phone like ?',array("$like%"));
					//$where->literal("ctct_phone like '$like%'");
					//$where->literal('cast(ctct_phone as char(15)) like ? ',array("$like%"));
					
					$phonenum=trim($ar['phone']);
					$phonenum1=str_replace(' ', '', str_replace('-','',str_replace('(','',str_replace(')','',$phonenum))));
					$like = addslashes(trim($phonenum1));
					
					$like2p = $this->formatPhoneNumber(trim($phonenum)); //added
					//echo $like2p;
					//$like2p=$like;
					//$where->literal('cast(ctct_phone as varchar) like ? or cast(ctct_phone as varchar) like ?',array("$like%", "$lik2p%")); 
					//$where->literal('ctct_cell  like ? ',array("$like%")); 
					$where->nest->literal('ctct_phone  like ? ',array("$like%"))->or->literal('ctct_cell  like ? ',array("$like%"))->or->literal('ctct_hphone  like ? ',array("$like%"))->unnest;
					//$where->literal('ctct_phone  like ? or ctct_phone like ?',array("$like%", "$lik2p")); 
				}
				else { $valid=false; }
			}
			if(isset($ar["ntype_2"])) //email
			{
				if($ar['email']!=""){
					$like = $ar['email'];
					$where->like('ctct_email',"$like%");
				}
				else { $valid=false; }
			}
			if(isset($ar["ntype_3"])) //address
			{
				if($ar['address']!="" || $ar['city']!="" || $ar['state']!="" || $ar['zip']!=""){				
					if(trim($ar['address'])!="")
						$where->like('ctct_addr_1',"".$ar['address']."%");
					if(trim($ar['city'])!="")
						$where->like('ctct_addr_c',"".$ar['city']."%");
					if(trim($ar['state'])!="" && $ar['state']!="--")
						$where->like('ctct_st_code',"".$ar['state']."%");
					if(trim($ar['zip'])!="")
						$where->like('ctct_addr_z',"".$ar['zip']."%");
						
				}
				else { $valid=false; }
			}
			if($valid)
				return $where;
			else
				return false;           
           
            //return $nduh? $where: false; // don't allow empty criteria
        }
        return false;
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
	
	public function getTypes()
	{
		$ar = Array();			
		
		$result = $this->adapter->query('select * from dctcontacttypes  ', array());		
			if($result)
			{			
				foreach ($result as $row) {
					
					$ar[$row->ctct_code][0] =$row->ctct_name;
					$ar[$row->ctct_code][1] =$row->ctct_asp;	
					$ar[$row->ctct_code][2] =$row->ctct_param;
				}
			}  
			//echo var_dump($ar);			
			return $ar;		
	}
	
	public function getLookupPages($ar,$page = 1,$pgsize = 25) {
        if( !$this->paginator ) { 
            $where = $this->buildQuery2($ar);
            if( $where ) { 
                $select = new Select('lstcontacts');
                $select->where($where);
				$select->order('ctct_name');	
                //$select->order('ctct_name,ctct_st_code,ctct_addr_c');	
					//echo $str = @$sql->getSqlStringForSqlObject($select);
                $limit = (int) $ar['pg_size']; if( !$limit || $limit > 200 ) $limit = 25;
                $this->initPaginator($select)->setItemCountPerPage($limit);
            }
            else return false;
        }
		//echo "P____".$page;
		//$page=2;
        $this->paginator->setCurrentPageNumber($page);
		
        return $this->paginator;
    }
    
    protected function initPaginator(Select $query = null) {
        if( !$this->paginator && $query ) {
            $padapter = new DbSelect($query, $this->adapter, $this->resultSetPrototype );
            $this->paginator = new Paginator($padapter);
        }
        return $this->paginator;
    }
	
	

}
