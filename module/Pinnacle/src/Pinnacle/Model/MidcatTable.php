<?php
// module/Pinnacle/src/Pinnacle/Model/MidcatTable.php:

namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\View\Renderer\PhpRenderer;

class MidcatTable extends Report\ReportTable
{
    protected $ata = array();
    protected $atb = array();
    protected $atc = 0;
    protected $nobsp = array("\xC2\xA0\xC2\xA0\xC2\xA0 ","\xC2\xA0\xC2\xA0 ","\xC2\xA0 ",' ');
    
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'dctalliedtypes',
                            '`at_code`, `at_name`, `at_sort`, `at_abbr`, `at_select`');
    }

    public function fetchAll() {
        $select = new Select($this->table);
        $select->order('at_code');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }

    public function getSelectOptions($option = 0) {
        if( $option ) return $this->getCatSelect();
        $resultSet = $this->fetchAll();
        $ar = array();
        foreach ($resultSet as $spec) {
            $sp = trim($spec->at_code);
            $ar[$sp] = str_repeat("\xC2\xA0", strlen($sp)). $spec->at_name . ' ('. $spec->at_abbr .')';
        }
        return $ar;
    }
    
    protected function initCats() {
        $resultSet = $this->fetchAll();
        $atc = 0;
        $vm = new PhpRenderer();
        foreach ($resultSet as $spec) {
            $sp = $vm->escapeHtml(trim($spec->at_code));
            if( $spec->at_select ) {
                $atc++;
                $this->ata[$sp] = str_repeat("\xC2\xA0", strlen($sp)). $spec->at_name . ' ('. $spec->at_abbr .')';
                $this->atb[$atc] = "<option value=\"$sp\"> (the selected category itself)</option>";
            }
            else
                $this->atb[$atc] .= "<option value=\"$sp\">".str_repeat("\xC2\xA0", strlen($sp)). $vm->escapeHtml($spec->at_name . ' ('. $spec->at_abbr) .')</option>';
        }
        $this->atc = $atc;
    }

    public function getCatSelect() { // returns option array for Zend\Form\Element\Select
        if( !$this->atc ) {
            $this->initCats();
        }
        return $this->ata;
    }

    public function getCatCount() { // returns count for .js
        if( !$this->atc ) {
            $this->initCats();
        }
        return $this->atc;
    }

    public function getCatLists($id = 'atb_sel_') { // returns bunch of selects
        if( !$this->atc ) {
            $this->initCats();
        }
        $atb = '';
        for( $i = 1; $i <= $this->atc; $i++ )
            $atb .= "<select id=\"$id$i\">".$this->atb[$i].'</select>'.PHP_EOL;
        return $atb;
    }

}
