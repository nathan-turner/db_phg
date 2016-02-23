<?php
// module/Pinnacle/src/Pinnacle/Controller/LookupController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pinnacle\Form\GeozipForm; 
use Pinnacle\Model\Utility;
use Pinnacle\Model\Report\LookupTable;
use Zend\Session\Container;

class LookupController extends AbstractActionController
{
    protected $geozipTable;
    
    public function indexAction()
    {
        $form  = new GeozipForm();
		$states = /*array('0' => ' Any State') +*/ Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(0);
        $request = $this->getRequest();
		
		$id = (int) $this->params()->fromRoute('id');
		$container = new Container('looklist_pref1');
		$container->pg_size=25;
		//$report = array(); //need blank for paginator
		$part=$id;
		//echo $id;
        if ($request->isPost() || $id>=1) {
            $form->setData($request->getPost());
			if($request->isPost())
				$_SESSION['post-data'] = $_POST;
			else
				$_POST=$_SESSION['post-data'];


//echo "HERE".$_POST['phone'];
//echo var_dump($_POST);
			
			
			//$container->list_page = $id;
			
			$phonenum=trim($_POST['phone']);
				$phonenum1=str_replace('-','',str_replace('(','',str_replace(')','',$phonenum)));
			$like = addslashes(trim($phonenum1));
			//echo $like;
			//$report = $this->getLookupTable()->getPages(1, $_POST);			
			$arr = $this->getLookupTable()->getTypes();		
			$report = $this->getLookupTable()->getLookupPages($_POST, $id,$container->pg_size);
			//echo var_dump($report);
			$container->list_page = $id;
			
			if(!$report)
				$messages="There was a problem with your query";
			
			
            /*if ($form->isValid()) {
                $data = $form->getData();				
                if( $data['zipcode'] ) {
                    // get geo data by zip code
                    $data = $this->getGeozipTable()->getGeozip( $data['zipcode'] )->getArrayCopy();
                    $data['zipcode'] = '';
                    $form->setData($data);
                }
                $zips = $this->getGeozipTable()->getZipCodes($data);
                return array(
                    'form' => $form, 'zips' => $zips
                );                
            }*/
        }
        return array(
            'form' => $form, 'zips' => array(), 'states' => $states, 'list' => $row, 'arr'=>$arr,
                     'report'=> $report, 'messages'=> $messages,
                     'pgsize'=> $container->pg_size, 'part'=>$part
        );
    }

    public function geozipAction()
    {
        $form  = new GeozipForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                if( $data['zipcode'] ) {
                    // get geo data by zip code
                    $data = $this->getGeozipTable()->getGeozip( $data['zipcode'] )->getArrayCopy();
                    $data['zipcode'] = '';
                    $form->setData($data);
                }
                $zips = $this->getGeozipTable()->getZipCodes($data);
                return array(
                    'form' => $form, 'zips' => $zips
                );
                
            }
        }
        return array(
            'form' => $form, 'zips' => array()
        );
    }

    public function getGeozipTable()
    {
        if (!$this->geozipTable) {
            $sm = $this->getServiceLocator();
            $this->geozipTable = $sm->get('Pinnacle\Model\GeozipTable');
        }
        return $this->geozipTable;
    }
	
	public function getLookupTable()
    {
        if (!$this->lookupTable) {
            $sm = $this->getServiceLocator();
            $this->lookupTable = $sm->get('Pinnacle\Model\Report\LookupTable');
        }
        return $this->lookupTable;
    }
}
