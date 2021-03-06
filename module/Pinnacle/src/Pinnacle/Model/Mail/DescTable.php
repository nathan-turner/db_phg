<?php
// module/Pinnacle/src/Pinnacle/Model/Mail/DescTable.php:
namespace Pinnacle\Model\Mail;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Pinnacle\Model\Report\ReportTable;

class DescTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'custlistdesc',
                            '`uid`, `list_id`, `description`, `name`, `shared`, `date_mod`');
    }

    public function fetchAll($uid = 0) { //echo $this->table;
        $uid = (int) $uid;
		//echo $uid;
        $select = new Select($this->table);
        $select->where(array('uid' => $uid));
        $select->order('date_mod DESC');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function getSelectOptions($uid = 0) {
        $ar = array();
        $result = $this->fetchAll($uid);
        foreach( $result as $row ) 
            $ar[$row->list_id] = $row->name;
        return $ar;
    }

    public function fetchOne($uid,$lid) {
        $uid = (int) $uid;
        $select = new Select($this->table);
        $select->where(array('uid' => $uid,'list_id'=> $lid));
        $resultSet = $this->selectWith($select);
        return $resultSet->current();
    }

    public function createDescList($uid,$name = 'New List') {
        $uid = (int) $uid;
        $resultSet = $this->fetchAll($uid);
        $ids = array();
        foreach( $resultSet as $desc ) $ids[$desc->list_id] = 1;
        for( $i = 15; $i<256; $i++ ) if( !isset($ids[$i]) ) break;
        if( $i == 256 ) return 0; // no room
        $ar = array('uid' => $uid, 'list_id' => $i, 'name' => $name);
        $this->insert($ar);
        return $i;
    }
    
    public function editDescList($uid,$lid,$name = 'New List',$desc = null, $shared = 0) {
        $uid = (int) $uid; $lid = (int) $lid; $shared = $shared? 1:0;
        $ar = array(
            'uid' => $uid, 'list_id' => $lid,   
        );
        $rowset = $this->select($ar);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find list $lid");
        }
        $newrow = array( 'name'=> $name, 'description'=> $desc, 'shared'=> $shared );
        $this->update($newrow, $ar);
    }
    public function editDescForm($data) {
        $this->editDescList( $data['uid'], $data['list_id'],
                             $data['name'], $data['description'], $data['shared'] );
    }
    
    public function deleteDescList($uid,$lid) {
        $uid = (int) $uid; $lid = (int) $lid;
        $sql = new Sql($this->adapter);
        $delete = $sql->delete()->from('custlistsus')
                      ->where(array( 'owneruid'=>$uid, 'listid'=>$lid ));
        $this->adapter->query($sql->getSqlStringForSqlObject($delete), Adapter::QUERY_MODE_EXECUTE);
        $this->delete(array( 'uid'=>$uid, 'list_id'=>$lid ));
    }
}
