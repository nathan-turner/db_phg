<?php
// module/Pinnacle/src/Pinnacle/Model/UsersTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class UsersTable extends AbstractTableGateway
{
    protected $table ='lstemployees';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Admin\Users());

        $this->initialize();
    }

    public function fetchAll()
    {
        $select = new Select($this->table);
        $select->order('emp_status DESC, emp_uname');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function fetchActive()
    {
        $select = new Select($this->table);
        $select->where('emp_status=1')->order('emp_realname');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function getSelectOptions($dept = null) {
        $resultSet = $this->fetchActive();
        $ar = array();
        foreach ($resultSet as $emp) {
            // not very effective, but who cares - it's only 25 or less records
            if( $dept && strpos($emp->emp_dept, $dept)===false ) continue;
            $ar["$emp->emp_id"] = $emp->emp_realname;
        }
        return $ar;
    }
}
