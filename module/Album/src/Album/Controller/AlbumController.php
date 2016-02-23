<?php
// module/Album/src/Album/Controller/AlbumController.php:
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album; 
use Album\Form\AlbumForm; 

class AlbumController extends AbstractActionController
{
    protected $albumTable;
    
    public function indexAction()
    {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel(array('phguser'=>$identity,
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        $album = $this->getAlbumTable()->getAlbum($id);

        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $identity = $this->getServiceLocator()
            ->get('Application\Model\LoginService')->checkAuth();
        
        if (!$identity) {
            return $this->redirect()->toRoute('login');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
    }
     
    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
}