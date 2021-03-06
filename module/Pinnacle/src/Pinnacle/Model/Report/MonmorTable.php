<?php
// module/Pinnacle/src/Pinnacle/Model/Report/MonmorTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class Monmor {
    public $monday;
    public $meetings;
    public $pendings;
    public $presents;
}


class MonmorTable
{
    protected $adapter;
    
    const S0Q2 = 'select * from vclimeetings where cm_cancel = 0 and cm_date between ? and ?  order by emp_realname, cm_nomeet, cm_date';
    const S0Q3 = 'select * from vrecpendings union select * from vrecpendings3 ORDER BY emp_realname, ctct_name, ph_id';
    const S0Q4 = 'select count(*) as cnt from tctrpipl where pipl_status in (2,8) and pipl_cancel = 0 and pipl_date between ? and ?';


    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function fetchAll() {
        $obj = new Monmor();
        $ad = $this->adapter;
        
        $dt = getdate();
        switch ($dt['wday']) {
            case 1: $str = 'today'; break;
            case 2: $str = 'yesterday'; break;
            default: $str = 'Monday';
        }
        $monday = strtotime($str);
        // debug:
        //$monday = strtotime('2012-07-16');
        
        $obj->monday = date('n/d/Y',$monday);
        $dd2 = date('Y-m-d',$monday);
        $dd1 = date('Y-m-d',strtotime('Monday last week',$monday)); 
        $dd3 = date('Y-m-d',strtotime('Monday next week',$monday)); 
        
        $obj->meetings = $ad->query(self::S0Q2, array($dd1,$dd3));
        $obj->pendings = $ad->query(self::S0Q3)->execute();
        
        $day1 = date('Y-m-d',strtotime('First day of this month',$monday));
        $day2 = date('Y-m-d',strtotime('First day of next month',$monday));

        $rowSet = $ad->query(self::S0Q4, array($day1,$day2));
        foreach ($rowSet as $rec)
            $obj->presents = $rec['cnt'];

        return $obj;
    }
}
