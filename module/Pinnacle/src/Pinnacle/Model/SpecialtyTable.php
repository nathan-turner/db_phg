<?php
// module/Pinnacle/src/Pinnacle/Model/SpecialtyTable.php:

namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Pinnacle\Model\Report\ReportTable;

class SpecialtyTable extends ReportTable
{
    protected $nobsp = array("\xC2\xA0\xC2\xA0\xC2\xA0 ","\xC2\xA0\xC2\xA0 ","\xC2\xA0 ",' ');
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'dctspecial',
                            '`sp_code`, `sp_name`, `sp_group`, `sp_grpcode`, `res_spec`, `sp_prima`');
    }

    public function getSpecialties() {
        $select = new Select($this->table);
        $select->where('sp_code <> \'---\'')->order('sp_code');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function fetchAll() {
        return $this->select();
    }
    
    public function getSelectOptions($nosp = 0) {
        $resultSet = $nosp? $this->getSpecialties(): $this->fetchAll();
        $ar = array();
        foreach ($resultSet as $spec) {
            $sp = trim($spec->sp_code);
            $ar[$sp] = $sp . $this->nobsp[strlen($sp)]. $spec->sp_name;
        }
        return $ar;
    }

}
