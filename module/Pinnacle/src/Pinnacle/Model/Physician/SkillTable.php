<?php
// module/Pinnacle/src/Pinnacle/Model/SkillTable.php:

namespace Pinnacle\Model\Physician;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Pinnacle\Model\Report\ReportTable;

class SkillTable extends ReportTable
{
    protected $nobsp = array("\xC2\xA0\xC2\xA0\xC2\xA0 ","\xC2\xA0\xC2\xA0 ","\xC2\xA0 ",' ');
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter, 'vspecial', '`sp_code`, `skill`, `spec`');
    }

    public function getSpecialties() {
        $select = new Select($this->table);
        $select->where('sp_code <> \'---\'')->order('sp_code, skill');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function fetchAll() {
        $select = new Select($this->table);
        $select->order('sp_code, skill');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
    
    public function getSelectOptions($nosp = 0) {
        $resultSet = $nosp? $this->getSpecialties(): $this->fetchAll();
        $ar = array();
        foreach ($resultSet as $spec) {
            $sp = trim($spec->sp_code);
            $idx = substr($sp.'   ',0,3). $spec->skill;
            $ar[$idx] = $sp . $this->nobsp[strlen($sp)]. $spec->spec;
        }
        return $ar;
    }

}
