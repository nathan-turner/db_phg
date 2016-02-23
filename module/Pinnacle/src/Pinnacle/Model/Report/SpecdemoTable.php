<?php
// module/Pinnacle/src/Pinnacle/Model/Report/SpecdemoTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class SpecdemoTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'tspecdemo',
            '`sd_id`, `sd_name`, `sd_gt`, `sd_md`, `sd_do`, `sd_amg`, `sd_fmg`, `sd_bc`, `sd_gt50`, `sd_md50`, `sd_do50`, `sd_amg50`, `sd_fmg50`, `sd_bc50`, `sd_gt45`, `sd_md45`, `sd_do45`, `sd_amg45`, `sd_fmg45`, `sd_bc45`, `sd_gt40`, `sd_md40`, `sd_do40`, `sd_amg40`, `sd_fmg40`, `sd_bc40`, `sd_gtres`, `sd_mdres`, `sd_dores`, `sd_amgres`, `sd_fmgres`, `sd_gt3`, `sd_md3`, `sd_do3`, `sd_amg3`, `sd_fmg3`, `sd_bc3`, `sd_ama`');
    }

    public function fetchAll() {
        $select = new Select($this->table);
        $select->columns(array('sd_id','sd_name'));
        $select->order('sd_name');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
    
    public function getDemo($id = 0) {
        $id = (int) $id;
        if( $id ) 
            $rowset = $this->select(array( 'sd_id' => $id,  ));
        else
            $rowset = $this->select(array( 'sd_name' => ' All',  ));

        $row = $rowset->current();

        return $row;
    }

}
