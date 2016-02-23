<?php
// module/Pinnacle/src/Pinnacle/Controller/ContractController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\ContractsForm;
use Pinnacle\Model\Utility;
use Pinnacle\Form\ViewForm;
use Pinnacle\Form\ContractsmodForm;

class ContractController extends AbstractActionController
{
    protected $usersTable;
    protected $specialtyTable;
    protected $midcatTable;
    protected $placementTable;
    protected $placemonthTable;
    protected $marketlogTable;
    protected $interviewTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        $form = new ContractsForm($this->getUsersTable(),$this->getSpecialtyTable(),$this->getMidcatTable());
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages );
    }

	public function responsesAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		$responses = $this->getContractsTable()->getResponses($id);
		$cnt = $this->getContractsTable()->getResponseCount($id);
		
        
        $request = $this->getRequest();
        //$form = new PhysicianForm();
		$form = new ViewForm();
        $form->setData($responses);
        
        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'ar' => $ar, 'responses' => $responses, 'cnt'=>$cnt,
            /*'contract' => $this->getContractsTable()->fetchAll($ar),*/ 'form' => $form, 
            'messages' => $messages, 'id' => $id );
    }
	
    public function viewAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		$ar = $this->getContractsTable()->selectContract($id);
		if(isset($_POST["profile_submit"])){
			$profileresult = $this->getContractsTable()->SetProfileDate($id, $_POST);	
			//$flashMessenger->addMessage('Success!');
			//echo $_POST["profile_date"];
			$messages="Profile date has been updated successfully";
			return header("location: /public/contract/view/".$id."?message=".$messages." \n");
		}
		$states = array('0' => ' Any State') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        
        
		$contact_status = $this->getClientsTable()->getContactStatus();
		
		$comments = $this->getContractsTable()->getComments($id, $ar["cli_id"]);
		$contacts = $this->getClientsTable()->selectClientContacts($ar["cli_id"]);
		$spec = $this->getContractsTable()->getSpecialties();
		$rec = $this->getContractsTable()->getRecruiters();
		$pipl = $this->getContractsTable()->selectPipl($id);
		$cnt = $this->getContractsTable()->getResponseCount($id);
		$sourcetypes = $this->getContractsTable()->getSourceTypes();
		$sources = $this->getContractsTable()->getSources();
		$history = $this->getContractsTable()->getSourceHistory($id);
		
		//section for ending contract (early?)		
		$isDraft=false;
		$newterm=false;
		$mm0;
		$mm0d;
		if($ar["ctr_src_term"]=="" || $ar["ctr_src_term"]==null)
			$newterm = true;
		if($ar["ctr_src_term"]==0)
			$newterm = true;
		$mm0 = 12;
		$mm0d = date('Y-m-d');
		
		if(!$newterm)
		{
			if($ar["ctr_src_termdt"]!="" && $ar["ctr_src_termdt"]!='0000-00-00 00:00:00')
				$mm0d=date('Y-m-d',strtotime($ar["ctr_src_termdt"]));
			else
				$mm0d=date('Y-m-d');
			$mm0 = $ar["ctr_src_term"];
			$mmE = date('Y-m-d', strtotime("+".$mm0." months", strtotime($mm0d)));
			//echo "TEST".$mmE;
			$mNxt = date('Y-m-d', strtotime("+1 months", strtotime(date('Y-m-d'))));
			if($mmE < date('Y-m-d'))
			{
				//end sourcing
				$ended = $this->getContractsTable()->endSourcing($id);
				if(!$ended)
					$messages="There was a problem ending the sourcing campaign";
				//sql = "EXEC SourcingEnd " & ctrid & ",'" & FormatDT(Date()) & "','" & Session("UserName") & "',1"
				$newterm = true;
			}
			elseif($mmE < $mNxt)
			{
				if($_GET["drops"]=="yes")
				{
					//end sourcing
					$ended = $this->getContractsTable()->endSourcing($id);
					if(!$ended)
						$messages="There was a problem ending the sourcing campaign";
					$newterm = true;
				}
				else
				{
					$messages='<p><span class="alert bld">This campaign ends in a month.</span> <span class="bld">If you want to start planning a new campaign now</span>, please <a href="?drops=yes">click here to proceed</a>. Or, you can extend current campaign for few months by editing it.</p>';
				}			
			}
		}
		//end section			
		
		$actives = $this->getContractsTable()->getActiveSources($id, $ar['ctr_status']); //send status from earlier query
		$contacts2 = $this->getContractsTable()->getContacts($id,$ar["cli_id"]);
		
		$pp = $this->getContractsTable()->getPreProfile($id, '');	
		//echo "client".$ar["cli_id"];
        foreach ($cnt as $row=>$arr)
			{				
				if($arr['total']!="")
					$total=$arr['total'];					
			}		
        
        $request = $this->getRequest();
        //$form = new InterviewForm($months);
		$form = new ViewForm();
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ar = $form->getData();
                $part = 'Y'.$ar['yer'];
                if( !$ar['mon'] ) $ar['mon'] = date('n');
                $id = $ar['mon'];
                return $this->redirect()->toRoute('contract',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/

        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'ar' => $ar, 'comments' => $comments, 'spec' => $spec, 'rec'=>$rec,
			'sourcetypes'=>$sourcetypes, 'sources'=>$sources, 'actives'=>$actives, 'contact_status'=>$contact_status, 'states'=>$states,
            /*'contract' => $this->getContractsTable()->fetchAll($ar),*/ 'form' => $form, 'contacts' => $contacts, 'pipl' => $pipl, 'reqtotal'=>$total,
            'contacts2' => $contacts2, 'messages' => $messages, 'id' => $id, 'pp'=>$pp, 'history'=>$history );
    }
	
	public function approveAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);		
		
        $ar=$_GET;
		
		if(isset($_POST["submit"])){
			$approved = $this->getContractsTable()->approveSourcing($id,$_POST);					
		}		
		
		$ar = $this->getContractsTable()->getActiveSources($id);
		$ar2 = $this->getContractsTable()->getSourceDetails($id);
        
        $request = $this->getRequest();
        //$form = new InterviewForm($months);
		$form = new ViewForm();
        $form->setData($ar);
     
        return array('phguser' => $identity,  'ar' => $ar, 'ar2' => $ar2, 				
             'form' => $form, 'id' => $id );
    }
	
	public function sourcingStrategyAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);		
		
        $ar=$_GET;	
			
		$ctct_id=$_GET["ctct_id"];
		
		$rs = $this->getContractsTable()->getContact($ctct_id,$id);
		$rs1 = $this->getContractsTable()->getCampaign($id, 0);
		$rs2 = $this->getContractsTable()->getCampaign($id, 1);
        //echo var_dump($rs2);
        $request = $this->getRequest();       
		$form = new ViewForm();
        $form->setData($ar);
     
        return array('phguser' => $identity,  'ar' => $ar, 'ar2' => $ar2, 'rs' => $rs, 	'rs1' => $rs1, 'rs2' => $rs2,			
             'form' => $form, 'id' => $id );
    }
	
	public function sourceAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);		
		
        $ar=$_GET;	
			
		$source_id=$_GET["source_id"];
		
		$ar = $this->getContractsTable()->getSource($id);
		$comments = $this->getContractsTable()->getSourceComments($id);
		
        //echo $rs['emp_name'];
        $request = $this->getRequest();       
		$form = new ViewForm();
        $form->setData($ar);
     
        return array('phguser' => $identity,  'ar' => $ar, 'comments' => $comments,			
             'form' => $form, 'id' => $id );
    }
	
	public function sourceModAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);		
		
        $ar=$_GET;	
		$message="";
		$message = $_GET["message"];	
		if($id>0){		
			$ar = $this->getContractsTable()->getSource($id);
			$mode='edit';
		}
		else {
			$mode='add';
		}
		if(isset($_POST["submit"]))
		{
			$message="";
			$valid=true;			
			if(trim($_POST["srcname"])==""){
				$message.="You must enter a source name.<br/>";
				$valid=false;
			}
			if(trim($_POST["price"])=="" || $_POST["price"]<0){
				$message.="You must enter a price.<br/>";
				$valid=false;
			}
			if(trim($_POST["srctype"])==""){
				$message.="You must select a source type.<br/>";
				$valid=false;
			}			
			if($valid){
				if($_POST["mode"]=="edit"){
					$result = $this->getContractsTable()->editSource($_POST);					
					$message="The source has been updated";
				}
				if($_POST["mode"]=="add"){			
					//echo $_POST["mode"];
					$id = $this->getContractsTable()->addSource($_POST);
					if($id!="" && $id>0)
						return header("location: /public/contract/sourcemod/".$id."?message=The source has been added \n");
					//echo "ID1:".$id;
					
				}
			}
		}
		//$comments = $this->getContractsTable()->getSourceComments($id);
		$spec = $this->getContractsTable()->getSpecialtyOptions();
		$sourcetypes = $this->getContractsTable()->getSourceTypes(1);
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        //echo $rs['emp_name'];
        $request = $this->getRequest();       
		$form = new ViewForm();
        $form->setData($ar);
     
        return array('phguser' => $identity,  'ar' => $ar, 'spec' => $spec,	'mode' => $mode, 'sourcetypes' => $sourcetypes,	'states'=>$states,	'message'=>$message,
             'form' => $form, 'id' => $id );
    }
	
	public function manageSourcesAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);		
		
        $ar=$_GET;			
		
		$sourcetypes = $this->getContractsTable()->getSourceTypes();
		$sources = $this->getContractsTable()->getSources();
		
        $request = $this->getRequest();       
		$form = new ViewForm();
        $form->setData($ar);
     
        return array('phguser' => $identity,  'ar' => $ar, 'spec' => $spec,	'mode' => $mode, 'sourcetypes' => $sourcetypes,	'sources' => $sources,		
             'form' => $form, 'id' => $id );
    }
	
	public function sourcesAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		
		
        $ar=$_GET;
		//echo var_dump($_GET);
		if(isset($_POST["submit"])){
			$newsource = $this->getContractsTable()->OrderNewSource();
					
		}
		if(isset($_POST["submit_changes_btn"])){ //update sources table part
			$changemessage = $this->getContractsTable()->updateSourcesTable($id, $_POST);
		}
		if(isset($_POST["discard_submit"])){ //cancel campaign btn
			$discard = $this->getContractsTable()->discardCampaign($id, $_POST);
			if($discard)
				return header("location: /public/contract/view/".$id);
				//return $this->redirect()->toRoute('/contract/view/'.$id); //fix redirect
		}
		if(isset($_POST["extend_submit"])){ //extend term
			$extended = $this->getContractsTable()->extendCampaign($id, $_POST);
			if($extended)
				return header("location: /public/contract/view/".$id);
		}////
		
		$ar = $this->getContractsTable()->getActiveSources($id);
		$ar2 = $this->getContractsTable()->getSourceDetails($id);
        
        $request = $this->getRequest();
        //$form = new InterviewForm($months);
		$form = new ViewForm();
        $form->setData($ar);
     

        return array('phguser' => $identity,  'ar' => $ar, 'ar2' => $ar2, 				
             'form' => $form, 'id' => $id );
    }
	
	public function editAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		$ar = $this->getContractsTable()->selectContract($id);
		//$comments = $this->getContractsTable()->getComments($id, $ar["cli_id"]);
		//$contacts = $this->getClientsTable()->selectClientContacts($ar["cli_id"]);
		
		$spec = $this->getContractsTable()->getSpecialtyOptions();
		$mid = $this->getContractsTable()->getMidlevelOptions();
		$rec = $this->getContractsTable()->getRecruiters();
		$marketers = $this->getClientsTable()->getMarketers();		
		$form = new ContractsmodForm($ar, $spec, $rec, $mid, $marketers);		
		//echo var_dump($ar);
		//echo $ar["ctr_nu_type"];
		$form->setData($ar);
		$request = $this->getRequest();
		if ($request->isPost()) {
             $form->setData($request->getPost());
			 $flashMessenger = $this->flashMessenger();
            if ($form->isValid()) {
                
				$result = $this->getContractsTable()->saveContract($form->getData(),$id, $identity);     
//print_r($form->getData());
//print_r($form->getMessages());				
				//$flashMessenger->addMessage($result);
				$flashMessenger->addMessage('Contract Updated');
				//$flashMessenger->addMessage($result);
				return $this->redirect()->toRoute('contract', array('action'=>'view','id'=>$id));
            }
			else{			
				$flashMessenger->addMessage('NOT VALID');
				$flashMessenger->addMessage('Check the fields below');
			}
			
			//$messages = null;
			if ($flashMessenger->hasMessages()) {
				$messages = $flashMessenger->getMessages();
			}
        }
		
		return array('form' => $form, 'ar'=>$ar, 'marketers'=>$mr, 'messages2' => $messages2, 
            'messages' => $messages, 'id'=>$id);
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
            'contract' => $this->getMarketlogTable()->fetchAll($ar), 'form' => $form,
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
            'contract' => $this->getPlacemonthTable()->fetchAll($ar), 'form' => $form,
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

        return array('phguser' => $identity,
            'contract' => $this->getPlacementTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }
	
	public function ctrchangeAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		$ctr_id=$_GET["ctr_id"];
		$pipl_id=$_GET["pipl_id"];
		//echo $id;
		//echo $part;
		$ar = $this->getContractsTable()->getContractInfo($ctr_id);		
		$ar = $ar[0]; //just one row
		$specs = $this->getContractsTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["submit"])){ //submit 
			
			$worked = $this->getContractsTable()->handleCtrChange($_POST, $identity);
			
			if(!$worked)
				$message="There was a problem with the contract change";
			else
				$message="Your contract change request was added";
			return header("location: /public/contract/view/".$ctr_id."?message=".$message." \n");			
				
		}		
				
		$form = new ViewForm();
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	
	public function approvectrchangeAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		$ctr_id=$_GET["ctr_id"];
		$pipl_id=$_GET["pipl_id"];
		$chg_id=$_GET["chg_id"];
		//echo $id;
		//echo $part;
		$ar = $this->getContractsTable()->getContractApprovalInfo($chg_id);		
		//$ar = $ar[0]; //just one row
		$specs = $this->getContractsTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["approvebtn"])||isset($_POST["declinebtn"])){ //submit 
						
			$worked = $this->getContractsTable()->handleApproveCtrChange($_POST, $identity);
			
			if(!$worked)
				$message="There was a problem with the contract change approval";
			else
				$message="Your contract change decision was added";
			//return header("location: /public/midlevel/view/".$id."?message=".$message." \n");			
				
		}	
		
				
		$form = new ViewForm();
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	
	public function preprofileAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		$ctrid = $_GET["ctr_id"];
		$contid = $_GET["cont_id"];
		
		$ar = $this->getContractsTable()->getPreProfile($ctrid, $contid);		
		//$ar = $ar[0]; //just one row		
				
        $request = $this->getRequest(); 		
				
		$form = new ViewForm();
        $form->setData($ar);      

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id );
    }
	
	public function ctrchange4Action() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		//$ctr_id=$_GET["ctr_id"];
		//$pipl_id=$_GET["pipl_id"];
		$ar = array();
		$chgid = $_GET["chg_id"];
		$ar = $this->getContractsTable()->getContractApprovalInfo($chgid);		
		//$ar = $ar[0]; //just one row
		//$specs = $this->getContractsTable()->getSpecialtyOptions();
		//$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
					
		$form = new ViewForm();
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	
	public function sourcing4Action() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        //$months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		
		$ar = array();
		$id = $_GET["ctr_id"];
		//$ar = $this->getContractsTable()->getContractApprovalInfo($chgid);		
		$ar = $this->getContractsTable()->selectContract($id);
		$mm0 = $ar["ctr_src_term"];
		$rs = $this->getContractsTable()->getApprovalSources($id); 		
        $request = $this->getRequest(); 
					
		$form = new ViewForm();
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost()); 
			
			if($_POST["act"]!="src4form")
			{
				$message="Client Approval is mandatory!";
			}
			else
			{
				$worked = $this->getContractsTable()->approveSubmitSourcing($_POST, $id);
				if(!$worked)
					$message="There was a problem with the contract approval";
				else
					$message="Your contract changes approval was submitted";
				return header("location: /public/contract/view/".$id."?message=".$message." \n");
			}
			//return header("location: /public/contract/view/".$id."?message=".$message." \n");
			//approveSubmitSourcing($post,$ctrid)
        }
//echo var_dump($rs);

        return array('phguser' => $identity, 'ar' => $ar, 'rs'=>$rs, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	

    public function getUsersTable() {
        return $this->contractTableFactory('usersTable','');
    }
    public function getSpecialtyTable() {
        return $this->contractTableFactory('specialtyTable','');
    }
    public function getMidcatTable() {
        return $this->contractTableFactory('midcatTable','');
    }
    public function getPlacementTable() {
        return $this->contractTableFactory('placementTable');
    }
    public function getPlacemonthTable() {
        return $this->contractTableFactory('placemonthTable');
    }
    public function getMarketlogTable() {
        return $this->contractTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->contractTableFactory('interviewTable');
    }
	public function getContractsTable() {
        return $this->contractTableFactory('contractsTable', '');
    }
	public function getMidlevelsTable() {
        return $this->contractTableFactory('midlevelsTable', '');
    }
	public function getClientsTable() {
        return $this->contractTableFactory('clientsTable', '');
    }

    protected function contractTableFactory($table,$submodel = 'Contract\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
