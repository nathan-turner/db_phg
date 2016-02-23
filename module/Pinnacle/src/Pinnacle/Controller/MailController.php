<?php
// module/Pinnacle/src/Pinnacle/Controller/MailController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\MailDescForm;
use Zend\Session\Container;

class MailController extends AbstractActionController
{
    protected $usersTable;
    protected $listsTable;
    protected $descTable;
    protected $mailcampTable;

    public function indexAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        $messages = null;
        if ($this->flashMessenger()->hasMessages()) {
            $messages = $this->flashMessenger()->getMessages();
        }
        $form = new MailDescForm();		
        
        return array('phguser' => $identity, 'messages' => $messages, 'form' => $form,
            'report' => $this->getDescTable()->fetchAll($identity->uid),
        );
    }
    
    public function listAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 1);
        $part = $this->params()->fromRoute('part', 'view');
        $lid = 0;

        $messages = null;
        if ($this->flashMessenger()->hasMessages()) {
            $messages = $this->flashMessenger()->getMessages();
        }
        $container = new Container('maillist_pref1');
        if( !$container->pg_size ) $container->pg_size = 25;
        if( $part{0} === 'L' ) {
            $lid = substr($part,1); $lid = (int) $lid;
        }
        if( $lid ) {
            $container->list_id = $lid;
            $container->list_page = $id;
            $row = $this->getDescTable()->fetchOne($identity->uid,$lid);
            $report = $this->getListsTable()->getPages($identity->uid,$lid,$id,$container->pg_size);
			
        }
        elseif( $container->list_id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $pgsize = $request->getPost('pgsize', 'x');
                if( is_numeric($pgsize) && $pgsize > 0 && $pgsize < 250 )
                    $container->pg_size = $pgsize;
            }
            $row = $this->getDescTable()->fetchOne($identity->uid,$container->list_id);
            $report = $this->getListsTable()->getPages($identity->uid,$container->list_id,$id,$container->pg_size);
            $part = "L$container->list_id";
            $container->list_page = $id;
        }
        else {
            if( ! $messages ) $messages = array();
            $messages[] = 'The supplied parameters are incorrect';
            $report = array();
        }
        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'list' => $row,
                     'report'=> $report, 'messages'=> $messages,
                     'pgsize'=> $container->pg_size);
    }
    
    public function listactAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 1);
        $part = $this->params()->fromRoute('part', 'view');
        $lid = 0;

        $container = new Container('maillist_pref1');
        if( $part{0} === 'L' ) {
            $lid = substr($part,1); $lid = (int) $lid;
        }
        if( !$lid ) $lid = $container->list_id;
        if( !$id ) $id = $container->list_page;
        if( $lid ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost();
                $listt = $this->getListsTable();
                $reslist = array();
		foreach( $post as $pk => $pv )
		    if( strpos($pk,'h_') == 1 && $pv ) {
			$rid = substr($pk,3);
			if( is_numeric($rid) ) $reslist[] = $rid;
		    }
                $sesscache = trim($post['sesscache']); $sessar = array();
                if( !empty($sesscache) ) {
                    if( $sesscache{strlen($sesscache)-1} === ',' ) $sesscache{strlen($sesscache)-1} = ' ';
                    if( $sesscache{0} === ',' ) $sesscache{0} = ' ';
                    $sessar = explode(',', trim($sesscache) );
                }
		if( $reslist || ( $post['actscope'] === 'MANY' && !empty($sesscache) ) ||
		    $post['act'] === 'PAGE' || $post['act'] === 'REST' ) {
                    if( $post['act'] === 'DEL' ) {
                        if( $post['actscope'] === 'MANY' && !empty($sesscache) ) 
                            $reslist = $reslist + $sessar;
                        $listt->deleteListSet($identity->uid,$lid,$reslist);
                    }
                    elseif( $post['act'] === 'KEEP' ) {
                        $scopem = null;
                        if( $post['actscope'] === 'MANY' && !empty($sesscache) ) 
                            $reslist = $reslist + $sessar;
                        else {
                            $fir_sto = $post['firsto'];
                            if( $fir_sto && $fir_sto{strlen($fir_sto)-1} === ',' )
                                $fir_sto{strlen($fir_sto)-1} = ' ';
                            $scopem = explode(',', trim($fir_sto) );
                        }
                        $listt->keepListSet($identity->uid,$lid,$reslist,$scopem);
                    }
                    elseif( $post['act'] === 'PAGE' ) {
                        $fir_sto = $post['firsto'];
                        if( $fir_sto && $fir_sto{strlen($fir_sto)-1} === ',' )
                            $fir_sto{strlen($fir_sto)-1} = ' ';
                        $ar = explode(',', trim($fir_sto) );
                        $listt->deleteListSet($identity->uid,$lid,$ar);
                    }
                    elseif( $post['act'] === 'REST' ) {
                        $fir_sto = $post['firsto'];
                        if( $fir_sto && $fir_sto{strlen($fir_sto)-1} === ',' )
                            $fir_sto{strlen($fir_sto)-1} = ' ';
                        $ar = explode(',', trim($fir_sto) );
                        $listt->keepListSet($identity->uid,$lid,$ar,null);
		    }
		} // reslist 
                $this->flashMessenger()->addMessage('The records were deleted');
            } // post
            else
                $this->flashMessenger()->addMessage('The records were not deleted');
        } // lid
        else 
            $this->flashMessenger()->addMessage('The submitted parameters are incorrect');
        return $this->redirect()->toRoute('mail',array('action'=>'list',
                            'part'=>$part, 'id' => $id));
    }
	
	public function listexportAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 1);
        $part = $this->params()->fromRoute('part', 'view');
        $lid = 0;
		
		if( $part{0} === 'L' ) {
            $lid = substr($part,1); $lid = (int) $lid;
        }
		/*echo var_dump($_POST["physlist"]);
		echo var_dump($_POST["clilist"]);
		echo var_dump($_POST["midlist"]);*/
		$type=$_GET["type"];
		$style=$_GET["style"]; //1-semicolon, 2-comma
		$locums=$_GET["locums"];
		/*if(isset($_POST["physexport"])){
			$list = $_POST["physlist"];
			$type=3;			
		}
		if(isset($_POST["cliexport"])){
			$list = $_POST["clilist"];
			$type=2;
		}
		if(isset($_POST["midexport"])){
			$list = $_POST["midlist"];
			$type=15;
		}	*/	
		$arr = $this->getListsTable()->getEmails($lid, $type, $locums);
		//echo $list;
		//echo $lid;
		
		return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'style'=>$style,
            'id' => $id, 'part' => $part, 'arr' => $arr
        );
	}
	
	public function listexportpAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 1);
        $part = $this->params()->fromRoute('part', 'view');
        $lid = 0;
		
		if( $part{0} === 'L' ) {
            $lid = substr($part,1); $lid = (int) $lid;
        }
		/*echo var_dump($_POST["physlist"]);
		echo var_dump($_POST["clilist"]);
		echo var_dump($_POST["midlist"]);*/
		$type=$_GET["type"];
		$style=$_GET["style"]; //1-semicolon, 2-comma
		$locums=$_GET["locums"];
		/*if(isset($_POST["physexport"])){
			$list = $_POST["physlist"];
			$type=3;			
		}
		if(isset($_POST["cliexport"])){
			$list = $_POST["clilist"];
			$type=2;
		}
		if(isset($_POST["midexport"])){
			$list = $_POST["midlist"];
			$type=15;
		}	*/	
		$arr = $this->getListsTable()->getEmails($lid, $type, $locums);
		//echo $list;
		//echo $lid;
		
		return array('phguser' => $identity, 'form' => $form, 'messages' => $messages, 'style'=>$style,
            'id' => $id, 'part' => $part, 'arr' => $arr
        );
	}

    public function goalsAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 0);
        $part = $this->params()->fromRoute('part', 'list');
        
        $y = $part === 'list'? date('Y'): substr($part,1,4);
        
        $messages = null;
        if ($this->flashMessenger()->hasMessages()) {
            $messages = $this->flashMessenger()->getMessages();
        }

        $form = new GoalsForm();
        $request = $this->getRequest();
        $user = $this->getGoalsTable()->getGoals($id,$y);
        if( $user ) {
            $form->bind($user);
            if ($request->isPost()) {
                $form->setData($request->getPost());
    
                if ($form->isValid()) {
                    $result = $this->getGoalsTable()->saveGoals($form->getData(),$id);
                    if( ! $messages ) $messages = array();
                    $messages[] = $result?'The record was saved.'
                                        :'The record was not saved.';
                }
                else {
                    if( ! $messages ) $messages = array();
                    $messages[] = 'The form is not valid';
                }
            }
        }
        else {
            $form->get('submit')->setValue('Add');
            if ($request->isPost()) {
                $form->setData($request->getPost());
    
                if ($form->isValid()) {
                    $user = new Goals();
                    $user->exchangeArray($form->getData());
                    $result = $this->getGoalsTable()->addGoals($user);
                    $this->flashMessenger()->addMessage($result?'The record was added.'
                                                        :'The record was not added.');
                    return $this->redirect()->toRoute('mail',array('action'=>'goals',
                            'part'=>$part, 'id' => $id));
                }
                else {
                    if( ! $messages ) $messages = array();
                    $messages[] = 'The form is not valid';
                }
            }
        }

        return array('phguser' => $identity, 'form' => $form, 'messages' => $messages,
            'users' => $this->getUsersTable()->fetchAll(), 'id' => $id, 'part' => $part
        );
    }
    
    public function usersAction() {
        // to add or edit users
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 0);
        $part = $this->params()->fromRoute('part', 'list');
        
        $form = new UsermodForm($part);
        $request = $this->getRequest();
        if( $part === 'add' ) {
            $form->get('submit')->setValue('Add');
            if ($request->isPost()) {
                $form->setData($request->getPost());
    
                if ($form->isValid()) {
                    $user = new Usermod();
                    $user->exchangeArray($form->getData());
                    $this->getUsermodTable()->addUser($user, $identity);
    
                    return $this->redirect()->toRoute('mail');
                }
            }
        }
        elseif( $part === 'edit' && $id ) {
            $form->get('submit')->setValue('Save');
            $user = $this->getUsermodTable()->getUser($id);
            $form->bind($user);
            if ($request->isPost()) {
                $form->setData($request->getPost());
    
                if ($form->isValid()) {
                    $this->getUsermodTable()->saveUser($form->getData(),$id, $identity);
    
                    return $this->redirect()->toRoute('mail');
                }
            }
        }
        else return $this->redirect()->toRoute('mail');
        
        return array('phguser' => $identity, 'form' => $form, 'id' => $id, 'part' => $part);
    }

    public function sourcesAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        $part = $this->params()->fromRoute('part', 'list');
        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part);
    }
	
	public function massemailAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		
		$message = urldecode($_GET["message"]);
        //$id = (int) $this->params()->fromRoute('id', 0);
        //$part = $this->params()->fromRoute('part', 'list');
		$ar = $this->getListsTable()->getMyEmails();
		//echo var_dump($ar);
        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'ar' => $ar, 'message'=>$message);
    }
	
	public function addemailAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		
		$mlists = $this->getDescTable()->getSelectOptions($identity->uid);
		if(isset($_POST["submit"])){
			$valid=true;
			if(!isset($_POST["subject"]) || trim($_POST["subject"])=='')
			{
				$valid=false;
				$message.="You must enter a subject.<br/>";
			}
			if(!isset($_POST["body"]) || $_POST["body"]=='')
			{
				$valid=false;
				$message.="You must enter body content.<br/>";
			}
			if(!isset($_POST["fromaddr"]) || $_POST["fromaddr"]=='')
			{
				$valid=false;
				$message.="You must enter a from addr.<br/>";
			}
			if(!isset($_POST["lists"]) || $_POST["lists"]=='')
			{
				$valid=false;
				$message.="You must select a mailing list.<br/>";
			}
			
			if($valid)
				$result = $this->getListsTable()->addEmail($_POST);
				
			if(!$result)
				$message.="There was a problem adding the email.<br/>";
			else
				$message.="The email was added.<br/>";
			return header("location: /public/mail/massemail?message=".$message." \n");
		}
		
		//echo var_dump($mlists);
        //$id = (int) $this->params()->fromRoute('id', 0);
        //$part = $this->params()->fromRoute('part', 'list');
        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'mlists'=>$mlists, 'message'=>$message );
    }
	
	public function editemailAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 1);
		//echo $id;
		$valid=true;
		
		if($id<=0 || $id==''){
			$message = "Invalid email id";
			$valid=false;
		}
		
		$mlists = $this->getDescTable()->getSelectOptions($identity->uid);
		
		$ar = $this->getListsTable()->getEmail($id);
		if(isset($_POST["submit"]) ){
			
			if(!isset($_POST["subject"]) || trim($_POST["subject"])=='')
			{
				$valid=false;
				$message.="You must enter a subject.<br/>";
			}
			if(!isset($_POST["body"]) || $_POST["body"]=='')
			{
				$valid=false;
				$message.="You must enter body content.<br/>";
			}
			if(!isset($_POST["fromaddr"]) || $_POST["fromaddr"]=='')
			{
				$valid=false;
				$message.="You must enter a from addr.<br/>";
			}
			if(!isset($_POST["lists"]) || $_POST["lists"]=='')
			{
				$valid=false;
				$message.="You must select a mailing list.<br/>";
			}
			
			if($valid)
				$result = $this->getListsTable()->editEmail($_POST, $id);
				
			if(!$result)
				$message.="There was a problem updating the email.<br/>";
			else
				$message.="The email was updated.<br/>";
		}
		        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'mlists'=>$mlists, 'message'=>$message, 'ar'=>$ar );
    }
	
	public function deletemailAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 1);
		//echo $id;
		$valid=true;
		
		if($id<=0 || $id==''){
			$message = "Invalid email id";
			$valid=false;
		}		
		
		$return = $this->getListsTable()->deleteEmail($id);
		
		if($return)
			$message="Email was deleted";
		else
			$message="There was a problem deleting the email ";
		return header("location: /public/massemail?message=".$message." \n");
		        
        //return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'mlists'=>$mlists, 'message'=>$message, 'ar'=>$ar );
    }
	
	//delete mailing list and recipients
	public function deletelistAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
		$id = (int) $this->params()->fromRoute('id', 1);
		//echo $id;
		$valid=true;
		
		if($id<=0 || $id==''){
			$message = "Invalid list id";
			$valid=false;
		}		
		
		if($valid && isset($id)){
			$return = $this->getListsTable()->deleteList($id);
		
			if($return)
				$message="List was deleted";
			else
				$message="There was a problem deleting the list ";
		
			return header("location: /public/mail?message=".$message." \n");
		}
		//return header("location: /public/mail/massemail?message=".$message." \n");
		        
        //return array('phguser' => $identity, 'id' => $id, 'part' => $part, 'mlists'=>$mlists, 'message'=>$message, 'ar'=>$ar );
    }
    
    public function getMailcampTable() {
        return $this->mailTableFactory('mailcampTable');
    }
    public function getDescTable() {
        return $this->mailTableFactory('descTable');
    }
    public function getUsersTable() {
        return $this->mailTableFactory('usersTable','');
    }
    public function getListsTable() {
        return $this->mailTableFactory('listsTable');
    }

    protected function mailTableFactory($table,$submodel = 'Mail\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
