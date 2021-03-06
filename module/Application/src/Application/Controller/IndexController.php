<?php
/**
 * Pinnacle Health Group (http://phg.com/)
 * 
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
// module/Application/src/Application/Controller/IndexController.php:

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\LoginForm;
use Application\Form\PreferenceForm;
use Application\Model\LoginService;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        //$auth = new LoginService();
        //$identity = $auth->checkAuth($this);
        
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        /* no redirect for main welcome page for now
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        */
        $flashMessenger = $this->flashMessenger();
        // $flashMessenger->addMessage('Flash message to test the layout');
        $messages = null;
        if ($flashMessenger->hasMessages()) {
            $messages = $flashMessenger->getMessages();
        }

        return new ViewModel(array('phguser'=>$identity,'messages'=>$messages));
    }
    
    public function linxAction()
    {
        /*$identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        return array('phguser'=>$identity);*/
        return new ViewModel();
    }

    public function preferenceAction() {
        $auth = $this->getServiceLocator()
            ->get('Application\Model\LoginService');

        if ($auth->hasIdentity()) {
            $ermsg = '';
            $identity = $auth->getIdentity();
            $form = new PreferenceForm();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
    
                if ($form->isValid()) {
                    $data = $form->getData();
                    $old_pass = $data['password0'];
                    $new_pass1 = $data['password1'];
                    $new_pass2 = $data['password2'];
                    if( $new_pass1 === $new_pass2 ) {
                        // ok. try to change
                        $result = $auth->changePassword($old_pass,$new_pass1);
                        if( $result ) {
                            $this->flashMessenger()->addMessage('Your preferences are saved.');
                            return $this->redirect()->toRoute('home');
                        }
                        else $ermsg = 'Fail: old password is incorrect.';
                    }
                    else {
                        $ermsg = 'New passwords are not identical; please try again';
                        $data['password1'] = ''; $data['password2'] = '';
                        $form->setData($data);
                    }
                }
                else $ermsg = 'Correct problems listed below and try again';
            }
            return array('phguser'=>$identity, 'form' => $form, 'errors' => $ermsg);
        }
        return $this->redirect()->toRoute('login');
    }
    
    public function logoutAction() {
        $auth = new LoginService();

        if ($auth->hasIdentity()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $del = $request->getPost('lgout', 'No');
                if ($del == 'Yes') {
                    // Identity exists; get rid of it
                    $auth->clearIdentity();
                    $this->flashMessenger()->addMessage('You are now logged out.');
                }
                return $this->redirect()->toRoute('home');
            }
            $identity = $auth->getIdentity();
            return array('phguser'=>$identity);
        }
        return $this->redirect()->toRoute('home');
    }
    
    public function loginAction() {
        $form = new LoginForm();
        $request = $this->getRequest();
        $ermsg = '';
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $my_user = $data['username'];
                $my_pass = $data['password'];
                if( !empty($my_pass) && !empty($my_user) ) {
                    $auth = $this->getServiceLocator()
                        ->get('Application\Model\LoginService');
    
                    $result = $auth->loginAuth($my_user,$my_pass);
           
                    if (!$result->isValid()) {
                        // Authentication failed; print the reasons why
                        foreach ($result->getMessages() as $message) {
                            $ermsg .= "$message\n";
                        }
                        $data['username'] = ''; $data['password'] = '';
                        $form->setData($data);
                    } else {
                        // Authentication succeeded
                        $this->flashMessenger()->addMessage('You are now logged in.');
                        return $this->redirect()->toRoute('home');
                    }
                }
                else $ermsg = "Please enter user name and password\n";
            }
            else $ermsg = "Form is not valid\n";
        }
        return array('form' => $form, 'errors' => $ermsg);
    }
}
