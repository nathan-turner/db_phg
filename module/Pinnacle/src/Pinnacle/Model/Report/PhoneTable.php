<?php
// module/Pinnacle/src/Pinnacle/Model/Report/PhoneTable.php:
// Warning: Two-In-One !!!
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class PhoneTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vcalls',
                            'emp_uname, emp_realname, call_numin, call_numout, call_timein, call_timeout,  call_date');
    }

    public function fetchDate($d) {
        $select = new Select($this->table);
        $select->where(array('call_date' => $d));
        $select->order('emp_realname');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function fetchAll() {
        $this->table = 'vcallshour'; // eeeek
        $select = new Select('vcallshour');
        $d = date('Y-m-d');
        // $d = '2012-08-15';
        $select->where("call_date >= '$d'");
        $select->order('emp_realname');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

}
