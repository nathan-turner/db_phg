<?php
// module/Pinnacle/src/Pinnacle/Model/Cleanup.php:
namespace Pinnacle\Model\Admin;

class Cleanup {
    public $id;
    public $no;
    public $part;
    public $name;
    public $city;
    public $state;
    public $spec;
    
    protected static $typesArray = array(
        'cli' => 'Client', 'ctr' => 'Contract',
        'phy' => 'Physician', 'mid' => 'Midlevel',
    );

    public function exchangeArray($data) {
        $this->id    = (isset($data['id']))    ? $data['id']    : null;
        $this->no    = (isset($data['no']))    ? $data['no']    : null;
        $this->part  = (isset($data['part']))  ? $data['part']  : null;
        $this->name  = (isset($data['name']))  ? $data['name']  : null;
        $this->city  = (isset($data['city']))  ? $data['city']  : null;
        $this->state = (isset($data['state'])) ? $data['state'] : null;
        $this->spec  = (isset($data['spec']))  ? $data['spec']  : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
    public function getPart() {
        return self::$typesArray[$this->part];
    }

    public static function getTypes($index = null) {
        return $index? self::$typesArray[$index]: self::$typesArray;
    }
}