<?php
// module/Pinnacle/src/Pinnacle/Model/CleanupTable.php:
namespace Pinnacle\Model\Admin;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class CleanupTable extends AbstractTableGateway
{
    protected $table ='vcontracts1'; // not really
    protected $sqlArray = array(
        'cli' => array( 'table' => 'vclient',
                        'columns' => array('id' => 'cli_id','name' => 'ctct_name',
                                        'city' => 'ctct_addr_c','state' => 'ctct_st_code',
                                        'spec' => 'cli_specialty'),
                        'where' => 'cli_id = %u and cli_id != 6',
                        'del' => 'call DelAClient(?)',
                      ),
        'ctr' => array( 'table' => 'vcontracts1',
                        'columns' => array('id' => 'ctr_id','name' => 'ctct_name',
                                        'city' => 'ctr_location_c',
                                        'state' => 'ctr_location_s',
                                        'spec' => 'ctr_spec',
                                        'no' => 'ctr_no'),
                        'where' => 'ctr_no = \'%s\'',
                        'del' => 'call DelAContract(?)',
                      ),
        'phy' => array( 'table' => 'vphmain',
                        'columns' => array('id' => 'ph_id','name' => 'ctct_name',
                                        'city' => 'ctct_addr_c','state' => 'ctct_st_code',
                                        'spec' => 'ph_spec_main'),
                        'where' => 'ph_id = %u and ph_id != 1',
                        'del' => 'call DelAPhysician(?)',
                      ),
        'mid' => array( 'table' => 'vanmain',
                        'columns' => array('id' => 'an_id','name' => 'ctct_name',
                                        'city' => 'ctct_addr_c','state' => 'ctct_st_code',
                                        'spec' => 'at_abbr'),
                        'where' => 'an_id = %u and an_id != 1',
                        'del' => 'call DelANurse(?)',
                      ),
    );

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Cleanup());

        $this->initialize();
    }

    public function fetchAll() {
        return false;
    }
    
    public function fetchRow($part, $id) {
        $ar = $this->sqlArray[$part];
        if( is_array($ar) ) {
            $this->table = $ar['table'];
            $select = new Select($this->table);
            $select->columns($ar['columns']);
            $wha = sprintf($ar['where'],$id);
            $select->where($wha);
            $resultSet = $this->selectWith($select);
            $row = $resultSet->current();
            return $row;
        }
        return false;
    }

    public function deleteRow($part, $id) {
        $ar = $this->sqlArray[$part];
        if( is_array($ar) ) {
            $this->table = $ar['table'];
            $result = $this->adapter->query($ar['del'],array($id));
            return $result;
        }
        return false;
    }

}
