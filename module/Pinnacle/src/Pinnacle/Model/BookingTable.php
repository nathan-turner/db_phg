<?php
// module/Pinnacle/src/Pinnacle/Model/BookingTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;

class BookingTable extends Report\ReportTable
{

    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vphlookupsmall',
            '`ph_id`, `ctct_name`, `ctct_title`, `st_name`, `phone`, `ctct_addr_c`, `ctct_st_code`, `ph_spec_main`, `ph_status`, `ph_recruiter`, `ph_licenses`, `ph_cv_date`, `ph_src_date`, `ph_pref_state`, `ph_pref_region`, `ph_subspec`, `ph_datasrc`, `ph_citizen`, `ph_practice`, `ph_skill`, `ph_locums`, ctct_id');
    }
	
	/*public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Admin\Users());

        $this->initialize();
    }*/
	
	//new addBooking
	public function addBooking($post, $identity) {         
            $userid = $_COOKIE["phguid"];			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;
					$emp_name=$row->emp_realname;
				}
			}
			
			$running_dates_arr = explode(",", $post["running_dates"]);
			$overtime_dates_arr = explode(",", $post["overtime_dates"]);
			$night_dates_arr = explode(",", $post["night_dates"]);
			$weekend_dates_arr = explode(",", $post["weekend_dates"]);
			$holiday_dates_arr = explode(",", $post["holiday_dates"]);
			
						
			$result = $this->adapter->query('BEGIN',Adapter::QUERY_MODE_EXECUTE);
			
			$result = $this->adapter->query('call AddBookings(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
			,
            array(    
				$userid,
				$post["billable"],
				$post["recruiter"],
				$post["marketer"],
				$post["physid"],	//$physid,
				$post["physician"],
				$post["clientid"],	//$clientid,
				$post["client"],
				$post["state"],
				$post["city"],
				$post["credentialing_manager"],
				$post["dr_rate"],
				$post["cli_rate"],
				$post["phys_per_diem"], 	//$phys_per_diem,
				$post["bill_per_diem"], 	//$bill_per_diem,
				$post["phys_malpractice"],
				$post["bill_malpractice"], 	//$bill_malpractice,
				$post["dr_holiday_rate"],
				$post["cli_holiday_rate"],
				$post["dr_overtime_rate"],
				$post["cli_overtime_rate"],
				$post["dr_night_rate"],
				$post["cli_night_rate"],
				$post["dr_weekend_rate"],
				$post["cli_weekend_rate"],
				$post["dr_mileage"],
				$post["cli_mileage"],
				$post["flight_arranged_by"],
				$post["fly_from"],
				$post["fly_to"],
				$post["depart_date"],
				$post["return_date"],
				$post["car_arranged_by"],
				$post["pickup_loc"],
				$post["dropoff_loc"],
				$post["car_agency"],
				$post["housing_arranged_by"],
				$post["housing_location"],
				$post["location_city"],
				$post["pets"],
				$post["smoking"],
				$post["family_members"],
				$post["contract_attached"],
				$post["dr_confirmed"],
				$post["timesheets"],
				$post["malpractice_ins"],
				$post["credentials"],
				$post["addendum"],
				$post["contract_ext"],
				$post["work_address"],
				$post["assignment"],
				$post["deposit"],
				$post["prepay"],
				$post["deposit_amt"],
				$post["prepay_amt"],
				$post["book_status"]
			));	
			foreach ($result as $row) {
				$id = $row->id;
			}
			
			foreach ($running_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$running_sql .= "('".$id."','".$val."','".$post["runningdatehours".$val]."','".$post["dr_rate"]."','".$post["cli_rate"]."','Normal'),";
				}
			}
			//$running_sql = rtrim($running_sql,',');									
			
			foreach ($overtime_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$overtime_sql .= "('".$id."','".$val."','".$post["overtimedatehours".$val]."','".$post["dr_overtime_rate"]."','".$post["cli_overtime_rate"]."','Overtime'),";
				}
			}
			//$overtime_sql = rtrim($overtime_sql,',');
						
			foreach ($night_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$night_sql .= "('".$id."','".$val."','".$post["nightdatehours".$val]."','".$post["dr_night_rate"]."','".$post["cli_night_rate"]."','Night'),";
				}
			}
			//$night_sql = rtrim($night_sql,',');
			
			foreach ($weekend_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$weekend_sql .= "('".$id."','".$val."','".$post["weekenddatehours".$val]."','".$post["dr_weekend_rate"]."','".$post["cli_weekend_rate"]."','Weekend'),";
				}
			}
			//$weekend_sql = rtrim($weekend_sql,',');
			
			foreach ($holiday_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$holiday_sql .= "('".$id."','".$val."','".$post["holidaydatehours".$val]."','".$post["dr_holiday_rate"]."','".$post["cli_holiday_rate"]."','Holiday'),";
				}
			}
			//$holiday_sql = rtrim($holiday_sql,',');
			
			if($running_sql!='' && $overtime_sql!='' && $night_sql!='' && $weekend_sql!='' && $holiday_sql!='')
			{
			$insert_dates_sql = "INSERT INTO booking_dates (bookingsid, bookdate, hours, rate, bill_rate, rate_type) VALUES ".$running_sql.$overtime_sql.$night_sql.$weekend_sql.$holiday_sql;
			$insert_dates_sql = rtrim($insert_dates_sql,',');
			
			//echo $insert_dates_sql;
			$result = $this->adapter->query($insert_dates_sql,Adapter::QUERY_MODE_EXECUTE);
			}
 			$result = $this->adapter->query('COMMIT',Adapter::QUERY_MODE_EXECUTE);
			
			
			
			return true;
    }
	
	//update booking
	public function updateBooking($id, $post, $identity) {         
            $userid = $_COOKIE["phguid"];			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;
					$emp_name=$row->emp_realname;
				}
			}
			
			$running_dates_arr = explode(",", $post["running_dates"]);
			$overtime_dates_arr = explode(",", $post["overtime_dates"]);
			$night_dates_arr = explode(",", $post["night_dates"]);
			$weekend_dates_arr = explode(",", $post["weekend_dates"]);
			$holiday_dates_arr = explode(",", $post["holiday_dates"]);
			
			if($id!='' && $id!=0)
			{
			$result = $this->adapter->query('BEGIN',Adapter::QUERY_MODE_EXECUTE);
			
			$result = $this->adapter->query('call UpdateBookings(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
			,
            array(   
				$id,
				$userid,
				$post["billable"],
				$post["recruiter"],
				$post["marketer"],
				$post["physid"],	//$physid,
				$post["physician"],
				$post["clientid"],	//$clientid,
				$post["client"],
				$post["state"],
				$post["city"],
				$post["credentialing_manager"],
				$post["dr_rate"],
				$post["cli_rate"],
				$post["phys_per_diem"], 	//$phys_per_diem,
				$post["bill_per_diem"], 	//$bill_per_diem,
				$post["phys_malpractice"],
				$post["bill_malpractice"], 	//$bill_malpractice,
				$post["dr_holiday_rate"],
				$post["cli_holiday_rate"],
				$post["dr_overtime_rate"],
				$post["cli_overtime_rate"],
				$post["dr_night_rate"],
				$post["cli_night_rate"],
				$post["dr_weekend_rate"],
				$post["cli_weekend_rate"],
				$post["dr_mileage"],
				$post["cli_mileage"],
				$post["flight_arranged_by"],
				$post["fly_from"],
				$post["fly_to"],
				$post["depart_date"],
				$post["return_date"],
				$post["car_arranged_by"],
				$post["pickup_loc"],
				$post["dropoff_loc"],
				$post["car_agency"],
				$post["housing_arranged_by"],
				$post["housing_location"],
				$post["location_city"],
				$post["pets"],
				$post["smoking"],
				$post["family_members"],
				$post["contract_attached"],
				$post["dr_confirmed"],
				$post["timesheets"],
				$post["malpractice_ins"],
				$post["credentials"],
				$post["addendum"],
				$post["contract_ext"],
				$post["work_address"],
				$post["assignment"],
				$post["deposit"],
				$post["prepay"],
				$post["deposit_amt"],
				$post["prepay_amt"],
				$post["book_status"]
			));	
			/*foreach ($result as $row) {
				$id = $row->id;
			}*/
			
			foreach ($running_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$running_sql .= "('".$id."','".$val."','".$post["runningdatehours".$val]."','".$post["dr_rate"]."','".$post["cli_rate"]."','Normal'),";
				}
			}											
			
			foreach ($overtime_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$overtime_sql .= "('".$id."','".$val."','".$post["overtimedatehours".$val]."','".$post["dr_overtime_rate"]."','".$post["cli_overtime_rate"]."','Overtime'),";
				}
			}			
						
			foreach ($night_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$night_sql .= "('".$id."','".$val."','".$post["nightdatehours".$val]."','".$post["dr_night_rate"]."','".$post["cli_night_rate"]."','Night'),";
				}
			}			
			
			foreach ($weekend_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$weekend_sql .= "('".$id."','".$val."','".$post["weekenddatehours".$val]."','".$post["dr_weekend_rate"]."','".$post["cli_weekend_rate"]."','Weekend'),";
				}
			}			
			
			foreach ($holiday_dates_arr as $key=>$val)
			{
				if($val!='NaN-NaN-NaN' && $val!='0000-00-00')
				{
					$holiday_sql .= "('".$id."','".$val."','".$post["holidaydatehours".$val]."','".$post["dr_holiday_rate"]."','".$post["cli_holiday_rate"]."','Holiday'),";
				}
			}			
			
			$insert_dates_sql = "INSERT INTO booking_dates (bookingsid, bookdate, hours, rate, bill_rate, rate_type) VALUES ".$running_sql.$overtime_sql.$night_sql.$weekend_sql.$holiday_sql;
			$insert_dates_sql = rtrim($insert_dates_sql,',');
			
			//echo $insert_dates_sql;
			$result = $this->adapter->query($insert_dates_sql,Adapter::QUERY_MODE_EXECUTE);
			
 			$result = $this->adapter->query('COMMIT',Adapter::QUERY_MODE_EXECUTE);			
			}
			
			return true;
	}
	
	//get booking
	public function getBooking($id, $identity) {         
            		
		$result = $this->adapter->query('select * from bookings where idbookings=?'
			, array($id));
			if($result)
			{			
				foreach ($result as $row) {
					
				$post["billable"]=$row->billable;
				$post["recruiter"]=$row->recruiterid;
				$post["marketer"]=$row->marketerid;
				$post["physid"]=$row->physid;	//$physid,
				$post["physician"]=$row->physname;
				$post["clientid"]=$row->clientid;	//$clientid,
				$post["client"]=$row->clienttxt;
				$post["state"]=$row->statetxt;
				$post["city"]=$row->citytxt;
				$post["credentialing_manager"]=$row->credentialing_manager;
				$post["dr_rate"]=$row->provider_pay;
				$post["cli_rate"]=$row->billing_pay;
				$post["phys_per_diem"]=$row->phys_per_diem; 	//$phys_per_diem,
				$post["bill_per_diem"]=$row->bill_per_diem; 	//$bill_per_diem,
				$post["phys_malpractice"]=$row->phys_malpractice;
				$post["bill_malpractice"]=$row->bill_malpractice; 	//$bill_malpractice,
				$post["dr_holiday_rate"]=$row->dr_holiday_rate;
				$post["cli_holiday_rate"]=$row->cl_holiday_rate;
				$post["dr_overtime_rate"]=$row->dr_overtime;
				$post["cli_overtime_rate"]=$row->cl_overtime;
				$post["dr_night_rate"]=$row->dr_night_call;
				$post["cli_night_rate"]=$row->cl_night_call;
				$post["dr_weekend_rate"]=$row->dr_weekend;
				$post["cli_weekend_rate"]=$row->cl_weekend;
				$post["dr_mileage"]=$row->dr_mileage;
				$post["cli_mileage"]=$row->cl_mileage;
				$post["flight_arranged_by"]=$row->flight_arrangedby;
				$post["fly_from"]=$row->fly_from;
				$post["fly_to"]=$row->fly_to;
				$post["depart_date"]=$row->depart_date;
				$post["return_date"]=$row->return_date;
				$post["car_arranged_by"]=$row->rental_arranged_by;
				$post["pickup_loc"]=$row->pickup_loc;
				$post["dropoff_loc"]=$row->dropoff_loc;
				$post["car_agency"]=$row->rental_agency;
				$post["housing_arranged_by"]=$row->housing_arrangedby;
				$post["housing_location"]=$row->housing_loc;
				$post["location_city"]=$row->housing_city;
				$post["pets"]=$row->pets;
				$post["smoking"]=$row->smoking;
				$post["family_members"]=$row->family_members;
				$post["contract_attached"]=$row->contract_attached;
				$post["dr_confirmed"]=$row->dr_confirmed;
				$post["timesheets"]=$row->timesheets;
				$post["malpractice_ins"]=$row->malpractice_ins;
				$post["credentials"]=$row->credentials;
				$post["addendum"]=$row->pay_addendum;
				$post["contract_ext"]=$row->contract_ext;
				$post["work_address"]=$row->work_addr;
				$post["assignment"]=$row->assignment;
				$post["deposit"]=$row->deposit;
				$post["prepay"]=$row->prepay;
				$post["deposit_amt"]=$row->deposit_amt;
				$post["prepay_amt"]=$row->prepay_amt;
				$post["book_status"]=$row->book_status;
				}
			}
			$result = $this->adapter->query("select *, DATE_FORMAT(bookdate, '%Y-%m-%d') AS bookdate2 from booking_dates where bookingsid=? order by bookdate2"
			, array($id));
			if($result)
			{			
				foreach ($result as $row) 
				{
					if($row->rate_type=='Normal' && $row->bookdate2!='0000-00-00'){
						$running_dates .= $row->bookdate2.",";
						$post["running_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="runningdatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';
					}
					if($row->rate_type=='Overtime' && $row->bookdate2!='0000-00-00'){
						$overtime_dates .= $row->bookdate2.",";
						$post["overtime_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="overtimedatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';					
					}
					if($row->rate_type=='Night' && $row->bookdate2!='0000-00-00'){
						$night_dates .= $row->bookdate2.",";
						$post["night_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="nightdatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';					
					}
					if($row->rate_type=='Weekend' && $row->bookdate2!='0000-00-00'){
						$weekend_dates .= $row->bookdate2.",";
						$post["weekend_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="weekenddatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';					
					}
					if($row->rate_type=='Holiday' && $row->bookdate2!='0000-00-00'){
						$holiday_dates .= $row->bookdate2.",";
						$post["holiday_html"] .= '<span id="datetxt">'.$row->bookdate2.'</span><input type="text" class="datehours" name="holidaydatehours'.$row->bookdate2.'" value="'.$row->hours.'" /><br/>';					
					}
				}
				$post["running_dates"] = rtrim($running_dates,',');
				$post["overtime_dates"] = rtrim($overtime_dates,',');
				$post["night_dates"] = rtrim($night_dates,',');
				$post["weekend_dates"] = rtrim($weekend_dates,',');
				$post["holiday_dates"] = rtrim($holiday_dates,',');
			}
			
		return $post;	
	}
    
	public function getDoctorAndClient($post, $identity) 
	{
			$result = $this->adapter->query('select ctct_name
			from lstphysicians AS p LEFT JOIN lstcontacts AS c ON c.ctct_id=p.ph_ctct_id
			WHERE ph_id=? '
			, array($post["physid"]));
			if($result)
			{			
				foreach ($result as $row) {
					$phys_name=$row->ctct_name;
				}
			}
			$result = $this->adapter->query('select ctct_name
			from lstclients AS p LEFT JOIN lstcontacts AS c ON c.ctct_id=p.cli_ctct_id
			WHERE cli_id=?'
			, array($post["clientid"]));
			if($result)
			{			
				foreach ($result as $row) {
					$cli_name=$row->ctct_name;
				}
			}	
			$ar["phys_name"]=$phys_name;
			$ar["cli_name"]=$cli_name;
			
			return $ar;
			
	}
	
	//get booking
	public function getBookings($identity, $sort='idbookings') {         
            		//$sort='idbookings';
		$result = $this->adapter->query('select * from bookings order by '.$sort.' desc'
			, array($sort));
			if($result)
			{		
				$i=0;
				foreach ($result as $row) {
				$post[$i]["id"]=$row->idbookings;	
				$post[$i]["billable"]=$row->billable;
				$post[$i]["recruiter"]=$row->recruiterid;
				$post[$i]["marketer"]=$row->marketerid;
				$post[$i]["physid"]=$row->physid;	//$physid,
				$post[$i]["physician"]=$row->physname;
				$post[$i]["clientid"]=$row->clientid;	//$clientid,
				$post[$i]["client"]=$row->clienttxt;
				$post[$i]["state"]=$row->statetxt;
				$post[$i]["city"]=$row->citytxt;
				$post[$i]["credentialing_manager"]=$row->credentialing_manager;
				$post[$i]["dr_rate"]=$row->provider_pay;
				$post[$i]["cli_rate"]=$row->billing_pay;
				$post[$i]["phys_per_diem"]=$row->phys_per_diem; 	//$phys_per_diem,
				$post[$i]["bill_per_diem"]=$row->bill_per_diem; 	//$bill_per_diem,
				$post[$i]["phys_malpractice"]=$row->phys_malpractice;
				$post[$i]["bill_malpractice"]=$row->bill_malpractice; 	//$bill_malpractice,
				$post[$i]["dr_holiday_rate"]=$row->dr_holiday_rate;
				$post[$i]["cli_holiday_rate"]=$row->cl_holiday_rate;
				$post[$i]["dr_overtime_rate"]=$row->dr_overtime;
				$post[$i]["cli_overtime_rate"]=$row->cl_overtime;
				$post[$i]["dr_night_rate"]=$row->dr_night_call;
				$post[$i]["cli_night_rate"]=$row->cl_night_call;
				$post[$i]["dr_weekend_rate"]=$row->dr_weekend;
				$post[$i]["cli_weekend_rate"]=$row->cl_weekend;
				$post[$i]["dr_mileage"]=$row->dr_mileage;
				$post[$i]["cli_mileage"]=$row->cl_mileage;
				$post[$i]["flight_arranged_by"]=$row->flight_arrangedby;
				$post[$i]["fly_from"]=$row->fly_from;
				$post[$i]["fly_to"]=$row->fly_to;
				$post[$i]["depart_date"]=$row->depart_date;
				$post[$i]["return_date"]=$row->return_date;
				$post[$i]["car_arranged_by"]=$row->rental_arranged_by;
				$post[$i]["pickup_loc"]=$row->pickup_loc;
				$post[$i]["dropoff_loc"]=$row->dropoff_loc;
				$post[$i]["car_agency"]=$row->rental_agency;
				$post[$i]["housing_arranged_by"]=$row->housing_arrangedby;
				$post[$i]["housing_location"]=$row->housing_loc;
				$post[$i]["location_city"]=$row->housing_city;
				$post[$i]["pets"]=$row->pets;
				$post[$i]["smoking"]=$row->smoking;
				$post[$i]["family_members"]=$row->family_members;
				$post[$i]["contract_attached"]=$row->contract_attached;
				$post[$i]["dr_confirmed"]=$row->dr_confirmed;
				$post[$i]["timesheets"]=$row->timesheets;
				$post[$i]["malpractice_ins"]=$row->malpractice_ins;
				$post[$i]["credentials"]=$row->credentials;
				$post[$i]["addendum"]=$row->pay_addendum;
				$post[$i]["contract_ext"]=$row->contract_ext;
				$post[$i]["work_address"]=$row->work_addr;
				$post[$i]["assignment"]=$row->assignment;
				$post[$i]["deposit"]=$row->deposit;
				$post[$i]["prepay"]=$row->prepay;
				$post[$i]["deposit_amt"]=$row->deposit_amt;
				$post[$i]["prepay_amt"]=$row->prepay_amt;
				$post[$i]["book_status"]=$row->book_status;
				$i++;
				}
			}
		return $post;
	}
	
	public function searchPhysicians($term) {      //return array for ajax   
            $sql="";
			$ar = array();			
			
			if(strlen($term)>=3)
			{
				$sql .= " ctct_name LIKE '%".$term."%' ";
			}			
			//$q = "select * from vPhForAdd WHERE ".$sql." LIMIT 10";
		//SELECT * FROM lstcontacts as l LEFT JOIN lstPhysicians as p ON p.ph_ctct_id=l.ctct_id
			//$result = $this->adapter->query("select * from vPhForAdd WHERE ".$sql." LIMIT 10")->execute();
			$result = $this->adapter->query("SELECT ph_id, ctct_name, ph_spec_main FROM lstcontacts as l LEFT JOIN lstphysicians as p ON p.ph_ctct_id=l.ctct_id WHERE ".$sql." AND NOT ISNULL(ph_id) LIMIT 12")->execute();
			if($result)
			{
				$i=0;
				foreach ($result as $row) 
				{
					$ar[$i]["id"] = $row["ph_id"];
					$ar[$i]["label"] = $row["ctct_name"]." (".$row["ph_spec_main"].")";
					$ar[$i]["value"] = $row["ctct_name"];
					
					$i+=1;
				}			
			}			
			
			return $ar;
    }
	
	public function searchClients($term) {      //return array for ajax   
            $sql="";
			$ar = array();			
			
			if(strlen($term)>=3)
			{
				$sql .= " ctct_name LIKE '%".$term."%' AND NOT ISNULL(cli_id)";
			}						
		//SELECT * FROM lstcontacts as l LEFT JOIN lstPhysicians as p ON p.ph_ctct_id=l.ctct_id
			//$result = $this->adapter->query("select * from vPhForAdd WHERE ".$sql." LIMIT 10")->execute();
			$result = $this->adapter->query("SELECT cli_id, ctct_name FROM lstcontacts as l LEFT JOIN lstclients as c ON c.cli_ctct_id=l.ctct_id WHERE ".$sql." LIMIT 12")->execute();
			if($result)
			{
				$i=0;
				foreach ($result as $row) 
				{
					$ar[$i]["id"] = $row["cli_id"];
					$ar[$i]["label"] = $row["ctct_name"];
					$ar[$i]["value"] = $row["ctct_name"];
					
					$i+=1;
				}			
			}			
			
			return $ar;
    }
	
	public function getTotals($identity, $id) {         
            		//$sort='idbookings';
					//$id=(string)$id;
		$ar = array();	
		$result = $this->adapter->query('select * from bookings  where idbookings=? '
			, array($id));
			if($result)
			{					
				foreach ($result as $row) {
				$phys_malpractice=$row->phys_malpractice;	
				$bill_malpractice=$row->bill_malpractice;
				$phys_perdiem = $row->phys_per_diem;
				$bill_perdiem = $row->bill_per_diem;
				}
			}
		
		$result = $this->adapter->query("SELECT *, 
SUM(hours) AS monthhours,
DATE_FORMAT( bookdate, '%M') AS month2,
ROUND(SUM(bill_rate*hours),2) AS billtotal, MONTH(bookdate) AS bookmonth, 
ROUND(SUM(rate*hours),2) AS paytotal,
WEEK(bookdate) AS weekdate, year(bookdate) bookyear, COUNT(*) AS bookings_cnt
FROM booking_dates where bookingsid= ?
group by bookmonth
order by bookmonth, weekdate", array($id));;
			if($result)
			{
				$i=0;
				foreach ($result as $row) 
				{
					$i=$row->bookmonth; //use this instead of counter
					$ar[$i]["monthhours"] = $row->monthhours;
					
					$phys_perdiem_total =  $phys_perdiem*$row->bookings_cnt;
					$bill_perdiem_total =  $bill_perdiem*$row->bookings_cnt;
					$ar[$i]["phys_perdiem_total"] =  $phys_perdiem_total;
					$ar[$i]["bill_perdiem_total"] =  $bill_perdiem_total;

					$ar[$i]["billtotal"] = $row->billtotal+$bill_perdiem_total;
					$ar[$i]["paytotal"] = $row->paytotal+$phys_perdiem_total;
					$ar[$i]["bookmonth"] = $row->month2;				
					
					$ar[$i]["grossprofit"] = $row->billtotal-$row->paytotal;
					
					$ar[$i]["percent"] = @round(($ar[$i]["grossprofit"]/$row->billtotal)*100,2);
					
					
					
					$ar[$i]["phys_malpractice"] = $phys_malpractice;
					$ar[$i]["bill_malpractice"] = $bill_malpractice;
					
					$i+=1;
				}			
			}
			
			
		return $ar;
	}
	
	public function getMonthlyTotals($identity, $id) {         
            		//$sort='idbookings';
					//$id=(string)$id;
		$ar = array();	
		$result = $this->adapter->query('select * from bookings  where  book_status="Confirmed" AND idbookings=? '
			, array($id));
			if($result)
			{					
				foreach ($result as $row) {
				$phys_malpractice=$row->phys_malpractice;	
				$bill_malpractice=$row->bill_malpractice;
				$phys_perdiem = $row->phys_per_diem;
				$bill_perdiem = $row->bill_per_diem;
				}
			}
		
		$result = $this->adapter->query("SELECT *, 
SUM(hours) AS monthhours,
DATE_FORMAT( bookdate, '%M') AS month2,
ROUND(SUM(bill_rate*hours),2) AS billtotal, MONTH(bookdate) AS bookmonth, 
ROUND(SUM(rate*hours),2) AS paytotal,
WEEK(bookdate) AS weekdate, year(bookdate) bookyear, COUNT(*) AS bookings_cnt
FROM booking_dates  /*where bookingsid= ?*/
AS d
LEFT JOIN bookings AS b ON b.idbookings=d.bookingsid
WHERE book_status<>'Pending'
group by bookmonth
order by bookmonth, weekdate", array($id));;
			if($result)
			{
				$i=0;
				foreach ($result as $row) 
				{
					$i=$row->bookmonth; //use this instead of counter
					$ar[$i]["monthhours"] = $row->monthhours;
					
					$phys_perdiem_total =  $phys_perdiem*$row->bookings_cnt;
					$bill_perdiem_total =  $bill_perdiem*$row->bookings_cnt;
					$ar[$i]["phys_perdiem_total"] =  $phys_perdiem_total;
					$ar[$i]["bill_perdiem_total"] =  $bill_perdiem_total;

					$ar[$i]["billtotal"] = $row->billtotal+$bill_perdiem_total;
					$ar[$i]["paytotal"] = $row->paytotal+$phys_perdiem_total;
					$ar[$i]["bookmonth"] = $row->month2;				
					
					$ar[$i]["grossprofit"] = $row->billtotal-$row->paytotal;
					
					$ar[$i]["percent"] = @round(($ar[$i]["grossprofit"]/$row->billtotal)*100,2);
					
					
					$ar[$i]["bookingsid"] = $row->bookingsid;
					$ar[$i]["phys_malpractice"] = $phys_malpractice;
					$ar[$i]["bill_malpractice"] = $bill_malpractice;
					
					$i+=1;
				}			
			}
			
			
		return $ar;
	}
	
	
	public function addHotDoc($post) {         
         $userid = $_COOKIE["phguid"];			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;
					$emp_name=$row->emp_realname;
				}
			}
		if($userid!=''){
			$result = $this->adapter->query('insert into locumshotdocs (doc_name, last_contact, whentxt, location, notes, add_date, added_by, mod_date, mod_by, typetxt) values (?,?,?,?,?,NOW(),?,NOW(),?,?)'
			, array($post["name"],$post["last_contact"],$post["when"],$post["location"],$post["notes"],$userid,$userid,$post["type"]));
		}
			if($result)
			{					
				
			}
		return true;
	}
	
	public function getHotDoc($id) {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();		
			
		if($id!=''){
			$result = $this->adapter->query('select * from locumshotdocs where doc_id=? limit 1', array($id));
		
			if($result)
			{					
				foreach ($result as $row) 
				{
					$ar["name"]=$row->doc_name;
					$ar["last_contact"]=$row->last_contact;
					$ar["when"]=$row->whentxt;
					$ar["location"]=$row->location;
					$ar["notes"]=$row->notes;
					$ar["add_date"]=$row->add_date;
					$ar["added_by"]=$row->added_by;
					$ar["mod_date"]=$row->mod_date;
					$ar["mod_by"]=$row->mod_by;
					$ar["type"]=$row->typetxt;
				}
			}
		}
		return $ar;
	}
	
	public function editHotDoc($post, $id) {         
         $userid = $_COOKIE["phguid"];			
			
			$this->table='lstemployees';
			$select = new Select($this->table);
			$select					
			->where->equalTo('emp_id',$userid);			
			$resultSet = $this->selectWith($select);
			if($resultSet)
			{			
				foreach ($resultSet as $row) {
					$note_user=$row->emp_user_mod;
					$dept=$row->emp_dept;
					$emp_name=$row->emp_realname;
				}
			}
		if($userid!='' && $id!=''){
			$result = $this->adapter->query('update locumshotdocs set doc_name=?, last_contact=?, whentxt=?, location=?, notes=?, mod_date=NOW(), mod_by=?, typetxt=? WHERE doc_id=? limit 1'
			, array($post["name"],$post["last_contact"],$post["when"],$post["location"],$post["notes"],$userid,$post["type"],$id));
		}
			if($result)
			{					
				return true;
			}
			else 
				return false;
		
	}
	
	public function getHotDocs() {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();		
			
		
			$result = $this->adapter->query('select * from locumshotdocs order by doc_name, typetxt', array());
		
			if($result)
			{	
				$i=0;
				foreach ($result as $row) 
				{
					$ar[$i]["id"]=$row->doc_id;
					$ar[$i]["name"]=$row->doc_name;
					$ar[$i]["last_contact"]=$row->last_contact;
					$ar[$i]["when"]=$row->whentxt;
					$ar[$i]["location"]=$row->location;
					$ar[$i]["notes"]=$row->notes;
					$ar[$i]["add_date"]=$row->add_date;
					$ar[$i]["added_by"]=$row->added_by;
					$ar[$i]["mod_date"]=$row->mod_date;
					$ar[$i]["mod_by"]=$row->mod_by;
					$ar[$i]["type"]=$row->typetxt;
					$i++;
				}
			}
		
		return $ar;
	}
	
	public function getLocumsStats() {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();					
		
			//$result = $this->adapter->query('SELECT * FROM locums_stats order by stat_id desc LIMIT 1', array());
			$result = $this->adapter->query('SELECT * FROM (SELECT * FROM locums_stats order by stat_id desc ) as s left join lstemployees as e on e.emp_id=s.emp_id group by s.emp_id   ', array()); //do subquery to get only most recent
		
			$i=0;
			if($result)
			{					
				foreach ($result as $row) 
				{
					$ar[$i]["ytd"]=$row->loc_YTD;
					$ar[$i]["current_mon"]=$row->current_mon;
					$ar[$i]["next_mon"]=$row->next_mon;
					$ar[$i]["net_add"]=$row->net_add;
					$ar[$i]["outstanding_contracts"]=$row->outstanding_contracts;
					$ar[$i]["open_positions"]=$row->open_positions;	
					$ar[$i]["yearly_goal"]=$row->yearly_goal;	
					$ar[$i]["monthly_goal"]=$row->monthly_goal;	
					$ar[$i]["emp_id"]=$row->emp_id;	
					$ar[$i]["user_mod"]=$row->emp_uname;
					//gp_mtd 
					if($row->monthly_goal > 0)
						$ar[$i]["gp_mtd"] = round(($row->current_mon/$row->monthly_goal)*100);
					//gp_ytd
					if($row->yearly_goal > 0)
						$ar[$i]["gp_ytd"] = round(($row->loc_YTD/$row->yearly_goal)*100);
					$i++;
				}
			}
		
		return $ar;
	}
	
	public function setLocumsStats($post) {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();		
			
		if($post!=''){
			$result = $this->adapter->query('INSERT INTO locums_stats (loc_YTD, current_mon, next_mon, net_add, outstanding_contracts, open_positions, yearly_goal, monthly_goal, emp_id ) VALUES (?,?,?,?,?,?,?,?,?)', array($post["ytd"], $post["current_mon"], $post["next_mon"], $post["net_add"], $post["outstanding_contracts"], $post["open_positions"], $post["yearly_goal"], $post["monthly_goal"], $post["emp_id"] ));
		
			if($result)
			{					
				
			}
		}
		return true;
	}
	
	public function getPCStats() {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();				
		
			$result = $this->adapter->query('SELECT * FROM pc_stats order by stat_id desc LIMIT 1', array());
		
			if($result)
			{					
				foreach ($result as $row) 
				{
					$ar["month"]=$row->month;
					$ar["monthly_goal"]=$row->monthly_goal;
					$ar["monthly_amt"]=$row->monthly_amt;
					$ar["yearly_goal"]=$row->yearly_goal;
					$ar["yearly_amt"]=$row->yearly_amt;
					if($row->monthly_goal>0)
						$ar["rev_mtd"]= round(($row->monthly_amt/$row->monthly_goal)*100);
					if($row->yearly_goal>0)
						$ar["rev_ytd"]=	round(($row->yearly_amt/$row->yearly_goal)*100);		
				}
			}		
		return $ar;
	}
	
	public function setPCStats($post) {         
        $userid = $_COOKIE["phguid"];		
		$ar = array();	
			
		$month=date('m');
		if($post!=''){
			$result = $this->adapter->query('INSERT INTO pc_stats (month, monthly_goal, monthly_amt, yearly_goal, yearly_amt) VALUES (?,?,?,?,?)', array($month, $post["monthly_goal"], $post["monthly_amt"], $post["yearly_goal"], $post["yearly_amt"] ));		
			if($result)
			{					
				
			}
		}
		return true;
	}
    
}
