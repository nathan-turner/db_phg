<?php
// module/Pinnacle/src/Pinnacle/Model/Report/RecordGen.php:
// Generic record object.
namespace Pinnacle\Model\Report;

class RecordGen {
    protected $fieldGen = array();
    static protected $nf = null;
    static protected $nm = null;
    
    public function __construct(array $fields = null) {
        if( is_array($fields) ) {
            foreach ($fields as $field)
                $this->fieldGen[trim($field," `\t\n\r\0")] = null;
        }
    }
    
    public function __get($name) {
        return $this->fieldGen[$name];
    }
    public function __set($name,$value) {
        $this->fieldGen[$name] = $value;
    }

    public function exchangeArray(array $data) {
        $this->fieldGen = $data;
    }

    public function getArrayCopy() {
        return $this->fieldGen;
    }
    
    static public function formatPhone($field) {
        if( ! self::$nf ) {
            self::$nf = new \NumberFormatter('en_US', \NumberFormatter::PATTERN_DECIMAL, "###,###,###,###,####");
            self::$nf->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL,'-');
        }
        return self::$nf->format($field, \NumberFormatter::TYPE_INT64);
    }

    static public function formatMoney($field) {
        if( ! self::$nm ) {
            self::$nm = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            self::$nm->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS,0);
        }
        return self::$nm->formatCurrency($field, 'USD');
    }
    
    static public function formatDate($field) {
        return substr($field,0,10);
    }
}    
