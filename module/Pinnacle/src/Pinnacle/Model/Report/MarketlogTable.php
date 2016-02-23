<?php
// module/Pinnacle/src/Pinnacle/Model/Report/MarketlogTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class MarketlogTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vcliretainerlog',
                            '`ctrm`, `ctry`, `ctr_retain_date`, `ctr_type`, `ctr_marketer`, `ctr_recruiter`, `ctr_amount`, `ctr_monthly`, `ctr_spec`, `ctr_nurse`, `ctr_location_c`, `ctr_location_s`, `ctr_no`, `mar_name`, `rec_name`, `at_abbr`, `ctct_name`, `cli_id`');
    }

    /**
     * @param array $ar     keys: yer, emp_id (0=All), ord (mark = order by mar_name)
    */
    public function fetchAll(array $ar = null) {
        $select = new Select($this->table);
        $sord = 'ctrm, mar_name, ctr_no';
        if( is_array($ar) ) {
            $where = new Where();
            if( $ar['emp_id'] ) $where->equalTo('ctr_marketer', $ar['emp_id'] );
            if( $ar['yer'] )    $where->equalTo('ctry', $ar['yer']);
            
            $select->where($where);
            if( $ar['ord'] == 'mark' ) $sord = 'mar_name, ctrm, ctr_no';
        }
        $select->order($sord);
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

}
