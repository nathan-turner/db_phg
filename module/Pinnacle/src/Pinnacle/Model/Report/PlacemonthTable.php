<?php
// module/Pinnacle/src/Pinnacle/Model/Report/PlacemonthTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class PlacemonthTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vplacemonth',
                            '`pl_id`, `pl_date`, `ctr_id`, `ctr_no`, `ctr_date`, `ctr_spec`, `st_name`, `ctr_location_c`, `ctr_location_s`, `cli_sys`, `mark_uname`, `mark_id`, `emp_uname`, `ctct_name`, `ctct_phone`, `cli_id`, `s2pl`, `pl_annual`, `pl_split_emp`, `split_uname`, `ph_name`, `ph_city`, `ph_state`, `ctr_nurse`, `at_abbr`');
    }

    /**
     * @param array $ar     keys: yer, spec (0=All,'---'=Midlevels), st_code (0=All)
    */
    public function fetchAll(array $ar = null) {
        $select = new Select($this->table);
        if( is_array($ar) ) {
            $where = new Where();
            if( $ar['spec'] === '---' ) $where->equalTo('ctr_nurse',1);
            elseif( $ar['spec'] ) $where->equalTo('ctr_spec',$ar['spec']);
            
            if( $ar['st_code'] ) $where->equalTo('ctr_location_s', $ar['st_code'] );
            
            if( $ar['yer'] )
                $where->literal('year(pl_date) = ?',array($ar['yer']));
            
            $select->where($where);
        }
        $select->order('pl_date, ctr_location_s, ctct_name, ctr_spec');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

}
