<?php
// module/Pinnacle/src/Pinnacle/Model/Users.php:
namespace Pinnacle\Model\Admin;

class Users {
    public $emp_id;
    public $emp_uname;  // and ctct_email
    public $emp_dept;
    public $emp_status;
    public $emp_realname;
    public $emp_admin;
    public $emp_access; // emp_admin+1 normally

    public function exchangeArray($data) {
        $this->emp_id      = (isset($data['emp_id']))      ? $data['emp_id']      : null;
        $this->emp_uname   = (isset($data['emp_uname']))   ? $data['emp_uname']   : null;
        $this->emp_dept    = (isset($data['emp_dept']))    ? $data['emp_dept']    : null;
        $this->emp_status  = (isset($data['emp_status']))  ? $data['emp_status']  : 1;
        $this->emp_realname= (isset($data['emp_realname']))? $data['emp_realname']: null;
        $this->emp_admin   = (isset($data['emp_admin']))   ? $data['emp_admin']   : 0;
        $this->emp_access  = (isset($data['emp_access']))  ? $data['emp_access']  :
            ( (isset($data['emp_admin']))   ? ((int) $data['emp_admin'])+1 : 1 );
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
}