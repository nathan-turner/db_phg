<?php
// module/Pinnacle/src/Pinnacle/Model/Mail/ListsTable.php:
/* *** this table (custlistsus) can also be modified by search model classes:
 *     ClientsTable, ContractsTable, PhysiciansTable, MidlevelsTable
 */
namespace Pinnacle\Model\Mail;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Pinnacle\Model\Report\ReportTable;
use Pinnacle\Model\Report\RecordGen;

class ListsTable extends ReportTable
{
    protected $paginator = null;

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'custlistsus',
                            '`owneruid`, `listid`, `memberuid`, `ctype`');
    }

    public function fetchAll($uid,$lid,$page = 0,$pgsize = 25) {
        $uid = (int) $uid; $lid = (int) $lid;
        $select = new Select($this->table);
        $select->where(array( 'owneruid'=>$uid, 'listid'=>$lid ));
        //        $select->order('memberuid DESC');
        $limit = (int) $pgsize; if( $limit<=0 || $limit > 200 ) $limit = 25;
        $select->limit($limit);
        $page--; if( $page < 0 ) $page = 0;
        $offset = $page * $limit;
        $select->offset($offset);
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function deleteListRow($uid,$lid,$ctid) {
        $uid = (int) $uid; $lid = (int) $lid; $ctid = (int) $ctid;
        $this->delete(array( 'owneruid'=>$uid, 'listid'=>$lid, 'memberuid'=>$ctid ));
    }

    /* @param array $set : plain array of memberuid's
     * deletes all ids from $set array
     * */
    public function deleteListSet($uid,$lid,array $set) {
        $uid = (int) $uid; $lid = (int) $lid; 
        if( is_array($set) ) {
            $this->delete(array( 'owneruid'=>$uid, 'listid'=>$lid, 'memberuid'=>$set ));
        }
    }
    
    /* @param array/string $set : plain array of memberuid's (or comma-separated string)
     * deletes all ids except ones in $set array
     * */
    public function keepListSet($uid,$lid,$set,$scope) {
        $uid = (int) $uid; $lid = (int) $lid; $ctype = (int) $ctype;
        $where = new Where();
        $where->equalTo('owneruid',$uid)->equalTo('listid',$lid);
        if( $scope && is_array($scope) )
            $where->in('memberuid',$scope);
        $ids = implode(',', preg_replace('/[^0-9,]/','',is_array($set)? $set: array($set)));
        $where->literal("memberuid not in ($ids)");
        $this->delete($where);
    }

    public function getPages($uid,$lid,$page = 1,$pgsize = 25) {
        if( !$this->paginator ) {
            $uid = (int) $uid; $lid = (int) $lid;
            $select = new Select('vcustlist');
            $select->where(array( 'owneruid'=>$uid, 'listid'=>$lid ));
            $select->order('ctype, ctct_name');
            $limit = (int) $pgsize; if( $limit<=0 || $limit > 200 ) $limit = 25;
            if( $page < 1 ) $page = 1;

            $this->initPaginator($select)->setItemCountPerPage($limit);
        }
        $this->paginator->setCurrentPageNumber($page);
        return $this->paginator;
    }
    
    protected function initPaginator(Select $query = null) {
        if( !$this->paginator && $query ) {
            $rs = new ResultSet();
            $rs->setArrayObjectPrototype(new RecordGen(array('owneruid', 'listid', 'ctype', 'ctct_name', 'ctct_id', 'ctct_email', 'ctct_asp', 'ct_name', 'ctct_backref')));
            $padapter = new DbSelect($query, $this->adapter, $rs );
            $this->paginator = new Paginator($padapter);
        }
        return $this->paginator;
    }
	
	public function getEmails($list, $type, $locums=0)
	{
		$ar = Array();
		$userid = $_COOKIE["phguid"];
		//$list = trim($list,',');
		if($list!='')
		{
			$result = $this->adapter->query('SET SESSION SQL_BIG_SELECTS=1',array());
		if($locums!=1)
		{	
		//echo 'SELECT * FROM vemailexport AS v left join custlistsus AS c ON v.ctct_id=c.memberuid  where c.listid=20 and ctype=3 and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid';
		//echo 'SELECT ctct_name, ctct_email FROM custlistsus AS c left join vemailexport AS v  ON v.ctct_id=c.memberuid  where c.listid='.$list.' and ctype=3 and owneruid=234 and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid';
			if($type==3)
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_hphone, ctct_cell FROM custlistsus AS c left join vemailexport AS v  ON v.ctct_id=c.memberuid  where c.listid=? and ctype=3 and owneruid=? and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list,$userid));
			//$result = $this->adapter->query('select * from vemailexport where ctct_id IN( '.$list.' ) ', array($list));	//can't use param		
			if($type==2) //echo 'SELECT ctct_name, ctct_email FROM custlistsus AS c  left join vcliemailexport AS v ON v.ctct_id=c.memberuid  where c.listid=? and ctype=2 and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid';
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_id, ctct_backref, cli_id, memberuid, ctct_hphone, ctct_cell FROM ( SELECT * FROM custlistsus AS u LEFT JOIN lstclients AS l on cli_ctct_id = memberuid where listid=? and ctype=2 and owneruid=?  ) AS c LEFT JOIN lstcontacts AS ct ON (ct.ctct_backref=c.cli_id and ct.ctct_type = 5) where ((ctct_bounces<>1 OR ISNULL(ctct_bounces)) and (ctct_email is not null) and (ctct_unsub<>1 OR ISNULL(ctct_unsub)) and (ctct_unsub2<>1 OR ISNULL(ctct_unsub2)) and (not ((ctct_email like "%.ca"))))', array($list,$userid));
			//$result = $this->adapter->query('SELECT ctct_name, ctct_email FROM custlistsus AS c left join vcliemailexport2 AS v ON v.ctct_id=c.memberuid  where c.listid=? and ctype=2 and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list));
			
			//$result = $this->adapter->query('SELECT ctct_name, ctct_email FROM custlistsus AS c  left join vcliemailexport AS v ON v.ctct_id=c.memberuid  where c.listid=? and ctype=2 and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list));
			//$result = $this->adapter->query('select * from vcliemailexport where ctct_id IN( '.$list.' ) ', array($list));
			if($type==15)
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_hphone, ctct_cell FROM custlistsus AS c  left join vmidemailexport AS v ON v.ctct_id=c.memberuid  where c.listid=? and ctype=15 and owneruid=? and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list,$userid));
		}
		else
		{
			
			if($type==3)
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_hphone, ctct_cell FROM custlistsus AS c left join vemailexport2 AS v  ON v.ctct_id=c.memberuid  where c.listid=? and ctype=3 and owneruid=? and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list,$userid));
			if($type==2) 
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_id, ctct_backref, cli_id, memberuid, ctct_hphone, ctct_cell FROM ( SELECT * FROM custlistsus AS u LEFT JOIN lstclients AS l on cli_ctct_id = memberuid where listid=? and ctype=2 and owneruid=?  ) AS c LEFT JOIN lstcontacts AS ct ON (ct.ctct_backref=c.cli_id and ct.ctct_type = 5) where ((ctct_email is not null) and not ((ctct_email like "%.ca")))', array($list,$userid));
			if($type==15)
				$result = $this->adapter->query('SELECT ctct_name, ctct_email, ctct_hphone, ctct_cell FROM custlistsus AS c  left join vmidemailexport2 AS v ON v.ctct_id=c.memberuid  where c.listid=? and ctype=15 and owneruid=? and NOT ISNULL(ctct_email) and NOT ISNULL(memberuid) order by memberuid', array($list,$userid));	
		}
			//$result = $this->adapter->query('select * from vmidemailexport where ctct_id IN( '.$list.' ) ', array($list));
		$i=0;	
			if($result)
			{			
				foreach ($result as $row) {
					
					$ar[$i]["ctct_name"] =$row->ctct_name;
					$ar[$i]["ctct_email"] =$row->ctct_email;
					$ar[$i]["ctct_hphone"] =$row->ctct_hphone;
					$ar[$i]["ctct_cell"] =$row->ctct_cell;
					$i+=1;					
				}
			}  
			//echo var_dump($ar);
			
			return $ar;
		}
	}
	
	
	
	
	public function addEmail($post) {   
	$userid = $_COOKIE["phguid"];
	
	$subject = $post["subject"];
	$body = $post["body"];
	$fromaddr = $post["fromaddr"];
	$listid = $post["lists"];
	$title = $post["title"];
	
	$result = $this->adapter->query('insert into emailcamp (emailtitle, subject, body, fromaddr, listid, ownerid, created) values (?,?,?,?,?,?,NOW())', array($title, $subject, $body, $fromaddr, $listid, $userid));	
		
	return true;
	}
	
	public function getEmail($id) {   
		$userid = $_COOKIE["phguid"];
	
		$subject = $post["subject"];
		$body = $post["body"];
		$fromaddr = $post["fromaddr"];
		$listid = $post["lists"];
	
		$result = $this->adapter->query('select * from emailcamp where emailid=? ', array($id));
		if($result)
			{			
				foreach ($result as $row) {
					
					$ar["subject"] =$row->subject;
					$ar["body"] =$row->body;
					$ar["fromaddr"] =$row->fromaddr;
					$ar["listid"] =$row->listid;
					$ar["created"] =$row->created;					
					$ar["emailtitle"] =$row->emailtitle;				
				}
			}  		
		
		return $ar;
	}
	
	public function editEmail($post,$id) {   
	$userid = $_COOKIE["phguid"];
	
	$subject = $post["subject"];
	$body = $post["body"];
	$fromaddr = $post["fromaddr"];
	$listid = $post["lists"];
	$title = $post["title"];
	
	$result = $this->adapter->query('update emailcamp set emailtitle=?, subject=?, body=?, fromaddr=?, listid=? WHERE emailid=? LIMIT 1', array($title, $subject, $body, $fromaddr, $listid, $id));	
		
	return true;
	}
	
	public function getMyEmails() {   
		$userid = $_COOKIE["phguid"];
	
		$subject = $post["subject"];
		$body = $post["body"];
		$fromaddr = $post["fromaddr"];
		$listid = $post["lists"];
	
		$result = $this->adapter->query('select * from emailcamp where ownerid=? ', array($userid));
		if($result)
			{		
			$i=0;		
				foreach ($result as $row) {
					$ar[$i]["emailid"] =$row->emailid;
					$ar[$i]["emailtitle"] =$row->emailtitle;
					$ar[$i]["subject"] =$row->subject;
					$ar[$i]["body"] =$row->body;
					$ar[$i]["fromaddr"] =$row->fromaddr;
					$ar[$i]["lastsent"] =$row->lastsent;
					$ar[$i]["created"] =$row->created;					
					$i++;				
				}
			}  		
		
		return $ar;
	}
	
	public function deleteEmail($id) {   
		$userid = $_COOKIE["phguid"];
		if($id>0 && $id!='')
			$result = $this->adapter->query('delete from emailcamp where emailid=? limit 1', array($id));	
		else 
			return false;
		
		return true;
	}
	
	public function deleteList($id) {   
		$userid = $_COOKIE["phguid"];
		
		if($userid>0 && $userid!='' && $id>0 && $id!='')
			$result = $this->adapter->query('call DeleteEmailList(?,?)', array($id, $userid));	
		else
			return false;
		
		return true;
	}
	
	
	
}





/*
$this->select(function (Select $select) use ($id){
$select->where(array('kind'=>$id));
$select->order(array('group','group_order'));
});
*/