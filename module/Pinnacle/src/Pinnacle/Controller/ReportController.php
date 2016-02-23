<?php
// module/Pinnacle/src/Pinnacle/Controller/ReportController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\DateForm;
use Pinnacle\Form\Report\PlacementForm;
use Pinnacle\Form\Report\PlacemonthForm;
use Pinnacle\Form\Report\MarketlogForm;
use Pinnacle\Model\Utility;
use Pinnacle\Form\Report\InterviewForm;

class ReportController extends AbstractActionController
{
    protected $retainedTable;
    protected $retainedMacTable;
    protected $specdemoTable;
    protected $statisticsTable;
    protected $monmorTable;
    protected $phoneTable;
    protected $usersTable;
    protected $specialtyTable;
    protected $placementTable;
    protected $placemonthTable;
    protected $marketlogTable;
    protected $interviewTable;
	protected $reportingTable;
	protected $contractsTable;
	protected $physiciansTable;
	protected $clientsTable;
	protected $bookingTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $part = $this->params()->fromRoute('part', 'common');

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        
        return array('phguser' => $identity,
            'part' => $part, 'messages' => $messages );
    }
    public function listAction() {
        return $this->indexAction(); // the same action but different template
    }

    public function phoneAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
        $part = $this->params()->fromRoute('part', 'yest');

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $newd = strtotime('yesterday');
        $date = date('Y-m-d',$newd);
        $form = new DateForm(1, array('label1' => 'Jump to date:', 'submit' => 'GO',
                                      'max'=> $date,  ));
        if( $prg !== false ) {
            $form->setData($prg);
    
            if ($form->isValid()) {
                $ar = $form->getData();
                $pastd = $ar['date1'];
                $newd = strtotime($pastd);
                if( $newd ) $date = date('Y-m-d',$newd);
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Date entered is not valid';
            }
        }

        $table = $this->getPhoneTable();
        $report = $part !== 'today'? $table->fetchDate($date): $table->fetchAll();
        $date = date('m/d/Y',$newd);
        return array('phguser' => $identity,
            'report' => $report, 'form' => $form,
            'date' => $date, 'part' => $part, 'messages' => $messages );
    }

    public function interviewAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = array('yer'=>date('Y'),'mon'=>date('n'));
        $part = $this->params()->fromRoute('part', 'Y'.date('Y'));
        $id = (int) $this->params()->fromRoute('id', 0);
        $yer = (int) substr($part,1,4);
        if( $yer && is_numeric($yer) ) $ar['yer'] = $yer;
        else $part = 'Y'.$ar['yer'];
        if( $id > 0 && $id < 13 ) $ar['mon'] = $id;
        else $id = $ar['mon'];
        
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        
        $request = $this->getRequest();
        $form = new InterviewForm($months);
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ar = $form->getData();
                $part = 'Y'.$ar['yer'];
                if( !$ar['mon'] ) $ar['mon'] = date('n');
                $id = $ar['mon'];
                return $this->redirect()->toRoute('report',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }

        return array('phguser' => $identity, 'part' => $part, 'months' => $months,
            'report' => $this->getInterviewTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages, 'id' => $id );
    }
    
    public function marketlogAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $part = $this->params()->fromRoute('part', 'sort');
        $ar = array('yer'=>date('Y'));
        if( $part == 'mark' ) $ar['ord'] = $part;
        
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        
        $form = new MarketlogForm($this->getUsersTable());
        if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }

        return array('phguser' => $identity, 'part' => $part, 'months' => $months,
            'report' => $this->getMarketlogTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }
    
    public function placemonthAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = array('yer'=>date('Y'));
        
        $form = new PlacemonthForm($this->getSpecialtyTable());
        if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }

        return array('phguser' => $identity,
            'report' => $this->getPlacemonthTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }

    public function placementAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = null;
        $form = new PlacementForm($this->getSpecialtyTable());
        if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }
		//$ar['date1']=$ar['date1txt'];
		//$ar['date2']=$ar['date2txt'];
        return array('phguser' => $identity,
            'report' => $this->getPlacementTable()->fetchAll($ar), 'form' => $form, 
            'messages' => $messages );
    }
	
	public function placement2Action() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = null;
        $form = new PlacementForm($this->getSpecialtyTable());
        if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }
		$arr = $this->getPlacementTable()->getPlacementReport($id); 

        return array('phguser' => $identity,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/ 'form' => $form, 'arr'=>$arr,
            'messages' => $messages );
    }
	
	public function usersumstAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		$part = $this->params()->fromRoute('part', 'this');
		
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = null;
        $form = new PlacementForm($this->getSpecialtyTable());
        /*if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/
		//$arr = $this->getPlacementTable()->getPlacementReport($id); 
		
		$rs = $this->getPlacementTable()->getCalls($part); 
		$gl = $this->getPlacementTable()->getGoals($part); 
		$gt = $this->getPlacementTable()->getGoalsTotal($part); 
		if($part=='prev'){
			$ldate = date('F , Y',strtotime("-1 months"));
			$coldate = date('M',strtotime("-1 months"));
		}
		else
		{
			$ldate = date('F , Y');
			$coldate = date('M');
		}

        return array('phguser' => $identity, 'coldate'=>$coldate, 'part'=>$part,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/ 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'rs'=>$rs, 'gl'=>$gl, 'gt'=>$gt,
            'messages' => $messages );
    }
	
	
	public function usersumst2Action() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $prg = $this->prg();
        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		$part = $this->params()->fromRoute('part', 'this');
		
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = null;
        $form = new PlacementForm($this->getSpecialtyTable());
        /*if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/
		//$arr = $this->getPlacementTable()->getPlacementReport($id); 
		
		$rs = $this->getPlacementTable()->getCalls($part); 
		$gl = $this->getPlacementTable()->getGoals($part); 
		$gt = $this->getPlacementTable()->getGoalsTotal($part); 
		if($part=='prev'){
			//$ldate = date('F , Y',strtotime("-1 months"));
			$date=date_create(date('Y-m-d'));
			date_sub($date,date_interval_create_from_date_string("1 month"));
			$ldate = date_format($date,"Y-m-d");
			$coldate = date('M',strtotime("-1 months"));
		}
		else
		{
			$ldate = date('F , Y');
			$coldate = date('M');
		}
		$pl = $this->getPlacementTable()->getPasses($part); 
		$wpl = $this->getPlacementTable()->getWeeklyPasses($part);
		$plt = $this->getPlacementTable()->getPltPresents($part);
		$lt = $this->getBookingTable()->getLocumsStats();		
		$pc = $this->getBookingTable()->getPCStats();	

        return array('phguser' => $identity, 'coldate'=>$coldate, 'part'=>$part, 'pl'=>$pl, 'plt'=>$plt, 'lt'=>$lt, 'pc'=>$pc, 'wpl' => $wpl,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/ 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'rs'=>$rs, 'gl'=>$gl, 'gt'=>$gt,
            'messages' => $messages );
    }
	
	

    public function retainedAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $part = $this->params()->fromRoute('part', 'sort');
        $id = (int) $this->params()->fromRoute('id', 0);

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $table = $part === 'mac'? $this->getRetainedMacTable(): $this->getRetainedTable();
        return array('phguser' => $identity,
            'report' => $table->fetchAll($id), 'order' => $table->getOrderStrings(),
            'id' => $id, 'part' => $part, 'messages' => $messages );
    }
    
    public function fulistAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $id = (int) $this->params()->fromRoute('id', 0);

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        return array('phguser' => $identity,
            'id' => $id, 'messages' => $messages );
    }
    
    public function specdemoAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $part = $this->params()->fromRoute('part', 'list');
        $id = (int) $this->params()->fromRoute('id', 0);

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $request = $this->getRequest();
        $demo = $this->getSpecdemoTable()->fetchAll();

        if ($request->isPost()) {
            $sid = $request->getPost('sid', '0');
            $sid = (int) $sid;
            if( $sid ) $id = $sid;
        }
        $report = $this->getSpecdemoTable()->getDemo($id);
                
        return array('phguser' => $identity,
            'report' => $report, 'demo' => $demo,
            'id' => $id, 'part' => $part, 'messages' => $messages );
    }
    
    public function statisticsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $part = $this->params()->fromRoute('part', 'sel0');
        $id = (int) $this->params()->fromRoute('id', 19);

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $sid = $request->getPost('snum', '19');
            $sid = (int) $sid;
            if( $sid ) $id = $sid;
        }
        $report = $this->getStatisticsTable()->fetchAll($part, $id);
        /*  StatisticsTable is not really a table,
         *  and not even a TableGateway, but iface is the same
         *  for convenience.
         *  Returns object, and view template knows about it
         *  */
                
        return array('phguser' => $identity,
            'report' => $report,
            'id' => $id, 'part' => $part, 'messages' => $messages );
    }
    
    public function monmorAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $report = $this->getMonmorTable()->fetchAll();
        // MonmorTable is not really a table either.

        return array('phguser' => $identity, 'report' => $report, 'messages' => $messages );
    }
	
	
	public function rptdataentryAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
        
        /*if( $prg !== false ) {
            $form->setData($prg);
            if ($form->isValid()) 
                $ar = $form->getData();
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/
		$ar = $this->getReportingTable()->getDataEntryStats($_POST); 
		/*if(isset($_POST["submitdate"])||isset($_POST)){
		echo "HERE****************!!!!!!!!!!";
		echo $_POST["startdate"];
		}*/
		//echo $_POST["startdate"];
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);
        return array('phguser' => $identity,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/'ar'=>$ar, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'rs'=>$rs, 'gl'=>$gl, 'gt'=>$gt,
            'messages' => $messages );
    }
	
	public function fuzionexportAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
        
        if(isset($_POST["removebtn"])){			
			$result = $this->getReportingTable()->updateExportList($_POST); 
		}
			
		$ar = $this->getReportingTable()->getFuzionExportList($_GET["V2"]); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);
        return array('phguser' => $identity,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/'ar'=>$ar, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'rs'=>$rs, 'gl'=>$gl, 'gt'=>$gt,
            'messages' => $messages );
    }
	
	public function rptaccsummAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		
        $specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
		
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
        if(isset($_POST["sortbtn"])){			
			//$result = $this->getReportingTable()->updateExportList($_POST); 
		}
			
		$ar = $this->getReportingTable()->getAccountSummary($_POST); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptpresentsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		
        //$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		//$rec = $this->getContractsTable()->getRecruiters();
		
		
        if(isset($_POST["sortbtn"])){			
			//$result = $this->getReportingTable()->updateExportList($_POST); 
		}
			
		$ar = $this->getReportingTable()->getPresentsReport($_POST); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptplacemonthAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		
        $specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		//$rec = $this->getContractsTable()->getRecruiters();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		
		
			
		$ar = $this->getReportingTable()->getPlaceMonthReport($_POST); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rpthotlistsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		if(!isset($_GET["mark"]))
			$_GET["mark"]=$_POST["mark"];
		$market = $_GET["mark"];
		if(!isset($_GET["filter"]))
			$_GET["filter"]=$_POST["filter"];
		$filter = $_GET["filter"];
		
		//echo $_POST["empid"];
		if($market=="yes"){
			$emps = $this->getClientsTable()->getMarketers();
			$table = $this->getReportingTable()->getHotLists($_POST, $market, $filter); 
			//echo "HERE";
			
		}	
		else{
			$emps = $this->getContractsTable()->getRecruiters();
			$table = $this->getReportingTable()->getRecruiterHotLists($_POST, $market, $filter); 
		}
		
				
		//echo var_dump($emps);
        //$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		//$rec = $this->getContractsTable()->getRecruiters();
		//$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		
		
			
		//$ar = $this->getReportingTable()->getPlaceMonthReport($_POST); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'emps'=>$emps, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptsrciscAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		$ordr = $_GET["ordr"];
		if($ordr=='' || $ordr<0)
			$ordr=0;
   
	switch($ordr){	
	case 1:
		$sordr = "ctr_date, emp_uname";
		$dordr = "Contract Date, Recruiter";
		break;
	case 2:
		$sordr = "emp_uname, ctr_date";
        $dordr = "Recruiter, Date";
		break;
	case 3:
		$sordr = "ctr_spec, emp_uname, ctr_date";
        $dordr = "Specialty, Recruiter, Date";
		break;
	case 4:
		$sordr = "ctr_location_c, ctr_location_s, ctct_name, ctr_date";
        $dordr = "City, State, Facility, Date";
		break;
	case 5:
		$sordr = "ctr_location_s, ctr_location_c, ctct_name, ctr_date";
        $dordr = "State, City, Facility, Date";
		break;
	case 6:
		$sordr = "ctct_name, ctr_date";
        $dordr = "Facility, Date";
		break;
	default:
		$sordr = "ctr_no";
		$dordr = "Contract #";
		break;
	}//end switch	
		
		$arr = $this->getReportingTable()->getISCReport($sordr,$dordr);
		
		//echo var_dump($arr);
       
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'sordr'=>$sordr, 'dordr'=>$dordr,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptspeclistAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		
        $specs = $this->getPhysiciansTable()->getSpecialtyOptions();		
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		$mid = $this->getContractsTable()->getMidlevelOptions();		
			
		//$ar = $this->getReportingTable()->getPlaceMonthReport($_POST); 	
		
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptmeetingsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$emps = $this->getClientsTable()->getMarketers();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
					
		$table = $this->getReportingTable()->getMarketingMeetingsReport($_POST); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'emps'=>$emps, 'table'=>$table,  'states'=>$states,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rpthotdoclimitedAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		$sort = $_GET["sort"];
		if(!isset($_POST["submitbtn"]))
			$_POST=$_GET;
		
		$startdttxt = $_POST["startdate"];
		$enddttxt = $_POST["enddate"];
		//echo $startdttxt;
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$emps = $this->getClientsTable()->getMarketers();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
					
		$table = $this->getReportingTable()->getHOTDocReport($_POST, $sort); 
		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'emps'=>$emps, 'table'=>$table,  'states'=>$states, 'startdttxt' => $startdttxt, 'enddttxt' => $enddttxt,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptclientlistAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		
        //$specs = $this->getPhysiciansTable()->getSpecialtyOptions();		
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		//$mid = $this->getContractsTable()->getMidlevelOptions();		
		$marketers = $this->getClientsTable()->getMarketers();		
			
		//$ar = $this->getReportingTable()->getPlaceMonthReport($_POST); 	
		
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importdocAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importDocs($_POST); 	
			//echo "SUBMIT";			
		}
		
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importnurAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importMidlevels($_POST); 	
			echo "SUBMIT";			
		}
		
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importdoccafeAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importDocCafe($_POST); 	
			//echo "SUBMIT";			
		}
		
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importdocslistAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importDocsList($_POST); 	
			//echo "SUBMIT";			
		}
		
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importamgaAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		//$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importAMGA($_POST); 	
			//echo "SUBMIT";			
		}
		
		
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function importspecdemoAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		//$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		
		if(isset($_POST["submit"]))
		{
			$table = $this->getReportingTable()->importSpecDemo($_POST); 	
			//echo "SUBMIT";			
		}
		
		
			
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid, 'mr'=>$marketers, 'table'=>$table,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function monthlyacctreviewAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$sort=$_GET["sort"];
					
		$ar = $this->getReportingTable()->getMonthAcctReview($sort); 	
		
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function pltpresentsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$sort=$_GET["sort"];
					
		$ar = $this->getReportingTable()->getPltPresents($sort); 	
		
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'states'=>$states, 'mid'=>$mid,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function clientActivityAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
		$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$sort=$_GET["sort"];
					
		if(isset($_POST["comments_submit"]))
			$this->getReportingTable()->addClientActivityComments($_POST, $id); 
		
		$ar = $this->getReportingTable()->getClientActivity($id, $_POST); 	
		
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr,  'messages' => $messages );
    }
	
	public function rptsyspresentsAction() { //system activity
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
				
		$table = $this->getReportingTable()->getSysPresentsReport($_POST); 		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'emps'=>$emps, 'table'=>$table,  'states'=>$states, 'demo'=>$demo, 'specs'=>$specs,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptsyspresents2Action() { //monthly activity
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
		
		//$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
				
		$table = $this->getReportingTable()->getMonthPresentsReport($_POST); 		
		
		//$form = new PlacementForm($this->getSpecialtyTable());
		$form = new InterviewForm($months);		
		
        return array('phguser' => $identity, 'emps'=>$emps, 'table'=>$table,  'states'=>$states, 'demo'=>$demo, 'specs'=>$specs,
            'ar'=>$ar, 'rec'=>$rec, 'specs'=>$specs, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'messages' => $messages );
    }
	
	public function rptfuexportAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        //$prg = $this->prg();
        //if ($prg instanceof \Zend\Http\PhpEnvironment\Response) return $prg;
		//$id = (int) $this->params()->fromRoute('id', 0);
		
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $ar = null;
        
       
		$table = $this->getReportingTable()->getRejectedFuzion($_GET); 
			
		
		//$form = new InterviewForm($months);
        return array('phguser' => $identity, 'table'=>$table,
            /*'report' => $this->getPlacementTable()->fetchAll($ar),*/'ar'=>$ar, 'form' => $form, 'arr'=>$arr, 'ldate'=>$ldate, 'rs'=>$rs, 'gl'=>$gl, 'gt'=>$gt,
            'messages' => $messages );
    }

    public function getRetainedTable() {
        return $this->reportTableFactory('retainedTable');
    }
    public function getRetainedMacTable() {
        return $this->reportTableFactory('retainedMacTable');
    }
    public function getSpecdemoTable() {
        return $this->reportTableFactory('specdemoTable');
    }
    public function getStatisticsTable() {
        return $this->reportTableFactory('statisticsTable');
    }
    public function getMonmorTable() {
        return $this->reportTableFactory('monmorTable');
    }
    public function getPhoneTable() {
        return $this->reportTableFactory('phoneTable');
    }
    public function getUsersTable() {
        return $this->reportTableFactory('usersTable','');
    }
    public function getSpecialtyTable() {
        return $this->reportTableFactory('specialtyTable','');
    }
    public function getPlacementTable() {
        return $this->reportTableFactory('placementTable');
    }
	public function getReportingTable() {
        return $this->reportTableFactory('reportingTable');
    }
    public function getPlacemonthTable() {
        return $this->reportTableFactory('placemonthTable');
    }
    public function getMarketlogTable() {
        return $this->reportTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->reportTableFactory('interviewTable');
    }
	public function getContractsTable() {
        return $this->reportTableFactory('contractsTable', '');
    }
	public function getPhysiciansTable() {
        return $this->reportTableFactory('physiciansTable', '');
    }
	public function getClientsTable() {
        return $this->reportTableFactory('clientsTable', '');
    }
	public function getBookingTable() {
        return $this->reportTableFactory('bookingTable', '');
    }

    protected function reportTableFactory($table,$submodel = 'Report\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
