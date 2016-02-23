<?php
// module/Pinnacle/src/Pinnacle/Model/Goals.php:
/* this module is a representation of form results;
 * for a database record, see Goal class.
 * Sorry
 * */
namespace Pinnacle\Model\Admin;

class Goals {
    public $g_emp_id;
    public $g_year;
    public $man_mode;
    public $g_class;
    public $goal = array( null,array(),array(),array(),array() );

    public function exchangeArray($data) {
        $this->g_emp_id     = (isset($data['g_emp_id']))   ? $data['g_emp_id']   : 0;
        $this->g_year   = (isset($data['g_year'])) ? $data['g_year'] : 0;
        $this->man_mode   = (isset($data['man_mode'])) ? $data['man_mode'] : null;
        $this->g_class  = (isset($data['g_class']))? $data['g_class']: 0;
        for( $i = 1; $i <= 4; $i++ ) 
            for( $j = 1; $j <= 12; $j++ )
                $this->goal[$i][$j] = (isset($data["g_${i}_$j"])) ? $data["g_${i}_$j"] : 0;
    }

    public function getArrayCopy() {
        $ar = array();
        $ar['g_emp_id'] = $this->g_emp_id;
        $ar['g_year'] = $this->g_year;
        $ar['man_mode'] = $this->man_mode;
        $ar['g_class'] = $this->g_class;
        for( $i = 1; $i <= 4; $i++ ) 
            for( $j = 1; $j <= 12; $j++ )
                $ar["g_${i}_$j"] = $this->goal[$i][$j];
        return $ar;
    }
    
    public function getFromGoal(Goal $go) {
        $this->g_emp_id = $go->g_emp_id;
        $this->g_class = $go->g_class;
        $this->g_year = $go->g_year;
        $this->goal[1][$go->g_month] = $go->g_1;
        $this->goal[2][$go->g_month] = $go->g_2;
        $this->goal[3][$go->g_month] = $go->g_3;
        $this->goal[4][$go->g_month] = $go->g_4;
    }

    public function setToGoal($month,Goal $go,Goal $ytd) {
        $go->g_emp_id = $this->g_emp_id;
        $go->g_class = $this->g_class;
        $go->g_year = $this->g_year;
        $go->g_month = $month;
        $go->g_1 = $this->goal[1][$month];
        $ytd->g_1 += $go->g_1; $go->g_1ytd = $ytd->g_1;
        $go->g_2 = $this->goal[2][$month];
        $ytd->g_2 += $go->g_2; $go->g_2ytd = $ytd->g_2;
        $go->g_3 = $this->goal[3][$month];
        $ytd->g_3 += $go->g_3; $go->g_3ytd = $ytd->g_3;
        $go->g_4 = $this->goal[4][$month];
        $ytd->g_4 += $go->g_4; $go->g_4ytd = $ytd->g_4;
        return $go;
    }
}