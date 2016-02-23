<?php
// module/Pinnacle/src/Pinnacle/Model/Report/FulistTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class FulistTable extends ReportTable
{
    protected $orders = array(
        'fu_invoice desc, ctct_name',           //0
        'ctct_name, cli_id',                    //1
        'ctct_st_code, ctct_addr_c, ctct_name', //2
        'fu_status, ctct_name, cli_id',         //3
        'fu_acct, ctct_name',                   //4
        'fu_date desc, ctct_name',              //5
        'fu_length, ctct_name, cli_id',         //6
        'fu_renewal desc, ctct_name',           //7
        'fu_start desc, ctct_name',             //8
        'fu_amount, ctct_name, cli_id',         //9
        );
    protected $orderStrings = array(
        'Invoice Date, Facility', //0
        'Facility',               //1
        'State, City, Facility',  //2
        'Status, Facility',       //3
        'Account, Facility',      //4
        'Date Signed, Facility',  //5
        'Length, Facility',       //6
        'Renewal Date, Facility', //7
        'Start Date, Facility',   //8
        'Amount, Facility',       //9
        );

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vfuzionlist',
            '`cli_id`, `ctct_name`, `ctct_addr_c`, `ctct_st_code`, `cli_fuzion`, `fu_client`, `fu_acct`, `fu_date`, `fu_length`, `fu_renewal`, `fu_invoice`, `fu_status`, `fu_start`, `fu_amount`');
    }

    public function fetchAll($ord = 0) {
        $ord = (int) $ord; if( $ord > 9 ) $ord = 0;
        $select = new Select($this->table);
        $select->order($this->orders[$ord]);
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
    
    public function getResort(AbstractController $ctrl, $id = 0) {
        $res = $this->fetchAll($id);
        $a = array();
        $fuss = array('Inactive','Active','Expired','N/A','Canceled',);
        $vm = new PhpRenderer();
        $urls = $ctrl->url();
        if( $res ) foreach ($res as $row) {
            if( $row->cli_id == 6 ) continue;

            $ar = array();
            $row->fu_status = $fuss[$row->fu_status] . ($row->cli_id == $row->fu_client?' (M)':'');
            $row->fu_date = $row->formatDate($row->fu_date);
            $row->fu_renewal = $row->fu_renewal? $row->formatDate($row->fu_renewal): '';
            $row->fu_invoice = $row->formatDate($row->fu_invoice);
            $row->fu_start = $row->formatDate($row->fu_start);
            foreach ($row->getArrayCopy() as $k=>$v)
                $ar[$k] = $vm->escapeHtml($v);
            $ar['amostr'] = $row->formatMoney($row->fu_amount);
            $ar['url'] = $urls->fromRoute('client', array('action'=>'view', 'part'=>'go', 'id' => $row->cli_id));
            $a[] = $ar;
        }
        else $a[] = 'No records in the range';
        return $a;
    }

    public function getOrderString($ord = 0) {
        $ord = (int) $ord; if( $ord > 9 ) $ord = 0;
        return $this->orderStrings[$ord];
    }
    public function getOrderStrings() {
        return $this->orderStrings;
    }

}
