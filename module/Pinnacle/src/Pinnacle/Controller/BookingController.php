<?php
// module/Pinnacle/src/Pinnacle/Controller/BookingController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\BookingForm;
use Pinnacle\Form\PhysViewForm;
use Pinnacle\Form\PhysmodForm; //new add/edit form (blank)
use Pinnacle\Model\Utility;
use Pinnacle\Model\BookingTable;

class BookingController extends AbstractActionController
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
			
		
		if(urldecode($_GET["sort"])!=""){
			$sort = urldecode($_GET["sort"]);		
			$arr = $this->getBookingTable()->getBookings($identity, $sort);
		}
		else{
			$arr = $this->getBookingTable()->getBookings($identity);
		}
		
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr );
    }

    public function viewAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 0);
		$part = $this->params()->fromRoute('part', '');
		
		$mr = $this->getClientsTable()->getLocumsMarketers();
		$rec = $this->getContractsTable()->getRecruiters();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);
		if(($id>=0 && $id!="")&&!isset($_POST["submit"]))
		{
			//echo $id;
			//echo $part;
			$arr = $this->getBookingTable()->getBooking($id, $identity);
			/*$_POST["running_dates"] = $arr["running_dates"];
			$_POST["overtime_dates"] = $arr["overtime_dates"];
			$_POST["night_dates"] = $arr["night_dates"];
			$_POST["weekend_dates"] = $arr["weekend_dates"];
			$_POST["holiday_dates"] = $arr["holiday_dates"];	*/		
			$_POST = $arr;			
		}
		
		if(isset($_POST["submit"]))
		{
			$valid=true;
			/*if($_POST["physid"]=="")
			{
				$message.="You must link to a physician.<br/>";
				$valid=false;
			}*/
			if($_POST["physician"]=="")
			{
				$message.="You must link to a physician.<br/>";
				$valid=false;
			}
			if($_POST["billable"]=="")
			{
				$message.="You must select if billable.<br/>";
				$valid=false;
			}
			if($_POST["client"]=="")
			{
				$message.="You must enter a client .<br/>";
				$valid=false;
			}
			if($_POST["city"]=="")
			{
				$message.="You must enter a city.<br/>";
				$valid=false;
			}
			/*if($_POST[""]=="")
			{
				$message.="You must enter a  .<br/>";
				$valid=false;
			}*/
			if($valid){			
				if($part=='add' || $part=='')
				{
				
					$result = $this->getBookingTable()->addBooking($_POST, $identity);
					if($result)
						$message = "Booking created successfully";
					//echo $result;
				}
				//echo "HERE".$part;
				if($part=='edit' && $id>0)
				{
				//echo "HERE";
					$result = $this->getBookingTable()->updateBooking($id, $_POST, $identity);
					if($result)
						$message = "Booking updated successfully";
				}
				elseif($id>0) { $message = "There was a problem accessing the record"; }
			}
			
			/*$_POST["running_dates"] = $this->formatDate($_POST["running_dates"]);
			$_POST["overtime_dates"] = $this->formatDate($_POST["overtime_dates"]);
			$_POST["night_dates"] = $this->formatDate($_POST["night_dates"]);
			$_POST["weekend_dates"] = $this->formatDate($_POST["weekend_dates"]);
			$_POST["holiday_dates"] = $this->formatDate($_POST["holiday_dates"]);*/
			//get hours before you format dates
			$_POST["running_html"] = $this->formatDateHours($_POST["running_dates"],'running',$_POST);
			$_POST["overtime_html"] = $this->formatDateHours($_POST["overtime_dates"],'overtime',$_POST);
			$_POST["night_html"] = $this->formatDateHours($_POST["night_dates"],'night',$_POST);
			$_POST["weekend_html"] = $this->formatDateHours($_POST["weekend_dates"],'weekend',$_POST);
			$_POST["holiday_html"] = $this->formatDateHours($_POST["holiday_dates"],'holiday',$_POST);
		}
		elseif($_GET["clientid"]!='' && $_GET["physid"]!='') {
			$_POST["clientid"] = $_GET["clientid"];
			$_POST["physid"] = $_GET["physid"];
			$arr = $this->getBookingTable()->getDoctorAndClient($_POST, $identity);
			$_POST["physician"]=$arr["phys_name"];
			$_POST["client"]=$arr["cli_name"];
		}					
			
			$_POST["running_dates"] = $this->formatDate($_POST["running_dates"]);
			$_POST["overtime_dates"] = $this->formatDate($_POST["overtime_dates"]);
			$_POST["night_dates"] = $this->formatDate($_POST["night_dates"]);
			$_POST["weekend_dates"] = $this->formatDate($_POST["weekend_dates"]);
			$_POST["holiday_dates"] = $this->formatDate($_POST["holiday_dates"]);
			
			//$post["running_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="runningdatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';
			
		

		$form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'marketers'=>$mr, 'rec'=>$rec, 'message' => $message, 'states'=>$states );
    }
	
	public function formatDate($indate){
		$arr = explode(',',$indate);
		foreach ($arr as $key=>$val)
		{
			if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				$date .= "'".$val."',";
		}
		$date = rtrim($date,',');
		$date = "[".$date."]";
		return $date;
	}
	
	public function formatDateHours($indate, $type, $post){
		$arr = explode(',',$indate);
		foreach ($arr as $key=>$val)
		{
			if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				$date .= '<span id="datetxt">'.$val.'</span><input type="text" class="datehours" name="runningdatehours'.$val.'" value="'.$post[$type.'datehours'.$val].'" /><br/>';
				
		}		
		return $date;
	}
	
	public function totalsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		//echo $id;				
				
			$arr = $this->getBookingTable()->getTotals($identity, $id);
		
		
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr );
    }
	
	public function monthtotalsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		//echo $id;				
				
			$arr = $this->getBookingTable()->getMonthlyTotals($identity, $id);
		
		
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr );
    }
	
	public function hotlistaddAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		//echo $id;				
				
		if(isset($_POST["submit"]))
		{
			$result = $this->getBookingTable()->addHotDoc($_POST);
			if($result)
				$message="Your doc has been added";
			else
				$message="There was a problem adding your doc";
		}		
		
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr, 'message'=>$message );
    }
	
	public function hotlisteditAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		$id = (int) $this->params()->fromRoute('id', 0);
		//echo $id;				
				
		$arr = $this->getBookingTable()->getHotDoc($id);		
		if(isset($_POST["submit"]) && $id>0)
		{
			$result = $this->getBookingTable()->editHotDoc($_POST, $id);
			if($result)
				$message="Your doc has been edited";
			else
				$message="There was a problem editing your doc";
		}		
		
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr, 'message'=>$message );
    }
	
	public function hotlistindexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
		//$id = (int) $this->params()->fromRoute('id', 0);
		//echo $id;				
				
		$arr = $this->getBookingTable()->getHotDocs();		
				
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr, 'message'=>$message );
    }
	
	public function locumstatsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
				
		if(isset($_POST["submit"]))
		{
			$this->getBookingTable()->setLocumsStats($_POST);	
			$messages[]="Your changes have been saved";
		}
		$arr = $this->getBookingTable()->getLocumsStats();		
				
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr, 'message'=>$message );
    }
	
	public function pcstatsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = $this->flashMessenger()->hasMessages()?
            $this->flashMessenger()->getMessages(): null;
				
		if(isset($_POST["submit"]))
		{
			$this->getBookingTable()->setPCStats($_POST);
			$messages[]="Your changes have been saved";			
		}
		$arr = $this->getBookingTable()->getPCStats();		
				
        $form = new BookingForm();
        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'arr'=>$arr, 'message'=>$message );
    }
	
	

    public function getUsersTable() {
        return $this->bookingTableFactory('usersTable','');
    }
    public function getSpecialtyTable() {
        return $this->bookingTableFactory('specialtyTable','');
    }
    public function getSkillTable() {
        return $this->bookingTableFactory('skillTable');
    }
    public function getPlacementTable() {
        return $this->bookingTableFactory('placementTable');
    }
    public function getPlacemonthTable() {
        return $this->bookingTableFactory('placemonthTable');
    }
    public function getMarketlogTable() {
        return $this->bookingTableFactory('marketlogTable');
    }
    public function getInterviewTable() {
        return $this->bookingTableFactory('interviewTable');
    }
	public function getPhysiciansTable() {
        return $this->bookingTableFactory('physiciansTable','');
		//return $this->reportTableFactory('physiciansTable','');
    }
	public function getClientsTable() {
        return $this->bookingTableFactory('clientsTable','');		
    }
    public function getDescTable() {
        return $this->bookingTableFactory('descTable','Mail\\'); 
    }
	public function getContractsTable() {
        return $this->bookingTableFactory('contractsTable',''); 
    }
	public function getBookingTable() {
        return $this->bookingTableFactory('bookingTable','');		
    }

    protected function bookingTableFactory($table,$submodel = 'Physician\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
