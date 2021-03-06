<?php
// module/Pinnacle/src/Pinnacle/Model/Goal.php:
/* this module is a representation of a database record;
 * for form results, see Goals class.
 * Sorry
 * */
namespace Pinnacle\Model\Admin;

class Goal {
    public $g_emp_id = 0;
    public $g_year = 0;
    public $g_month = 0;
    public $g_class = 0;
    public $g_1 = 0;
    public $g_2 = 0;
    public $g_3 = 0;
    public $g_4 = 0;
    public $g_1ytd = 0;
    public $g_2ytd = 0;
    public $g_3ytd = 0;
    public $g_4ytd = 0;

    public function exchangeArray($data) {
        $this->g_emp_id = (isset($data['g_emp_id'])) ? $data['g_emp_id']: 0;
        $this->g_year   = (isset($data['g_year']))   ? $data['g_year']  : 0;
        $this->g_month  = (isset($data['g_month']))  ? $data['g_month'] : 0;
        $this->g_class  = (isset($data['g_class']))  ? $data['g_class'] : 0;
        $this->g_1  = (isset($data['g_1']))  ? $data['g_1'] : 0;
        $this->g_2  = (isset($data['g_2']))  ? $data['g_2'] : 0;
        $this->g_3  = (isset($data['g_3']))  ? $data['g_3'] : 0;
        $this->g_4  = (isset($data['g_4']))  ? $data['g_4'] : 0;
        $this->g_1ytd  = (isset($data['g_1ytd']))  ? $data['g_1ytd'] : 0;
        $this->g_2ytd  = (isset($data['g_2ytd']))  ? $data['g_2ytd'] : 0;
        $this->g_3ytd  = (isset($data['g_3ytd']))  ? $data['g_3ytd'] : 0;
        $this->g_4ytd  = (isset($data['g_4ytd']))  ? $data['g_4ytd'] : 0;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
}
