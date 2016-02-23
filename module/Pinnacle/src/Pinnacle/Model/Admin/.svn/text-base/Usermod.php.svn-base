<?php
// module/Pinnacle/src/Pinnacle/Model/Usermod.php:
namespace Pinnacle\Model\Admin;

class Usermod extends Users {
    public $ctct_name;
    public $ctct_title;
    public $ctct_phone;
    public $ctct_ext1;
    public $ctct_fax;
    public $ctct_cell;
    public $ctct_hphone;
    public $ctct_url;
    public $ctct_addr_1;
    public $ctct_addr_2;
    public $ctct_addr_c;
    public $ctct_addr_z;
    public $ctct_st_code;
    public $password3;


    public function exchangeArray($data) {
        parent::exchangeArray($data);
        $this->ctct_title  = (isset($data['ctct_title']))  ? $data['ctct_title']  : null;
        $this->ctct_name   = (isset($data['ctct_name']))   ? $data['ctct_name']   : null;
        if( isset($data['first_name']) && isset($data['last_name']) )
            $this->ctct_name = $data['last_name'] . ', ' . $data['first_name'];
        $this->ctct_phone  = (isset($data['ctct_phone']))  ? $data['ctct_phone']  : null;
        $this->ctct_ext1   = (isset($data['ctct_ext1']))   ? $data['ctct_ext1']   : null;
        $this->ctct_fax    = (isset($data['ctct_fax']))    ? $data['ctct_fax']    : null;
        $this->ctct_cell   = (isset($data['ctct_cell']))   ? $data['ctct_cell']   : null;
        $this->ctct_hphone = (isset($data['ctct_hphone'])) ? $data['ctct_hphone'] : null;
        $this->ctct_url    = (isset($data['ctct_url']))    ? $data['ctct_url']    : null;
        $this->ctct_addr_1 = (isset($data['ctct_addr_1'])) ? $data['ctct_addr_1'] : null;
        $this->ctct_addr_2 = (isset($data['ctct_addr_2'])) ? $data['ctct_addr_2'] : null;
        $this->ctct_addr_c = (isset($data['ctct_addr_c'])) ? $data['ctct_addr_c'] : null;
        $this->ctct_addr_z = (isset($data['ctct_addr_z'])) ? $data['ctct_addr_z'] : null;
        $this->ctct_st_code= (isset($data['ctct_st_code']))? $data['ctct_st_code']: null;
        $this->password3   = (isset($data['password3']))   ? $data['password3']   : null;
    }
   
}