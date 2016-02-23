<?php
// module/Pinnacle/src/Pinnacle/Controller/MidlevelController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\MidlevelsForm;
use Pinnacle\Form\MidsViewForm;
use Pinnacle\Model\Utility;

class MidlevelController extends AbstractActionController
{
    protected $usersTable;
    protected $midcatTable;
    protected $placementTable;
    protected $placemonthTable;
    protected $marketlogTable;
    protected $interviewTable;
    protected $descTable;
	protected $physiciansTable;
	protected $contractsTable;
	protected $clientsTable;
	protected $midlevelsTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        $cats = $this->getMidcatTable();
        $mlists = $this->getDescTable()->getSelectOptions($identity->uid);
        $form = new MidlevelsForm($cats->getCatSelect(),$mlists);
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages,
                     'atc' => $cats->getCatCount(), 'atb' => $cats->getCatLists() );
    }

    public function viewAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        //$ar = array('yer'=>date('Y'),'mon'=>date('n'));
        $part = $this->params()->fromRoute('part', 'Y'.date('Y'));
        $id = (int) $this->params()->fromRoute('id', 0);
        $yer = (int) substr($part,1,4);
        if( $yer && is_numeric($yer) ) $ar['yer'] = $yer;
        else $part = 'Y'.$ar['yer'];
               
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		//echo $id;
		//echo $part;
		$ar = $this->getMidlevelsTable()->selectMidlevel($id);		
		$comments = $this->getMidlevelsTable()->getMidlevelComments($id);
		$rec = $this->getContractsTable()->getRecruiters();
		$users = $this->getContractsTable()->getUsers();
		$sources = $this->getPhysiciansTable()->getSources2();	
		$sources2 = $this->getMidlevelsTable()->getSourceHistory($id);
		$history = $this->getMidlevelsTable()->getHistory($id);
		$act = $this->getPhysiciansTable()->getActivities(1);
		$contracts = $this->getContractsTable()->getContracts(1);
		$mycontracts = $this->getContractsTable()->getMyContracts(0);
		$ltcontracts = $this->getContractsTable()->getLocumsContracts(0); //0 since no nurse contracts right now.
		$asscontracts = $this->getContractsTable()->getAssessmentContracts($id,1); //0 for dr
		$passes = $this->getMidlevelsTable()->getPasses($id);
		$srslink="";
		if($_GET["srs"]!='')
			$srslink="&srs=".$_GET["srs"];
        
        $request = $this->getRequest(); 
		
		if(isset($_POST["commentsubmit"])){ //submit comment
			
			$worked = $this->getClientsTable()->addNewComment($_POST, $identity);
			if(!$worked)
				$message="There was a problem with adding your comment";
			else
				$message="Your comment was added";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");			
				
		}		
		if(isset($_POST["sched_submit_btn"])){ //submit activity
			
			$worked = $this->getMidlevelsTable()->addActivity($_POST, $identity);
			if(!$worked)
				$message="There was a problem with adding your activity";
			else
				$message="Your activity was added";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");			
				
		}
		if(isset($_POST["mid_pass_submit_btn"])){ 
			$worked = $this->getMidlevelsTable()->passTo($_POST, $identity);
			if(!$worked)
				$message="There was a problem with passing";
			else
				$message="Midlevel passed successfully";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if($_GET["action"]=='cancel'){ //cancel interview...
			//echo "cancel".$_GET["id"];
			$id=$_GET["id"];
			if($id>0 && $id!="")
				$worked = $this->getMidlevelsTable()->cancelPIPL($_GET, $identity);
			$message="The activity was cancelled";
		}
		if(isset($_POST["pres_submit_btn"])){
			$valid=true;			
			if($_POST["pres_ctr_id"]==""){
				$valid=false;
				$message.="Contract is required<br/>";
			}
			if($_POST["pres_date"]==""){
				$valid=false;
				$message.="Present date is required<br/>";
			}
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->createPresent($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the present";
				else
					$message="Present was added";
			}
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		
		if(isset($_POST["pend_submit_btn"])){ //add pending
			$valid=true;			
			if($_POST["ctr_id"]==""){
				$valid=false;
				$message.="Contract is required<br/>";
			}
			if($_POST["pend_date"]==""){
				$valid=false;
				$message.="Pending date is required<br/>";
			}
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->createPending($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the pending status";
				else
					$message="Pending was added";
				return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");
			}
			
		}
		
		/*if(isset($_POST["ch_submit"])){ //update lead lists, hotdoc, etc (action panel)
			$valid=true;			
			
				$worked = $this->getPhysiciansTable()->updateHotList($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating hot list info";
				else
					$message="Hot List info was updated";
				return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");
			
		}*/
		
		if(isset($_POST["int_submit_btn"])){
			$valid=true;			
			if($_POST["int_ctr_id"]==""){
				$valid=false;
				$message.="Contract is required<br/>";
			}
			if($_POST["int_date"]==""){
				$valid=false;
				$message.="Interview date is required<br/>";
			}
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->createInterview($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the interview";
				else
					$message="Interview was added";
			}
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["loc_pres_submit_btn"])){
			$valid=true;			
			if($_POST["pres_ctr_id"]==""){
				$valid=false;
				$message.="Contract is required<br/>";
			}
			if($_POST["pres_date"]==""){
				$valid=false;
				$message.="Present date is required<br/>";
			}
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->createLocumsPresent($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the present";
				else
					$message="Present was added";
			}
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["loc_pass_submit_btn"])){
			$valid=true;			
			if($_POST["phname"]==""){
				$valid=false;
				$message.="Provider Name is Mandatory<br/>";
			}			
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->createLocumsPass($_POST, $identity);
				if(!$worked)
					$message="There was a problem passing to locums";
				else
					$message="Midlevel passed successfully";
			}
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		//credentials date section
		if(isset($_POST["ref_date_submit_btn"])){ //reference request submit
			$worked = $this->getMidlevelsTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["ama_date_submit_btn"])){ //ama date submit
			$worked = $this->getMidlevelsTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["preint_date_submit_btn"])){ //pre-int date submit
			$worked = $this->getMidlevelsTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["cand_ass_date_submit_btn"])){ //cand ass date
		
		}
		if(isset($_POST["packet_date_submit_btn"])){ //packet completed
			$worked = $this->getMidlevelsTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
		}
		
		if(isset($_POST["addsource_submit_btn"])){
			$valid=true;			
			if($_POST["addsource_ctr_id"]=="" || $_POST["addsource_ctr_id"]<=0){
				$valid=false;
				$message.="Contract is not selected<br/>";
			}	
			if($_POST["is_dm"]==""){
				$valid=false;
				$message.="Source is mandatory<br/>";
			}	
			if($_POST["is_dm"]=="0" && ($_POST["source"]=="" || $_POST["source"]=="Unspecified")){
				$valid=false;
				$message.="Select a source from the drop down<br/>";
			}
			if($_POST["addsource_date"]==""){
				//$valid=false;
				//$message.="Date is mandatory<br/>";
				$_POST["addsource_date"]=date('Y-m-d');
			}	
			if($valid)
			{
				$worked = $this->getMidlevelsTable()->addSource($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding source";
				else
					$message="Source added successfully";
				return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["candass_submit_btn"])){ //cand ass document 
			
			$_POST["candass_ctr_id"];
			$candarr = $this->getMidlevelsTable()->getCandidateAssessment($_POST);
			
			//echo " /public/word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&cliname=".$candarr['cliname']."&ph_id=".$candarr['ph_id']."&phname=".$candarr['phname']."&phtitle=".$candarr['phtitle']."&ctr_id=".$candarr['ctr_id']."&ctr_no=".$candarr['ctr_no']."&addr1=".$candarr['addr1']."&addr2=".$candarr['addr2']."&city=".$candarr['city']."&zip=".$candarr['zip']."&state=".$candarr['state']."&spec=".$candarr['spec']."&reqname=".$candarr['reqname']."&reqtitle=".$candarr['reqtitle']."&as_date=".$candarr['as_date']."&motiv=".$candarr['motiv']."&goals=".$candarr['goals']."&fam=".$candarr['fam']."&hob=".$candarr['hob']."&items=".$candarr['items']." \n";
			return header("location: /public/word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&cliname=".$candarr['cliname']."&ph_id=".$candarr['ph_id']."&phname=".$candarr['phname']."&phtitle=".$candarr['phtitle']."&ctr_id=".$candarr['ctr_id']."&ctr_no=".$candarr['ctr_no']."&addr1=".$candarr['addr1']."&addr2=".$candarr['addr2']."&city=".$candarr['city']."&zip=".$candarr['zip']."&state=".$candarr['state']."&spec=".$candarr['spec']."&reqname=".$candarr['reqname']."&reqtitle=".$candarr['reqtitle']."&as_date=".$candarr['as_date']."&motiv=".urlencode($candarr['motiv'])."&goals=".urlencode($candarr['goals'])."&fam=".urlencode($candarr['fam'])."&hob=".urlencode($candarr['hob'])."&items=".urlencode($candarr['items'])." \n");				
		
		}
		
		
		//echo var_dump($_POST);
		
		
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());           
                
                //return $this->redirect()->toRoute('midlevel',array('action'=>'interview', 'part'=>$part,'id'=>$id));
            
        }
//echo var_dump($act);
//echo $sources[1]['src_name'];
        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'comments' => $comments, 'rec'=>$rec,'sources'=>$sources, 'sources2'=>$sources2,
            'ar' => $ar, 'form' => $form, 'message'=>$message, 'history' => $history, 'act'=>$act, 'users' => $users, 'contracts'=>$contracts, 'ltcontracts'=>$ltcontracts,
            'messages' => $messages, 'id' => $id, 'asscontracts'=>$asscontracts, 'mycontracts'=>$mycontracts, 'passes'=>$passes );
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
		$ar = $this->getMidlevelsTable()->getContractInfo($ctr_id);		
		$ar = $ar[0]; //just one row
		$specs = $this->getMidlevelsTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["submit"])){ //submit 
			
			$worked = $this->getMidlevelsTable()->handleCtrChange($_POST, $identity);
			
			if(!$worked)
				$message="There was a problem with the contract change";
			else
				$message="Your contract change request was added";
			//return header("location: /public/midlevel/view/".$id."?message=".$message." \n");			
				
		}		
				
		$form = new MidsViewForm($months);		
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
		$ar = $this->getMidlevelsTable()->getContractApprovalInfo($chg_id);		
		//$ar = $ar[0]; //just one row
		$specs = $this->getMidlevelsTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["approvebtn"])||isset($_POST["declinebtn"])){ //submit 
						
			$worked = $this->getMidlevelsTable()->handleApproveCtrChange($_POST, $identity);
			
			if(!$worked)
				$message="There was a problem with the contract change approval";
			else
				$message="Your contract change decision was added";
			//return header("location: /public/midlevel/view/".$id."?message=".$message." \n");			
				
		}	
		
				
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	
	public function addAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		
		$ar = array();
		//$id = (int) $this->params()->fromRoute('id', 0);
        $statuses = $this->getPhysiciansTable()->getPhyStatus();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		$types = $this->getPhysiciansTable()->getPracticeTypes();		
		$rec = $this->getContractsTable()->getRecruiters();
		$sources = $this->getPhysiciansTable()->getSources();
		//$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$regs = $this->getPhysiciansTable()->getRegions();
		
		$cats = $this->getMidcatTable();
		$specs = $cats->getCatSelect();
        //$mlists = $this->getDescTable()->getSelectOptions($identity->uid);
				
        $request = $this->getRequest(); 		
	
		
		if(isset($_POST["submitadd"])){ //submit 
			$valid=true;
			if($_POST["an_fname"]==""){
				$valid=false;
				$message.="First Name is required<br/>";
			}
			if($_POST["an_lname"]==""){
				$valid=false;
				$message.="Last Name is required<br/>";
			}
			if($valid)
			{
			$nurse_id = $this->getMidlevelsTable()->addMidlevel($_POST, $identity);
			
			if($nurse_id<=0||$nurse_id==''){
				$message="There was a problem adding the midlevel";
			}
			else{
				$message="The midlevel was added";
				return header("location: /public/midlevel/view/".$nurse_id."?message=".$message." \n");					
			}
			}
				
		}	
		
				
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec, 'statuses' => $statuses, 'states'=>$states,
            'types' => $types,  'message'=>$message, 'regs' => $regs, 'atc' => $cats->getCatCount(), 'atb' => $cats->getCatLists() );
    }
	
	
	public function editAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
			
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$message = $_GET["message"];
		
		$ar = array();
		$id = (int) $this->params()->fromRoute('id', 0);
		$ar = $this->getMidlevelsTable()->getMidlevelDetails($id, $identity);
		
        $statuses = $this->getPhysiciansTable()->getPhyStatus();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		$types = $this->getPhysiciansTable()->getPracticeTypes();		
		$rec = $this->getContractsTable()->getRecruiters();
		$sources = $this->getPhysiciansTable()->getSources();
		
		//$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$regs = $this->getPhysiciansTable()->getRegions();
		
		$cats = $this->getMidcatTable();
		$specs = $cats->getCatSelect();
        //$mlists = $this->getDescTable()->getSelectOptions($identity->uid);
				
        $request = $this->getRequest(); 		
		
		if($_GET["srs"]!='')
			$srslink="&srs=".$_GET["srs"];
		if($_POST["srs"]!='')
			$srslink="&srs=".$_POST["srs"];
		
		if(isset($_POST["submitedit"])){ //submit 
			$valid=true;
			if($_POST["ctct_name"]==""){
				$valid=false;
				$message.="Name is required<br/>";
			}
			if($_POST["an_type"]==""){
				$valid=false;
				$message.="Specialty is required<br/>";
			}
			if($valid)
			{
			$result = $this->getMidlevelsTable()->editMidlevel($_POST, $identity);
			
			if(!$result){
				$message="There was a problem editing the midlevel";
			}
			else{
				$message="The midlevel was edited";
				return header("location: /public/midlevel/view/".$id."?message=".$message.$srslink." \n");	
				//return header("location: /public/midlevel/edit/".$id."?message=".$message." \n");	
			}
			}
				
		}	
		
				
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec, 'statuses' => $statuses, 'states'=>$states,
            'types' => $types,  'message'=>$message, 'regs' => $regs, 'atc' => $cats->getCatCount(), 'atb' => $cats->getCatLists() );
    }
	
	public function viewOldAction() {
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
                return $this->redirect()->toRoute('midlevel',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }

        return array('phguser' => $identity, 'part' => $part, 'months' => $months,
            'midlevel' => $this->getInterviewTable()->fetchAll($ar), 'form' => $form,
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
            'midlevel' => $this->getMarketlogTable()->fetchAll($ar), 'form' => $form,
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
            'midlevel' => $this->getPlacemonthTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }

	public function placementAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$ar = array();	
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$rec = $this->getContractsTable()->getRecruiters();
		$users = $this->getContractsTable()->getUsers();
		$message = $_GET["message"];
		//$ctr_id=$_GET["ctr_id"];
		//$pipl_id=$_GET["pipl_id"];
		//$chg_id=$_GET["chg_id"];
		
		$ctr_id = $_POST["plac_ctr_id"];
		if($_POST["plac_ctr_id"]=='')
			$ctr_id = $_GET["plac_ctr_id"];
		//$ctr_id = 4082; ///change!!!!!!!!!!!!!!!!!!
		$an_id = $_POST["an_id"];
		if($_POST["an_id"]=='')
			$an_id = $_GET["an_id"];
		//echo $ctr_id;
		//echo $part;
		$ar = $this->getMidlevelsTable()->getContractInfo($ctr_id);		
		$ar = $ar[0]; //just one row
		$ar2 = $this->getMidlevelsTable()->getPlacementInfo($ctr_id, $an_id);
		$ar3 = $this->getMidlevelsTable()->getContractSpec($ctr_id);		
		$specs = $this->getMidlevelsTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["approvebtn"])){ //submit 
			$valid=true;
			if($_POST["pl_sent_date"]=="" || $_POST["pl_sent_date"]=="0000-00-00" || $_POST["pl_sent_date"]=="1970-01-01"){ 
				$valid=false;
				$message.="Please enter the contract sent date<br/>";
			}
			if($_POST["pl_date"]=="" || $_POST["pl_date"]=="0000-00-00" || $_POST["pl_date"]=="1970-01-01"){ 
				$valid=false;
				$message.="Please enter the placement sent date<br/>";
			}
			if($_POST["pl_text1"]==""){
				$valid=false;
				$message.="Please answer success story questions<br/>";
			}			
			if($_POST["pl_text2"]==""){
				$valid=false;
				$message.="Please answer success story questions<br/>";
			}	
			if($_POST["pl_text3"]==""){
				$valid=false;
				$message.="Please answer success story questions<br/>";
			}	
			if($_POST["pl_text4"]==""){
				$valid=false;
				$message.="Please answer success story questions<br/>";
			}	
			
			if($valid)
			{
				//submit
				$worked = $this->getMidlevelsTable()->addPlacement($_POST, $identity);
				if($worked)
					$message="The placement was updated";
				else
					$message="There was a problem submitting the placement";
			}				
				
		}			
				
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'an_id'=>$an_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'ctr_id'=>$ctr_id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
	public function assessmentAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$ar = array();	
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
		$rec = $this->getContractsTable()->getRecruiters();
		$users = $this->getContractsTable()->getUsers();
		$message = $_GET["message"];
		$id = (int) $this->params()->fromRoute('id', 0);
		
		$ctr_id = $_GET["ctr_id"];
		
		
		$ar = $this->getMidlevelsTable()->selectMidlevel($id);	
		$an_ca_not = $ar["an_ca_not"];		
		$ar = $this->getMidlevelsTable()->getAssessment($id, $ctr_id);		
		//echo var_dump($ar);
		//$ar = $ar[0]; //just one row	
		$contracts = $this->getContractsTable()->getContracts(0);
				
        $request = $this->getRequest();		
		
		if(isset($_POST["savebtn"])){ //submit 
			$valid=true;
			if($_POST["ctr_id"]==""){
				$valid=false;
				$message.="Please select a contract<br/>";
			}			
			if($_POST["ph_id"]=="" ){
				$valid=false;
				$message.="No midlevel ID. Please go back.<br/>";
			}		
			if($_POST["as_date"]==""){
				$valid=false;
				$message.="Please enter a date.<br/>";
			}	
			
			if($valid)
			{
				//submit
				$worked = $this->getMidlevelsTable()->addAssessment($_POST, $identity);
				if($worked)
					$message="The assessment was updated";
				else
					$message="There was a problem submitting the assessment";
			}				
		}
		
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'an_ca_not'=>$an_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'an_id'=>$an_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
	
	public function refbillAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$ar = array();	
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();		
		$message = $_GET["message"];
		//$id = (int) $this->params()->fromRoute('id', 0);		
		
		$ctr_id = $_GET["ctr_id"];
		$an_id = $_GET["an_id"];
		
		
				
		$ar = $this->getMidlevelsTable()->getBilling($ctr_id, $an_id);	
		
		//echo var_dump($ar); //$ph_id;
		
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $an_id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
	public function amacheckAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$ar = array();	
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();		
		$message = $_GET["message"];
		//$id = (int) $this->params()->fromRoute('id', 0);		
		
		$ctr_id = $_GET["ctr_id"];
		$an_id = $_GET["an_id"];
		
		
				
		$ar = $this->getMidlevelsTable()->getAMACheck($ctr_id, $an_id);	
		
		//echo var_dump($ar); //$ph_id;
		
		$form = new MidsViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $an_id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
    /*public function placementAction() {
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
            'midlevel' => $this->getPlacementTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }*/

    public function getUsersTable() {
        return $this->midlevelTableFactory('usersTable','');
    }
    public function getMidcatTable() {
        return $this->midlevelTableFactory('midcatTable','');
    }
    public function getPlacementTable() {
        return $this->midlevelTableFactory('placementTable');
    }
    public function getPlacemonthTable() {
        return $this->midlevelTableFactory('placemonthTable');
    }
    public function getMarketlogTable() {
        return $this->midlevelTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->midlevelTableFactory('interviewTable','Report');
    }
    public function getDescTable() {
        return $this->midlevelTableFactory('descTable','Mail\\');
    }
	public function getPhysiciansTable() {
        return $this->midlevelTableFactory('physiciansTable','');
		//return $this->reportTableFactory('physiciansTable','');
    }
	public function getClientsTable() {
        return $this->midlevelTableFactory('clientsTable','');		
    }    
	public function getContractsTable() {
        return $this->midlevelTableFactory('contractsTable',''); 
    }
	public function getMidlevelsTable() {
        return $this->midlevelTableFactory('midlevelsTable',''); 
    }
	

    protected function midlevelTableFactory($table,$submodel = 'Midlevel\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
