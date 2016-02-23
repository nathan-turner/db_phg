<?php
// module/Pinnacle/src/Pinnacle/Controller/ResortController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use ZendService\LiveDocx\MailMerge;
//use Zend\Locale;

class AjaxController extends AbstractRestfulController
{	
    public function init()
	{
    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
	}

    public function getList() {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('List Not Implemented'.PHP_EOL);
        return $response;
    }
	
	public function create($data) {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('Create Not Implemented'.PHP_EOL);
        return $response;
    }
	public function createAction() {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('Create Not Implemented'.PHP_EOL);
        return $response;
    }
    public function update($id,$data) {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('Update Not Implemented'.PHP_EOL);
        return $response;
    }
    public function delete($id) {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('Delete Not Implemented'.PHP_EOL);
        return $response;
    }

    public function get($id) {
        $id = (int) $id;
        return $this->getResortObject('retainedResort',$id);
    }
	
	public function getAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		
		
		$arr = $this->getClientsTable()->selectContactDetails($id);
		$result->code = Response::STATUS_CODE_200; 
		//$result->emp_id = $id;
		$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function getSourceListAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		
		
		$arr = $this->getPhysiciansTable()->getSourceList($id);
		$result->code = Response::STATUS_CODE_200; 
		//$result->emp_id = $id;
		$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function getLocumsAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		
		
		$arr = $this->getClientsTable()->selectClient($id);
		$result->code = Response::STATUS_CODE_200; 
		//$result->emp_id = $id;
		$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	
	
	public function addContactAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        //$id = (int) $id;
		//$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		//$post = array();
		//$viewModel = new ViewModel();
        //$viewModel->setTerminal(true);
		
		$request = $this->getRequest();
            if ($request->isPost()) {
				
                //$post = $request->getPost()->toArray();
				$post = $request->getPost();	
				//$post = $this->params()->fromPost('backref');
				//$post = serialize($post);
					//$post = $this->getRequest()->getParams(); 
				//$post = decode($request->getPost());
				//$post = Json::decode($request->getPost(), Json::TYPE_ARRAY);
				//$post = Json::decode($request->getPost()->toArray(), Json::TYPE_ARRAY);
				//$post = $post->toArray();
				//$post = $_POST;				
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();
				//$post = $this->params()->fromQuery('backref');				
			}
			//$post = serialize($post);		
			
			$worked = $this->getClientsTable()->addNewContact($post, $identity);
			$message = $worked ? "Contact Added." : "There was a problem adding contact";
			

		//$arr = $this->getClientsTable()->selectContactDetails($id);
		$result->code = Response::STATUS_CODE_200; 
		//$result->emp_id = $id;
		//$result->arr = $arr;
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function updateContactAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        //$id = (int) $id;
		//$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {
				
                //$post = $request->getPost()->toArray();
				$post = $request->getPost();							
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();
				//$post = $this->params()->fromQuery('backref');				
			}
			//$post = serialize($post);		
			
			$worked = $this->getClientsTable()->updateContact($post, $identity);
			$message = $worked ? "Contact Updated." : "There was a problem updating the contact";
			

		
		$result->code = Response::STATUS_CODE_200; 
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function deleteContactAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        //$id = (int) $id;
		//$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {
				
                //$post = $request->getPost()->toArray();
				$post = $request->getPost();							
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();
				//$post = $this->params()->fromQuery('backref');				
			}
			//$post = serialize($post);		
			
			$worked = $this->getClientsTable()->deleteContact($post, $identity);
			$message = $worked ? "Contact Deleted." : "There was a problem deleting contact";

		
		$result->code = Response::STATUS_CODE_200; 
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function addCommentAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();		
				//$post = $this->getRequest()->getContent();
				//$json = json_decode($post, true);
				//$post = json_decode(trim($_GET["data"],"'"), true);
			}			
			
			$worked = $this->getClientsTable()->addNewComment($post, $identity);
			$message = $worked ? "Comment Added." : "There was a problem adding comment";	
			
			//$message = var_dump(json_decode($post["data"]));
			//$message = var_dump($post);
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function updateRatingAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getClientsTable()->updateRating($post, $identity);
			$message = $worked ? "Rating updated." : "There was a problem adding rating";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function cancelMeetingAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getClientsTable()->cancelMeeting($post, $identity);
			$message = $worked ? "Meeting Cancelled." : "There was a problem cancelling the meeting";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function scheduleActivityAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getClientsTable()->scheduleActivity($post, $identity);
			$message = $worked ? "Scheduled Activity." : "There was a problem scheduling activity";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function passToLocumsAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getClientsTable()->passToLocums($post, $identity);
			$message = $worked;// ? "Passed to Locums" : "There was a problem passing to Locums";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function addChangeRequestAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getContractsTable()->addChangeRequest($post, $identity);
			$message = $worked ? "Change Request Added." : "There was a problem adding Change Request";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function updateDirectMailAction() { //SWITCH TO POST AT SOME POINT
		$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();       
		$result = new \stdClass();		
		
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
			
			$worked = $this->getContractsTable()->updateDirectMail($post, $identity);
			$message = $worked ? "Updated direct mail info." : "There was a problem updating direct mail info";	
		
		$result->code = Response::STATUS_CODE_200; 		
		$result->message = $message;
		$result->post = $worked;//$post->backref;//$post["backref"];
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	

    protected function getResortObject($method,$id) {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        $result = new \stdClass();
        $result->code = Response::STATUS_CODE_403; // Forbidden
        if ($identity) {
            $result->emp_id = $identity->uid;
            if( method_exists( $this, $method ) ) 
                $result = $this->$method($result,$id);
            else {
                $result->code = Response::STATUS_CODE_404;
                $result->error = 'RPC Method not found';
            }
        } else
            $result->error = 'Please log in with your user name and password';
        // Encode it to return to the client:
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function searchPhysiciansAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
		
		$arr = $this->getPhysiciansTable()->searchPhysicians($post); //returns html
		$message = $arr;
		$result->code = Response::STATUS_CODE_200; 
		//$result->emp_id = $id;
		$result->message = $message;
		$result->post = $arr;
		//$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	
	public function searchPhysAction() {        
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
		$term=$post["term"];
		
		$arr = $this->getBookingTable()->searchPhysicians($term); //returns html
				
		//$result->code = Response::STATUS_CODE_200; 	
		
		$result = $arr;		
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        //$response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;		
    }
	
	public function searchCliAction() {        
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
		$term=$post["term"];
		
		$arr = $this->getBookingTable()->searchClients($term); //returns html
				
		//$result->code = Response::STATUS_CODE_200; 	
		
		$result = $arr;		
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        //$response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;		
    }
	
	public function getActivityAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}	
		//if($id=='')
			//$id=1;
		$date=$post["date"];
		$id=$post["user"];
		
		$arr = $this->getCalendarTable()->getUserActivities($id,$date); //returns html
		$message = $arr;
		$result->code = Response::STATUS_CODE_200;		
		//$result->message = $message; //redundant
		$result->post = $arr;
		//$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function getMonthActivityAction() {
        //$id = (int) $id;
		$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}	
		//if($id=='')
			//$id=1;
		$date=$post["date"];
		$id=$post["user"];
		
		$arr = $this->getCalendarTable()->getMonthActivities($id,$date); //returns html
		$message = $arr;
		$result->code = Response::STATUS_CODE_200;		
		//$result->message = $message; //redundant
		$result->post = $arr["html"];
		$result->thedate = $arr["date"];
		//$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function removeActivityAction() {        
		//$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
		
		$id=$post["id"];
		
		$worked = $this->getCalendarTable()->removeActivity($id); //returns html
		
		$result->code = Response::STATUS_CODE_200;		
		
		if($worked)
			$result->message = "Task was deleted successfully";
		else
			$result->message = "There was a problem deleting the task";
		//$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
	
	public function editActivityAction() {        
		//$id = (int) $this->params()->fromRoute('id', 0);
		$result = new \stdClass();
		$request = $this->getRequest();
            if ($request->isPost()) {			
                
				$post = $request->getPost();					
			}
			if ($request->isGet()) {                
				$post = $request->getQuery()->toArray();							
			}			
		
		$id=$post["id"];
		
		$arr = $this->getCalendarTable()->editActivity($id); //returns html
		
		$result->code = Response::STATUS_CODE_200;		
		
		
		$result->arr = $arr;
        $json = Json::encode($result);
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode($result->code);
        $response->getHeaders()->addHeaders(array('Content-Type'=>'application/json'));
        $response->setContent($json);
        return $response;
    }
    

   
    public function getRetainedTable() {
        return $this->reportTableFactory('retainedTable');
    }
    public function getRetainedMacTable() {
        return $this->reportTableFactory('retainedMacTable');
    }
    public function getFulistTable() {
        return $this->reportTableFactory('fulistTable');
    }
    public function getClientsTable() {
        return $this->reportTableFactory('clientsTable','');
    }
    public function getContractsTable() {
        return $this->reportTableFactory('contractsTable','');
    }
    public function getPhysiciansTable() {
        return $this->reportTableFactory('physiciansTable','');
    }
	public function getBookingTable() {
        return $this->reportTableFactory('bookingTable','');
    }
    public function getMidlevelsTable() {
        return $this->reportTableFactory('midlevelsTable','');
    }
    public function getDescTable() {
        return $this->reportTableFactory('descTable','Mail');
    }
	public function getCalendarTable() {
        return $this->reportTableFactory('calendarTable','');
    }

    protected function reportTableFactory($table,$submodel = 'Report\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
