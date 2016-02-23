<?php
// module/Pinnacle/src/Pinnacle/Controller/AdminController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Model\Admin\Usermod;
use Pinnacle\Form\UsermodForm;
use Pinnacle\Model\Admin\Cleanup;
use Pinnacle\Form\CleanupForm;
use Pinnacle\Model\Admin\Goals;
use Pinnacle\Form\GoalsForm;

class AdminController extends AbstractActionController
{
    protected $usersTable;
    protected $usermodTable;
    protected $cleanupTable;
    protected $goalsTable;

    public function indexAction() {
        // also lists users
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        return array('phguser' => $identity,
            'users' => $this->getUsersTable()->fetchAll(),
        );
    }
    
    public function cleanupAction() {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }
        
        $id = (int) $this->params()->fromRoute('id', 0);
        $part = $this->params()->fromRoute('part', 'list');

        $request = $this->getRequest();
        $messages = null;
        if ($this->flashMessenger()->hasMessages()) {
            $messages = $this->flashMessenger()->getMessages();
        }
        if( $part === 'list' || ! $id ) {
            $form = new CleanupForm();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $choice = new Cleanup();
                    $choice->exchangeArray($form->getData());
                    $record = $this->getCleanupTable()->fetchRow($choice->part,$choice->no);
                    if( $record ) {
                        $id = $record->id;
                        $record->part = $choice->part;
                        $record->verbose = $choice->getPart();
                    }
                    else {
                        $this->flashMessenger()->addMessage(
                        'Can not fetch the record for '.$choice->getPart().' '.
                        $choice->no);
                        return $this->redirect()->toRoute('admin',
                                        array('action' => 'cleanup'));
                    }
                    return array('phguser' => $identity, 'record' => $record,
                                 'id' => $id, 'part' => $choice->part,
                                 'messages' => $messages);
                }
            }
            return array('phguser' => $identity, 'form' => $form,
             'id' => $id, 'part' => $part, 'messages' => $messages);
        }
        else {
            if ($request->isPost()) {
                $del = $request->getPost('confirm', 'No');
                if ($del == 'Yes') {
                    // get rid of it
                    $result = $this->getCleanupTable()->deleteRow($part,$id);
                    $this->flashMessenger()->addMessage($result?'The record was deleted.'
                                                        :'The record was not deleted.');
                }
                return $this->redirect()->toRoute('admin', array('action' => 'cleanup'));
            }
        }
        
        return array('phguser' => $identity, 'id' => $id, 'part' => $part,
                     'messages' => $messages);
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
                    return $this->redirect()->toRoute('admin',array('action'=>'goals',
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
    
                    return $this->redirect()->toRoute('admin');
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
    
                    return $this->redirect()->toRoute('admin');
                }
            }
        }
        else return $this->redirect()->toRoute('admin');
        
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
    
    public function getCleanupTable() {
        return $this->adminTableFactory('cleanupTable');
    }
    public function getGoalsTable() {
        return $this->adminTableFactory('goalsTable');
    }
    public function getUsersTable() {
        return $this->adminTableFactory('usersTable','');
    }
    public function getUsermodTable() {
        return $this->adminTableFactory('usermodTable');
    }

    protected function adminTableFactory($table,$submodel = 'Admin\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
