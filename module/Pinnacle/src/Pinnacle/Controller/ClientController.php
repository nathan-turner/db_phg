<?php
// module/Pinnacle/src/Pinnacle/Controller/ClientController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\ClientsForm;
use Pinnacle\Form\InterviewForm;
use Pinnacle\Form\ViewForm;
use Pinnacle\Form\ClientmodForm;
use Pinnacle\Model\Utility;

class ClientController extends AbstractActionController
{
    protected $usersTable;
    protected $specialtyTable;
    protected $placementTable;
    protected $placemonthTable;
    protected $marketlogTable;
    protected $interviewTable;
    protected $descTable;
	protected $contractsTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        //$mlists = $this->getDescTable()->getSelectOptions($identity->uid);
		$mlists = array();
        $form = new ClientsForm($mlists);
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages );
    }
	
	//ajax form
	public function showformAction()
    {
        $viewmodel = new ViewModel();
        $form       = $this->getForm();
        $request = $this->getRequest();
         
        //disable layout if request by Ajax
        $viewmodel->setTerminal($request->isXmlHttpRequest());
         
        $is_xmlhttprequest = 1;
        if ( ! $request->isXmlHttpRequest()){
            //if NOT using Ajax
            $is_xmlhttprequest = 0;
            if ($request->isPost()){
                $form->setData($request->getPost());
                if ($form->isValid()){
                    //save to db <img src="http://s1.wp.com/wp-includes/images/smilies/icon_wink.gif?m=1129645325g" alt=";)" class="wp-smiley">
                    $this->savetodb($form->getData());
                }
            }
        }
         
        $viewmodel->setVariables(array(
                    'form' => $form,
                    // is_xmlhttprequest is needed for check this form is in modal dialog or not in view
                    'is_xmlhttprequest' => $is_xmlhttprequest
        ));
         
        return $viewmodel;
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
		//$ar = array('yer'=>date('Y'),'mon'=>date('n'),'cli_city'=>'atlanta1');
        $message=$_GET["message"];
		
       $states = array('0' => ' Any State') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        
        $request = $this->getRequest();
		$ar = $this->getClientsTable()->selectClient($id);
		$contacts = $this->getClientsTable()->selectClientContacts($id);
		$contact_status = $this->getClientsTable()->getContactStatus();
		$comments = $this->getClientsTable()->getComments($id);
		$contracts = $this->getClientsTable()->getContracts($id);
		$meetings = $this->getClientsTable()->getMeetings($id);
		$activities = $this->getClientsTable()->getActivities($id);
		$activitylist = $this->getClientsTable()->getActivityList();
		$userslist = $this->getClientsTable()->getUsersList();
		$hotlist = $this->getClientsTable()->getHotList($id);
		//echo var_dump($hotlist);
		//$sendgrid = new SendGrid('mfollowell', 'Phg3356!');
		$srslink="";
		if($_GET["srs"]!='')
			$srslink="&srs=".$_GET["srs"];
		if(isset($_POST["commentsubmit"])){ //submit comment
			//echo "submit comment";
			//echo $_POST"ref_id"];
			$worked = $this->getClientsTable()->addNewComment($_POST, $identity);
			if(!$worked)
				$message="There was a problem with adding your comment";
			else
				$message="Your comment was added";
			return header("location: /public/client/view/".$id."?message=".$message.$srslink." \n");
							
		}
		if(isset($_POST["change_listsbtn"])){ //submit comment
			
			$worked = $this->getClientsTable()->updateHotList($_POST, $id);
			if(!$worked)
				$message="There was a problem with your update";
			else
				$message="Your lists were updated";
			return header("location: /public/client/view/".$id."?message=".$message.$srslink." \n");
							
		}
		if(isset($_POST["merge_submit_btn"])){ //submit merge			
			$worked = $this->getClientsTable()->mergeClient($_POST);
			if(!$worked)
				$message="There was a problem with your merge";
			else
				$message="The client was merged successfully";
			return header("location: /public/client/view/".$id."?message=".$message.$srslink." \n");
		}
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
                return $this->redirect()->toRoute('client',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/

        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'states' => $states, 'activities' => $activities, 'activitylist' => $activitylist,
            'client' => $this->getInterviewTable()->fetchAll($ar), 'form' => $form, 'meetings'=> $meetings, 'userslist' => $userslist, 'hotlist' => $hotlist,
            'messages' => $messages, 'message' => $message, 'id' => $id, 'ar'=>$ar, 'contacts'=>$contacts, 'contact_status'=>$contact_status, 'comments'=>$comments, 'contracts'=>$contracts,);
    }
	
	public function editAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		$ar = $this->getClientsTable()->selectEditClient($id);
		$mr = $this->getClientsTable()->getMarketers();
		$types = $this->getClientsTable()->getTypes();
		$form = new ClientmodForm($mr, $types, $ar);        
		
		$form->setData($ar);
		$request = $this->getRequest();
		if ($request->isPost()) {
             $form->setData($request->getPost());
			 $flashMessenger = $this->flashMessenger();
            if ($form->isValid()) {
                //$this->getUsermodTable()->saveUser($form->getData(),$id, $identity);
				$result = $this->getClientsTable()->saveClient($form->getData(),$id, $identity);                
				$flashMessenger->addMessage($result);
				$flashMessenger->addMessage('Client Updated');
				return $this->redirect()->toRoute('client', array('action'=>'view','id'=>$id));
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
		
		return array('form' => $form, 'ar'=>$ar, 'marketers'=>$mr,
            'messages' => $messages, 'id'=>$id);
	}
	
	public function addAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		//$id = (int) $this->params()->fromRoute('id', 0);
		$ar = $this->getClientsTable()->selectEditClient($id);
		$mr = $this->getClientsTable()->getMarketers();
		$types = $this->getClientsTable()->getTypes();
		$form = new ClientmodForm($mr, $types, $ar);        
		
		$form->setData($ar);
		$request = $this->getRequest();
		if ($request->isPost()) {
             $form->setData($request->getPost());
			 $flashMessenger = $this->flashMessenger();
            if ($form->isValid()) {
                
				$result = $this->getClientsTable()->addClient($form->getData(), $identity);
                //$flashMessenger->addMessage('VALID');
				//echo var_dump($form->getData());
				//$flashMessenger->addMessage($result);
				if($result>0){
					$flashMessenger->addMessage('Client Added Successfully');
					return $this->redirect()->toRoute('client', array('action'=>'view','id'=>$result));
				}
            }
			else{
				$flashMessenger->addMessage('NOT VALID');
				$flashMessenger->addMessage('Check the fields below');
			}
			//$flashMessenger->addMessage('cookie'.$_COOKIE["phguid"]);
			 
			//$messages = null;
			if ($flashMessenger->hasMessages()) {
				$messages = $flashMessenger->getMessages();
			}
        }
		
		return array('form' => $form, 'ar'=>$ar, 'marketers'=>$mr,
            'messages' => $messages, 'id'=>$id);
	}
	
	
	/*public function viewActionOld() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        $ar = array('yer'=>date('Y'),'mon'=>date('n'),'cli_city'=>'atlanta1');
        $part = $this->params()->fromRoute('part', 'Y'.date('Y'));
        $id = (int) $this->params()->fromRoute('id', 0);
        $yer = (int) substr($part,1,4);
        if( $yer && is_numeric($yer) ) $ar['yer'] = $yer;
        else $part = 'Y'.$ar['yer'];
        if( $id > 0 && $id < 13 ) $ar['mon'] = $id;
        else $id = $ar['mon'];
        
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        
        $request = $this->getRequest();
        //$form = new InterviewForm($months);
		$form = new ViewForm();
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ar = $form->getData();
                $part = 'Y'.$ar['yer'];
                if( !$ar['mon'] ) $ar['mon'] = date('n');
                $id = $ar['mon'];
                return $this->redirect()->toRoute('client',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }

        return array('phguser' => $identity, 'part' => $part, 'months' => $months,
            'client' => $this->getInterviewTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages, 'id' => $id );
    }*/
    
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
            'client' => $this->getMarketlogTable()->fetchAll($ar), 'form' => $form,
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
            'client' => $this->getPlacemonthTable()->fetchAll($ar), 'form' => $form,
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
            'client' => $this->getPlacementTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }
	
	
	public function addcontractAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		//$ar = $this->getContractsTable()->selectContract($id);
		$ar = $this->getClientsTable()->selectClient($id);
		
		$spec = $this->getPhysiciansTable()->getSpecialtyOptions(); //was contracts table
		$mid = $this->getContractsTable()->getMidlevelOptions();
		//$mids = $this->getMidcatTable()->getCatSelect();
		$rec = $this->getContractsTable()->getRecruiters();
		$mr = $this->getClientsTable()->getMarketers();		
		$status = $this->getClientsTable()->getStatusList();
		$states = array('0' => ' Any State') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
		$form = new ViewForm();	
		
		//echo var_dump($ar);
		//$form->setData($ar);
		$request = $this->getRequest();
		if(isset($_POST["submit"]))
		{
			//echo "submitted";
			$message="";
			$valid=true;			
			if(trim($_POST["ctr_no"])==""){
				$message.="You must enter a contract #.<br/>";
				$valid=false;
			}
			
			if($valid){
				
				$cid = $this->getClientsTable()->addContract($_POST); //doesn't return id right now, just true
				if($cid!="" && $cid>0)
						return header("location: /public/client/view/".$id."?message=The contract has been added \n");
				else
					$message = "There was a problem adding the contract";
				
			}
		}
		
		return array('form' => $form, 'ar'=>$ar, 'mr'=>$mr, 'messages2' => $messages2, 'rec'=>$rec, 'spec'=>$spec, 'mid'=>$mid, 'mids'=>$mids, 'status'=>$status,
            'states'=>$states, 'messages' => $messages, 'message' => $message, 'id'=>$id);
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
            'client' => $table->fetchAll($id), 'order' => $table->getOrderStrings(),
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
	
	public function addfuzionAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		
		$cli_id = $_GET["cli_id"];
						
		$form = new ViewForm();			
		
		$request = $this->getRequest();
		if(isset($_POST["submit"]))
		{
			$cli_id = $_POST["cli_id"];
			$result = $this->getClientsTable()->addFuzionContract($_POST);
			if($result)
				$message="Fuzion contract added";
			else
				$message="There was a problem";
			return header("location: /public/client/view/".$cli_id."?message=".$message." \n");
		}
		
		return array('form' => $form, 'ar'=>$ar, 'cli_id'=>$cli_id, 'messages2' => $messages2, 'rec'=>$rec, 'spec'=>$spec, 'mid'=>$mid, 'mids'=>$mids, 'status'=>$status,
            'states'=>$states, 'messages' => $messages, 'message' => $message, 'id'=>$id);
	}
	
	public function editfuzionAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		
		$cli_id = $_GET["cli_id"];
		$id = (int) $this->params()->fromRoute('id', 0);				
		$form = new ViewForm();			
		$ar = $this->getClientsTable()->getFuzionContract($id);
		
		$request = $this->getRequest();
		if(isset($_POST["submit"]))
		{
			$cli_id = $_POST["cli_id"];			
			$result = $this->getClientsTable()->editFuzionContract($_POST);
			if($result)
				$message="Fuzion contract updated";
			else
				$message="There was a problem";
			return header("location: /public/client/view/".$cli_id."?message=".$message." \n");
		}
		
		return array('form' => $form, 'ar'=>$ar, 'cli_id'=>$cli_id, 'messages2' => $messages2, 'rec'=>$rec, 'spec'=>$spec, 'mid'=>$mid, 'mids'=>$mids, 'status'=>$status,
            'states'=>$states, 'messages' => $messages, 'message' => $message, 'id'=>$id);
	}
	
	public function weeklyupdateAction() {
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		
		$realname = $_COOKIE["realname"];
		//$cli_id = $_GET["cli_id"];
		//$id = (int) $this->params()->fromRoute('id', 0);				
		$form = new ViewForm();			
		
		
		$request = $this->getRequest();
		if(isset($_POST["submit1"])) //go to step 2
		{
			
			$arclients = $this->getClientsTable()->handleWeeklyForm1($_POST);			
			
		}
		else if(isset($_POST["submit2"])) //step 3 / send
		{
			$this->getClientsTable()->sendWeeklyForm($_POST);
		}
		else if(isset($_POST["save"])) //save
		{
			$arclients = $this->getClientsTable()->saveWeeklyForm($_POST);
			//$arclients = $this->getClientsTable()->handleWeeklyForm1($_POST);
		}
		else {
			$ar = $this->getClientsTable()->getWeeklyClientList($_POST);
		}
		
		return array('form' => $form, 'ar'=>$ar, 'arclients'=>$arclients, 'cli_id'=>$cli_id, 'messages2' => $messages2, 'rec'=>$rec, 'spec'=>$spec,  'messages' => $messages, 'message' => $message, 'id'=>$id, 'realname'=>$realname);
	}
    
    public function getUsersTable() {
        return $this->clientTableFactory('usersTable','');
    }
    public function getSpecialtyTable() {
        return $this->clientTableFactory('specialtyTable','');
    }
    public function getPlacementTable() {
        return $this->clientTableFactory('placementTable');
    }
    public function getPlacemonthTable() {
        return $this->clientTableFactory('placemonthTable');
    }
	public function getClientsTable() {
        return $this->clientTableFactory('clientsTable', '');
    }
	public function getContractsTable() {
        return $this->clientTableFactory('contractsTable', '');
    }
	public function getPhysiciansTable() {
        return $this->clientTableFactory('physiciansTable', '');
    }
	public function getMidcatTable() {
        return $this->clientTableFactory('midcatTable', '');
    }
    public function getMarketlogTable() {
        return $this->clientTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->clientTableFactory('interviewTable','Report\\');
    }
    public function getDescTable() {
        return $this->clientTableFactory('descTable','Mail\\');
    }

    protected function clientTableFactory($table,$submodel = 'Client\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
