<?php
// module/Pinnacle/src/Pinnacle/Model/Report/RetainedTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class RetainedTable extends ReportTable
{
    protected $orders = array(
        'ctr_spec, ctct_st_code, ctct_addr_c, rec_uname, ctct_name', //0
        'ctr_no',                                                    //1
        'rec_uname, ctct_st_code, ctct_addr_c, ctct_name, ctr_spec', //2
        'ctct_addr_c, ctct_st_code, ctct_name, ctr_spec',            //3
        'ctct_st_code, ctct_addr_c, ctr_spec, ctct_name',            //4
        'ctct_name, ctr_spec',                                       //5
        'mark_uname, ctct_st_code, ctct_addr_c, ctct_name, ctr_spec',//6
        'cli_rating desc, mark_uname, ctct_st_code, ctct_addr_c, ctct_name, ctr_spec',//7
        );
    protected $orderStrings = array(
        'Specialty, State, City',        //0
        'Contract #',                    //1
        'Recruiter, State, City',        //2
        'City, State, Facility',         //3
        'State, City, Specialty',        //4
        'Facility, Specialty',           //5
        'Marketer, State, City',         //6
        'Rating, Marketer, City',        //7
        );

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vretainedsearch',
            'ctr_id,ctr_no,ctr_spec,rec_uname,cli_id,ctct_addr_c,ctct_st_code, ctct_name,mark_uname,cli_rating,ctr_type,rec_status,mark_status,ctr_nurse,at_abbr');
    }

    public function fetchAll($ord = 0) {
        $ord = (int) $ord; if( $ord > 7 ) $ord = 0;
        $select = new Select($this->table);
        $select->order($this->orders[$ord]);
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
    
    public function getResort(AbstractController $ctrl, $id = 0) {
        $res = $this->fetchAll($id);
        $a = array();
        $vm = new PhpRenderer();
        $urls = $ctrl->url();
        if( $res ) foreach ($res as $row) {
            if( $row->cli_id == 6 ) continue;
            $ar = array();

            if( $row->ctr_nurse ) $row->ctr_spec = $row->at_abbr? $row->at_abbr: 'midl.';
            $row->ctr_no .= ' ('.trim($row->ctr_type).')';
            if( is_null($row->cli_rating) ) $row->cli_rating = '';
            $ar['ruclass'] = $row->rec_status? '':'class="text-error"';
            $ar['marclass'] = $row->mark_status? '':'class="text-error"';
            $ar['url'] = $urls->fromRoute('contract', array('action'=>'view', 'part'=>'go', 'id' => $row->ctr_id));
            $ar['cliurl'] = $urls->fromRoute('client', array('action'=>'view', 'part'=>'go', 'id' => $row->cli_id));
            $ar['prourl'] = $urls->fromRoute('contract', array('action'=>'profile', 'part'=>'go', 'id' => $row->ctr_id));

            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }

    public function getOrderString($ord = 0) {
        $ord = (int) $ord; if( $ord > 7 ) $ord = 0;
        return $this->orderStrings[$ord];
    }
    public function getOrderStrings() {
        return $this->orderStrings;
    }

}
