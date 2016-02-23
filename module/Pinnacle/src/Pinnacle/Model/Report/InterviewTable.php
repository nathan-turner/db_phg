<?php
// module/Pinnacle/src/Pinnacle/Model/Report/InterviewTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class InterviewTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vinterviewrpt',
                            '`emp_uname`, `pipl_date`, `ph_id`, `ctct_name`, `ama`, `cas`, `ref`, `ctr_id`, `ctr_no`, `ctr_location_c`, `ctr_location_s`, `ph_spec_main`, `iq`, `ctr_type`, `ctr_nurse`, `pipl_nurse`, `at_abbr`');
    }

    /**
     * @param array $ar     keys: yer, mon
    */
    public function fetchAll2(array $ar = null) {
        $select = new Select($this->table);
        if( is_array($ar) ) {
            $where = new Where();
            $y = $ar['yer']? sprintf('%04d',$ar['yer']): date('Y');
            $m = $ar['mon']? sprintf('%02d',$ar['mon']): date('m');
            $sm1 = "$y-$m-01";
            if( $m == '12' ) {
                $y = sprintf('%04d',$y+1); $m = '01';
            }
            else $m = sprintf('%02d',$m+1);
            
            $sm2 = "$y-$m-01";
            $where->between('pipl_date', $sm1, $sm2);
            
            $select->where($where);
        }
        $select->order('emp_uname, pipl_date, ctr_no');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
	
	
					
	
	public function fetchAll(array $ar = null) {
        //$select = new Select($this->table);
        if( is_array($ar) ) {
            //$where = new Where();
            $y = $ar['yer']? sprintf('%04d',$ar['yer']): date('Y');
            $m = $ar['mon']? sprintf('%02d',$ar['mon']): date('m');
            $sm1 = "$y-$m-01";
            if( $m == '12' ) {
                $y = sprintf('%04d',$y+1); $m = '01';
            }
            else $m = sprintf('%02d',$m+1);
            
            $sm2 = "$y-$m-01";
            //$where->between('pipl_date', $sm1, $sm2);
            
            //$select->where($where);
        }
        //$select->order('emp_uname, pipl_date, ctr_no');
        //$resultSet = $this->selectWith($select);
		
		$resultSet = $this->adapter->query('select * from vinterviewrpt where pipl_date between ? and ? UNION select * from vinterviewrpt3 where pipl_date between ? and ? order by emp_uname, pipl_date, ctr_no',
            array($sm1, $sm2, $sm1, $sm2));			
			$ar = array();
			/*if($resultSet)
			{			
				foreach ($resultSet as $row) {
				}
			}*/
        return $resultSet;
    }

}
