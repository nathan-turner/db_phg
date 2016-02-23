<?php
// module/Pinnacle/src/Pinnacle/Controller/PhysicianController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\PhysiciansForm;
use Pinnacle\Form\PhysViewForm;
use Pinnacle\Form\PhysmodForm; //new add/edit form (blank)
use Pinnacle\Model\Utility;

class PhysicianController extends AbstractActionController
{
    protected $usersTable;
    protected $specialtyTable;
    protected $skillTable;
    protected $placementTable;
    protected $placemonthTable;
    protected $marketlogTable;
    protected $interviewTable;
    protected $descTable;
	protected $physiciansTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        $mlists = $this->getDescTable()->getSelectOptions($identity->uid);
        $form = new PhysiciansForm($this->getUsersTable(),$this->getSkillTable(),$mlists);
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages );
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
        //$part = $this->params()->fromRoute('part', 'Y'.date('Y'));
        $id = (int) $this->params()->fromRoute('id', 0);
        /*$yer = (int) substr($part,1,4);
        if( $yer && is_numeric($yer) ) $ar['yer'] = $yer;
        else $part = 'Y'.$ar['yer'];
        if( $id > 0 && $id < 13 ) $ar['mon'] = $id;
        else $id = $ar['mon'];*/        
        $months = Utility::getClass('Pinnacle\Model\DictMonths')->getMonths();
        $message = $_GET["message"];
        $request = $this->getRequest();
		$ar = $this->getPhysiciansTable()->selectPhysician($id);		
		$comments = $this->getPhysiciansTable()->getPhysicianComments($id);
		$rec = $this->getContractsTable()->getRecruiters();
		$users = $this->getContractsTable()->getUsers();
		$sources = $this->getPhysiciansTable()->getSources2();
		
		$history = $this->getPhysiciansTable()->getHistory($id);
		$passes = $this->getPhysiciansTable()->getPasses($id);
		$act = $this->getPhysiciansTable()->getActivities();
		$contracts = $this->getContractsTable()->getContracts(0); //0 for dr
		$mycontracts = $this->getContractsTable()->getMyContracts(0);
		$ltcontracts = $this->getContractsTable()->getLocumsContracts(0);
		$sources2 = $this->getPhysiciansTable()->getSourceHistory($id);
		$asscontracts = $this->getContractsTable()->getAssessmentContracts($id,0); //0 for dr 
		$pipl = $this->getPhysiciansTable()->getPiplAlert($id);
		//$sourcelist = $this->getPhysiciansTable()->getSourcesList();
		//echo var_dump($sources);		
		//echo var_dump($history);
		//echo $userid = $_COOKIE["phguid"];
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
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
							
		}
		if(isset($_POST["fupass_submit_btn"])){ //PC pass - not used?
			echo "PHYS CAREER PASS";
			//echo var_dump($_POST);
			/*$worked = $this->getPhysiciansTable()->passPhysCareer($_POST, $identity);
			if($worked)
				$message="Physician passed successfully";
			else
				$message="There was a problem with your request";*/
			
		}
		if(isset($_POST["enable_fuzion_submit"])){ //remove from tmp4 table to enable on fuzion report			
			$worked = $this->getPhysiciansTable()->enableFuzion($_POST);
			if($worked)
				$message="Success";
			else
				$message="There was a problem with your request";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");			
		}
		if(isset($_POST["sched_submit_btn"])){ //submit activity
			
			$worked = $this->getPhysiciansTable()->addActivity($_POST, $identity); //user midlevels function?
			if(!$worked)
				$message="There was a problem with adding your activity";
			else
				$message="Your activity was added";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");			
				
		}		
		if(isset($_POST["physician_pass_submit_btn"])){ 
			$worked = $this->getPhysiciansTable()->passTo($_POST, $identity);///////////user midlevels function?
			if(!$worked)
				$message="There was a problem with passing";
			else
				$message="Physician passed successfully";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
		}
		if($_GET["action"]=='cancel'){ //cancel interview...
			//echo "cancel".$_GET["id"];
			$id=$_GET["id"];
			if($id>0 && $id!="")
				$worked = $this->getMidlevelsTable()->cancelPIPL($_GET, $identity); //user midlevels function?
			if($worked)
				$message="The activity was cancelled";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
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
				$worked = $this->getPhysiciansTable()->createPresent($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the present";
				else
					$message="Present was added";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["pend_submit_btn"])){
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
				$worked = $this->getPhysiciansTable()->createPending($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the pending status";
				else
					$message="Pending was added";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["ch_submit"])){ //update lead lists, hotdoc, etc (action panel)
			$valid=true;			
			
				$worked = $this->getPhysiciansTable()->updateHotList($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating hot list info";
				else
					$message="Hot List info was updated";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			
		}
		
		if(isset($_POST["locums_doc_submit"])){ //update lead lists, hotdoc, etc (action panel)
			$valid=true;			
			
				$worked = $this->getPhysiciansTable()->updateLocumsDoc($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating locums info";
				else
					$message="Locums info was updated";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			
		}
		
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
				$worked = $this->getPhysiciansTable()->createInterview($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding the interview";
				else
					$message="Interview was added";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["resend_int_submit_btn"])){
			$valid=true;			
			if($valid)
			{
				$worked = $this->getPhysiciansTable()->createInterview($_POST, $identity); //do interview, but don't create interview entry				
				if(!$worked)
					$message="There was a problem resending the interview";
				else
					$message="Pre-Interview resent";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["loc_pres_submit_btn"])){
			$valid=true;			
			if($_POST["loc_location"]==""){
				$valid=false;
				$message.="Location is required<br/>";
			}
			if($_POST["loc_pres_date"]==""){
				$valid=false;
				$message.="Present date is required<br/>";
			}
			if($valid)
			{
				$worked = $this->getPhysiciansTable()->createLocumsPresent($_POST, $identity);
								
				
				if($worked){				
					$_POST["commenttxt"]="Locums Present Added - Doctor: <a href='/public/physician/view/".$_POST["ph_id"]."'>".$_POST["ph_name"]."</a> Location: ".$_POST["loc_location"]." Work Site: ".$_POST["loc_work_site"]." Spec: ".$_POST["loc_spec"]." Present Date: ".$_POST["loc_pres_date"];
					$_POST["ref_id"]=$_POST["ph_id"];
					$_POST["note_type"]=3;
					$worked = $this->getClientsTable()->addNewComment($_POST, $identity);
					$_POST["id"]=$_POST["loc_cli_id"];
					$_POST["ref_id"]="";
					$_POST["note_type"]=2;
					$worked = $this->getClientsTable()->addNewComment($_POST, $identity);
				
				}
				if(!$worked)
					$message="There was a problem adding the present";
				else
					$message="Present was added";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		if(isset($_POST["loc_pass_submit_btn"])){
			$valid=true;			
			if($_POST["phname"]==""){
				$valid=false;
				$message.="Provider Name is Mandatory<br/>";
			}			
			if($valid)
			{
				$worked = $this->getPhysiciansTable()->createLocumsPass($_POST, $identity);
				if(!$worked)
					$message="There was a problem passing to locums";
				else
					$message="Physician passed successfully";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
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
				$worked = $this->getPhysiciansTable()->addSource($_POST, $identity);
				if(!$worked)
					$message="There was a problem adding source";
				else
					$message="Source added successfully";
				return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");
			}
		}
		
		//credentials date section
		if(isset($_POST["ref_date_submit_btn"])){ //reference request submit
			$worked = $this->getPhysiciansTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["ama_date_submit_btn"])){ //ama date submit
			$worked = $this->getPhysiciansTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["preint_date_submit_btn"])){ //pre-int date submit
			$worked = $this->getPhysiciansTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["cand_ass_date_submit_btn"])){ //cand ass date
		
		}
		if(isset($_POST["packet_date_submit_btn"])){ //packet completed
			$worked = $this->getPhysiciansTable()->updateCredentialsDate($_POST, $identity);
				if(!$worked)
					$message="There was a problem updating credentials";
				else
					$message="Credentials updated successfully";
			return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");	
		}
		if(isset($_POST["candass_submit_btn"])){ //cand ass document 
			
			$_POST["candass_ctr_id"];
			$candarr = $this->getPhysiciansTable()->getCandidateAssessment($_POST);
			//echo var_dump($candarr);
			//return header("location: /word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&ctr_no=$candarr['ctr_no']&ctr_spec=$candarr['ctr_spec'];&ctct_addr_1=$candarr['ctct_addr_1']&ctct_addr_2=$candarr['ctct_addr_2']&ctct_addr_c=$candarr['ctct_addr_c']&ctct_addr_z=$candarr['ctct_addr_z']&ctr_location_c=$candarr['ctr_location_c']&ctct_st_code=$candarr['ctct_st_code']&ctr_location_s=$candarr['ctr_location_s']&sp_name=$candarr['sp_name']&ctct_name=$candarr['ctct_name'] \n");				
			/*$ar['ph_id']=$row->ph_id;
					$ar['phname']=$row->phname;
					$ar['phtitle']=$row->phtitle;
					$ar['ctr_id']=$row->ctr_id;				
					$ar['ctr_no']=$row->ctr_no;
					$ar['cliname']=$row->cliname;		
					$ar['addr1']=$row->addr1;		
					$ar['addr2']=$row->addr2;		
					$ar['city']=$row->city;		
					$ar['zip']=$row->zip;	
					$ar['state']=$row->state;	
					$ar['spec']=$row->spec;		
					$ar['specn']=$row->specn;	
					$ar['reqname']=$row->reqname;							
					$ar['reqtitle']=$row->reqtitle;		
					$ar['as_date']=$row->as_date;		
					$ar['motiv']=$row->motiv;		
					$ar['goals']=$row->goals;	
					$ar['fam']=$row->fam;		
					$ar['hob']=$row->hob;	
					$ar['items']=$row->items;		
					$ar['lic']=$row->lic;	*/
					//echo "location: /public/word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&cliname=".$candarr['cliname']."&ph_id=".$candarr['ph_id']."&phname=".$candarr['phname']."&phtitle=".$candarr['phtitle']."&ctr_id=".$candarr['ctr_id']."&ctr_no=".$candarr['ctr_no']."&addr1=".$candarr['addr1']."&addr2=".$candarr['addr2']."&city=".$candarr['city']."&zip=".$candarr['zip']."&state=".$candarr['state']."&specn=".$candarr['spec']."&reqname=".$candarr['reqname']."&reqtitle=".$candarr['reqtitle']."&as_date=".$candarr['as_date']."&motiv=".urlencode($candarr['motiv'])."&goals=".urlencode($candarr['goals'])."&fam=".urlencode($candarr['fam'])."&hob=".urlencode($candarr['hob'])."&items=".urlencode($candarr['items'])."&lic=".$candarr['lic']." \n";
			//return header("location: /public/word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&cliname=".$candarr['cliname']."&ph_id=".$candarr['ph_id']."&phname=".$candarr['phname']."&phtitle=".$candarr['phtitle']."&ctr_id=".$candarr['ctr_id']."&ctr_no=".$candarr['ctr_no']."&addr1=".$candarr['addr1']."&addr2=".$candarr['addr2']."&city=".$candarr['city']."&zip=".$candarr['zip']."&state=".$candarr['state']."&specn=".$candarr['spec']."&reqname=".$candarr['reqname']."&reqtitle=".$candarr['reqtitle']."&as_date=".$candarr['as_date']."&motiv=".$candarr['motiv']."&goals=".$candarr['goals']."&fam=".$candarr['fam']."&hob=".$candarr['hob']."&items=".$candarr['items']."&lic=".$candarr['lic']." \n");				
		return header("location: /public/word_doc2.php?filename=New Database\Reports\CANDIDATE ASSESSMENT MM.docx&cliname=".$candarr['cliname']."&ph_id=".$candarr['ph_id']."&phname=".$candarr['phname']."&phtitle=".$candarr['phtitle']."&ctr_id=".$candarr['ctr_id']."&ctr_no=".$candarr['ctr_no']."&addr1=".$candarr['addr1']."&addr2=".$candarr['addr2']."&city=".$candarr['city']."&zip=".$candarr['zip']."&state=".$candarr['state']."&specn=".$candarr['spec']."&reqname=".$candarr['reqname']."&reqtitle=".$candarr['reqtitle']."&as_date=".$candarr['as_date']."&motiv=".urlencode($candarr['motiv'])."&goals=".urlencode($candarr['goals'])."&fam=".urlencode($candarr['fam'])."&hob=".urlencode($candarr['hob'])."&items=".urlencode($candarr['items'])."&lic=".$candarr['lic']." \n");
		}
		
        $form = new PhysViewForm($months); //new form
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ar = $form->getData();
                $part = 'Y'.$ar['yer'];
                if( !$ar['mon'] ) $ar['mon'] = date('n');
                $id = $ar['mon'];
                return $this->redirect()->toRoute('physician',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/

        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'ar'=>$ar, 'comments' => $comments, 'rec' => $rec, 'act'=>$act,
		'users'=>$users, 'contracts'=>$contracts, 'mycontracts'=>$mycontracts, 'history'=>$history, 'ltcontracts'=>$ltcontracts, 'sources2' => $sources2, 
            /*'physician' => $this->getInterviewTable()->fetchAll($ar),*/ 'form' => $form, 'message'=>$message, 'sources' => $sources, 'pipl'=>$pipl,
            'messages' => $messages, 'id' => $id, 'passes'=>$passes, 'asscontracts'=>$asscontracts );
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
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["submit"])){ //submit 
			
			$worked = $this->getPhysiciansTable()->handleCtrChange($_POST, $identity);
			
			if(!$worked)
				$message="There was a problem with the contract change";
			else
				$message="Your contract change request was added";
			//return header("location: /public/midlevel/view/".$id."?message=".$message." \n");			
				
		}		
				
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    }
	
	public function editAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;

        //$ar = array('yer'=>date('Y'),'mon'=>date('n'));
        //$part = $this->params()->fromRoute('part', 'Y'.date('Y'));
        $id = (int) $this->params()->fromRoute('id', 0);
        
        $message = $_GET["message"];
        $request = $this->getRequest();
		
		$ar = $this->getPhysiciansTable()->getPhysician($id);	
		//echo var_dump($ar);
		$statuses = $this->getPhysiciansTable()->getPhyStatus();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		$types = $this->getPhysiciansTable()->getPracticeTypes();		
		$rec = $this->getContractsTable()->getRecruiters();
		$sources = $this->getPhysiciansTable()->getSources();
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$regs = $this->getPhysiciansTable()->getRegions();
		$srslink="";
		if($_GET["srs"]!='')
			$srslink="&srs=".$_GET["srs"];
		if($_POST["srs"]!='')
			$srslink="&srs=".$_POST["srs"];
		
        $form = new PhysmodForm(); //new form
		if(isset($_POST["submitedit"]))
		{
			$ar = $_POST; //switch to use post vals
			$valid=true;
			$message=$_GET["message"];
			//echo "submit";
			if($_POST["ph_licenses"]=="")
			{
				$message.="Please enter licensed states in the \"Licenses\" field.  <br/>";
				$valid=false;
			}
			if($_POST["ctct_name"]=="")
			{
				$message.="You must enter a Name <br/>";
				$valid=false;
			}
			if($_POST["ctct_title"]=="")
			{
				$message.="You must enter a Title  <br/>";
				$valid=false;
			}
			
			if($valid){
				$result = $this->getPhysiciansTable()->editPhysician($_POST, $id);
				//echo $result;
				$message.= "The physician info has been updated";
				if($result)
					return header("location: /public/physician/view/".$id."?message=".$message.$srslink." \n");	
					//return header("location: ".$_POST["basepath"]."/physician/view/".$id." \n");
				
			}
			//echo var_dump($_POST);
			
		}
		if(isset($_POST["cvrequest"]))
		{
			if($_POST["phemail"]=="") //$_POST["ctct_email"]
			{
				$message.="You must have an email saved for this physician! <br/>";
				$valid=false;
			}
			else{			
				$result = $this->getPhysiciansTable()->sendCV($_POST, $id);
			}
		}
        //$form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ar = $form->getData();
                $part = 'Y'.$ar['yer'];
                if( !$ar['mon'] ) $ar['mon'] = date('n');
                $id = $ar['mon'];
                return $this->redirect()->toRoute('physician',array('action'=>'interview',
                                    'part'=>$part,'id'=>$id));
            }
            else {
                if( !$messages ) $messages = array();
                $messages[] = 'Selection criteria is not valid';
            }
        }*/

        return array('phguser' => $identity, 'part' => $part, 'months' => $months, 'ar'=>$ar, 'rec' => $rec, 'statuses' => $statuses, 'states'=>$states,
            'types' => $types, 'form' => $form, 'message'=>$message, 'sources' => $sources, 'specs' => $specs, 'regs' => $regs, 'message'=>$message,
            'messages' => $messages, 'id' => $id );
    }
	
	public function addAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
        $ar = array();
        $id = (int) $this->params()->fromRoute('id', 0);
        $statuses = $this->getPhysiciansTable()->getPhyStatus();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);	
		$types = $this->getPhysiciansTable()->getPracticeTypes();		
		$rec = $this->getContractsTable()->getRecruiters();
		$sources = $this->getPhysiciansTable()->getSources();
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$regs = $this->getPhysiciansTable()->getRegions();
		
        $message = $_GET["message"];
        //$request = $this->getRequest();
		
		if(isset($_POST["submitadd"])){			
			$valid=true;
			$ar = $_POST;
			$message=$_GET["message"];			
			if($_POST["ph_licenses"]=="")
			{
				$message.="Please enter licensed states in the \"Licenses\" field.  <br/>";
				$valid=false;
			}
			if($_POST["ph_fname"]=="")
			{
				$message.="You must enter a First Name <br/>";
				$valid=false;
			}
			if($_POST["ph_lname"]=="")
			{
				$message.="You must enter a Last Name <br/>";
				$valid=false;
			}
			if($_POST["ctct_title"]=="")
			{
				$message.="You must enter a Title  <br/>";
				$valid=false;
			}
			if($_POST["ph_DOB"]!="")
			{
				if(preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}/",$_POST["ph_DOB"],$match)==0){
					$message.="DOB is not in valid format: YYYY-MM-DD <br/>";
					$valid=false;
				}
			}
			if($_POST["ctct_email"]!="")
			{
				$pharr = $this->getPhysiciansTable()->searchEmails($_POST["ctct_email"]);
				if(count($pharr)>0)
				{
					$message.="That email address is already in use. Please update the current record(s) <br/>";
					$valid=false;
				}
			}
			
			if($valid){
				$result = $this->getPhysiciansTable()->addPhysician($_POST);
				//echo $_POST["basepath"];//$result;
				if($result || $result>0){
					$message.="Physician added successfully!<br/>";
					return header("location: ".$_POST["basepath"]."/physician/edit/".$result." \n");
				}
				else{ $message.="There was a problem adding the doc<br/>"; }
			}
		}
		//else { $_POST=null; }
		//echo var_dump($_POST);
		
		//$ar = $this->getPhysiciansTable()->selectPhysician($id);	
		
		$rec = $this->getContractsTable()->getRecruiters();
		$sources = $this->getPhysiciansTable()->getSources();
        $form = new PhysmodForm(); //new form
        $form->setData($ar);
        

        return array('phguser' => $identity, 'part' => $part, /*'months' => $months, 'ar'=>$ar,*/ 'rec' => $rec,
		 'statuses' => $statuses, 'states'=>$states,
            'types' => $types,  'message'=>$message, 'specs' => $specs, 'regs' => $regs, 
             'form' => $form, 'message'=>$message, 'sources-' => $sources,
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
            'physician' => $this->getMarketlogTable()->fetchAll($ar), 'form' => $form,
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
            'physician' => $this->getPlacemonthTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
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
            'physician' => $this->getPlacementTable()->fetchAll($ar), 'form' => $form,
            'messages' => $messages );
    }*/
	
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
		$ph_id = $_POST["ph_id"];
		if($_POST["ph_id"]=='')
			$ph_id = $_GET["ph_id"];
		//echo $ctr_id;
		//echo $part;
		$ar = $this->getMidlevelsTable()->getContractInfo($ctr_id);		
		$ar = $ar[0]; //just one row
		$ar2 = $this->getPhysiciansTable()->getPlacementInfo($ctr_id, $ph_id);
		$ar3 = $this->getMidlevelsTable()->getContractSpec($ctr_id);		
		$specs = $this->getPhysiciansTable()->getSpecialtyOptions();
		$rec = $this->getContractsTable()->getRecruiters();
		$ph = $this->getPhysiciansTable()->getPhysician($ph_id);
				
        $request = $this->getRequest(); 
		
		if(isset($_POST["approvebtn"])){ //submit 
			$valid=true;
			if($_POST["pl_sent_date"]=="" || $_POST["pl_sent_date"]=="0000-00-00" || $_POST["pl_sent_date"]=="1970-01-01" || $_POST["pl_sent_date"]=="1969-12-31"){ 
				$valid=false;
				$message.="Please enter the contract sent date<br/>";
			}
			if($_POST["pl_date"]=="" || $_POST["pl_date"]=="0000-00-00" || $_POST["pl_date"]=="1970-01-01" || $_POST["pl_date"]=="1969-12-31"){ 
				$valid=false;
				$message.="Please enter the placement sent date<br/>";
			}
			if($_POST["pl_src_id"]=="0" && $_POST["pl_source"]==""){
				$valid=false;
				$message.="Please select or enter a source<br/>";
			}	
			if($_POST["pl_annual"]==""){
				$valid=false;
				$message.="Please enter an annual salary<br/>";
			}	
			if($_POST["pl_signing"]==""){
				$valid=false;
				$message.="Please enter signing bonus<br/>";
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
				$worked = $this->getPhysiciansTable()->addPlacement($_POST, $identity);
				if($worked)
					$message="The placement was added";
				else
					$message="There was a problem submitting the placement";
				
				return header("location: /public/physician/view/".$ph_id."?message=".$message." \n");
			}				
				
		}			
				
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ar' => $ar, 'ph'=>$ph, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'ctr_id'=>$ctr_id, 'specs'=>$specs, 'rec'=>$rec );
    
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
		
		
		$ar = $this->getPhysiciansTable()->selectPhysician($id);	
		$ph_ca_not = $ar["ph_ca_not"];		
		$ar = $this->getPhysiciansTable()->getAssessment($id, $ctr_id);		//change///////
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
			if($_POST["ph_id"]==""){
				$valid=false;
				$message.="No physician ID. Please go back.<br/>";
			}		
			if($_POST["as_date"]==""){
				$valid=false;
				$message.="Please enter a date.<br/>";
			}	
			
			if($valid)
			{
				//submit
				$worked = $this->getPhysiciansTable()->addAssessment($_POST, $identity);
				if($worked)
					$message="The assessment was updated<br/><a href='/public/physician/view/".$id."'>Return to Physician record</a>";
				else
					$message="There was a problem submitting the assessment";
			}				
		}
		
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        if ($request->isPost()) {
            $form->setData($request->getPost());             
        }
//echo var_dump($ar[0]);

        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
	public function exportAction() {
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
		$id = (int) $this->params()->fromRoute('id', 0);
		
		//$ph_id = $_GET["ph_id"];
				
		//$ar = $this->getPhysiciansTable()->selectPhysician($id);	
		$ar = $this->getPhysiciansTable()->getPhysician($id);
		
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $id, 'specs'=>$specs, 'rec'=>$rec );
    
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
		$ph_id = $_GET["ph_id"];
		
		
				
		$ar = $this->getPhysiciansTable()->getBilling($ctr_id, $ph_id);	
		
		//echo var_dump($ar); //$ph_id;
		
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $ph_id, 'specs'=>$specs, 'rec'=>$rec );
    
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
		$ph_id = $_GET["ph_id"];
		
		
				
		$ar = $this->getPhysiciansTable()->getAMACheck($ctr_id, $ph_id);	
		
		//echo var_dump($ar); //$ph_id;
		
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $ph_id, 'specs'=>$specs, 'rec'=>$rec );
    
    }
	
	public function contpreAction() {
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
		$states = Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);
		$message = $_GET["message"];
		//$id = (int) $this->params()->fromRoute('id', 0);		
		
		//$ctr_id = $_GET["ctr_id"];
		$ph_id = $_GET["ph_id"];
		if($ph_id=='')
			$ph_id = $_POST["ph_id"];
		
		if(isset($_POST["submit1"]))
		{
			$ar = $this->getPhysiciansTable()->searchClients($_POST);				
		}
		if(isset($ph_id))
		{
			$ar2 = $this->getPhysiciansTable()->getPhysician($ph_id);
		}	
		if(isset($_POST["submit2"]))
		{
			$result = $this->getPhysiciansTable()->addContPresent($_POST);				
			if($result)
				$message = "Contingency Present added successfully";
			else
				$message = "There was a problem adding the Contingency Present ";
		}
		
		//echo var_dump($ar); //$ph_id;
		
		$form = new PhysViewForm($months);		
        $form->setData($ar);
        /*if ($request->isPost()) {
            $form->setData($request->getPost());             
        }*/


        return array('phguser' => $identity, 'rec'=>$rec, 'states'=>$states, 'ph_ca_not'=>$ph_ca_not, 'contracts'=>$contracts, 'ar' => $ar, 'ar2' => $ar2, 'ar3' => $ar3, 'ph_id'=>$ph_id, 'users'=>$users,'form' => $form, 'message'=>$message, 'messages' => $messages, 'id' => $ph_id, 'specs'=>$specs, 'rec'=>$rec );
    
    }

    public function getUsersTable() {
        return $this->physicianTableFactory('usersTable','');
    }
    public function getSpecialtyTable() {
        return $this->physicianTableFactory('specialtyTable','');
    }
    public function getSkillTable() {
        return $this->physicianTableFactory('skillTable');
    }
    public function getPlacementTable() {
        return $this->physicianTableFactory('placementTable');
    }
    public function getPlacemonthTable() {
        return $this->physicianTableFactory('placemonthTable');
    }
    public function getMarketlogTable() {
        return $this->physicianTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->physicianTableFactory('interviewTable');
    }
	public function getPhysiciansTable() {
        return $this->physicianTableFactory('physiciansTable','');
		//return $this->reportTableFactory('physiciansTable','');
    }
	public function getClientsTable() {
        return $this->physicianTableFactory('clientsTable','');		
    }
    public function getDescTable() {
        return $this->physicianTableFactory('descTable','Mail\\'); 
    }
	public function getContractsTable() {
        return $this->physicianTableFactory('contractsTable',''); 
    }
	public function getMidlevelsTable() {
        return $this->physicianTableFactory('midlevelsTable',''); 
    }

    protected function physicianTableFactory($table,$submodel = 'Physician\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
