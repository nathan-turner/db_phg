<?php
// module/Pinnacle/src/Pinnacle/Controller/ResortController.php:
namespace Pinnacle\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Session\Container;

class ResortController extends AbstractRestfulController
{
    protected $retainedTable;
    protected $retainedMacTable;
    protected $fulistTable;
    protected $clientsTable;
    protected $contractsTable;
    protected $midlevelsTable;
    protected $physiciansTable;
    protected $descTable;

    public function create($data) {
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
    public function getList() {
        $response = $this->getResponse(); //new Response();
        $response->setStatusCode(Response::STATUS_CODE_501);
        $response->setContent('List Not Implemented'.PHP_EOL);
        return $response;
    }

    public function get($id) {
        $id = (int) $id;
        return $this->getResortObject('retainedResort',$id);
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

    public function retainedAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $part = $id > 9;
        if( $part ) $id -= 10;
        return $this->getResortObject($part?'retainedMacResort':'retainedResort',$id);
    }
    protected function retainedResort($result,$id) {
        $result->report = $this->getRetainedTable()->getResort($this,$id);
        $result->order = $this->getRetainedTable()->getOrderStrings();
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }
    protected function retainedMacResort($result,$id) {
        $result->report = $this->getRetainedMacTable()->getResort($this,$id);
        $result->order = $this->getRetainedMacTable()->getOrderStrings();
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }

    public function fulistAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('fulistResort',$id);
    }
    protected function fulistResort($result,$id) {
        $result->report = $this->getFulistTable()->getResort($this,$id);
        $result->order = $this->getFulistTable()->getOrderStrings();
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }
        
    public function clientsAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('clientsResort',$id);
    }
    protected function clientsResort($result,$id) {
        $container = new Container('clients_look1');
        $post = array();
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $post['emp_id'] = $result->emp_id;
                $container->post = $post;
            }
            elseif ($container->post !== null) $post = $container->post;
            else $result->error = 'Please repeat your search';
            $result->pages = $this->getClientsTable()->getResortPages($post);
        }
        elseif ($container->post !== null) { // pages of the result set, starting with 1
            $post = $container->post;
            $result->report = $this->getClientsTable()->getResort($this,$id,$post);
        }
        else $result->error = 'Please try your search again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }
        
    public function contractsAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('contractsResort',$id);
    }
    protected function contractsResort($result,$id) {
        $container = new Container('ontracts_look1');
        $post = array();
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $post['emp_id'] = $result->emp_id;
                $container->post = $post;
            }
            elseif ($container->post !== null) $post = $container->post;
            else $result->error = 'Please repeat your search';
            $result->pages = $this->getContractsTable()->getResortPages($post);
        }
        elseif ($container->post !== null) { // pages of the result set, starting with 1
            $post = $container->post;
            $result->report = $this->getContractsTable()->getResort($this,$id,$post);
        }
        else $result->error = 'Please try your search again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }

    public function physiciansAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('physiciansResort',$id);
    }
    protected function physiciansResort($result,$id) {
        $container = new Container('physicais_look1');
        $post = array();
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $post['emp_id'] = $result->emp_id;
                $container->post = $post;
            }
            elseif ($container->post !== null) $post = $container->post;
            else $result->error = 'Please repeat your search';
            $result->pages = $this->getPhysiciansTable()->getResortPages($post);
        }
        elseif ($container->post !== null) { // pages of the result set, starting with 1
            $post = $container->post;
            $result->report = $this->getPhysiciansTable()->getResort($this,$id,$post);
        }
        else $result->error = 'Please try your search again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }
        
    public function midlevelsAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('midlevelsResort',$id);
    }
    protected function midlevelsResort($result,$id) {
        $container = new Container('midmevels_look1');
        $post = array();
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $post['emp_id'] = $result->emp_id;
                $container->post = $post;
            }
            elseif ($container->post !== null) $post = $container->post;
            else $result->error = 'Please repeat your search';
            $result->pages = $this->getMidlevelsTable()->getResortPages($post);
        }
        elseif ($container->post !== null) { // pages of the result set, starting with 1
            $post = $container->post;
            $result->report = $this->getMidlevelsTable()->getResort($this,$id,$post);
        }
        else $result->error = 'Please try your search again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }

    public function mdesaddAction() { // mail desc add, what?
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('mdesaddResort',$id);
    }
    protected function mdesaddResort($result,$id) {
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $result->list_id = $this->getDescTable()->createDescList($result->emp_id,
                                                                         $post['name']);
                $result->name = htmlentities($post['name']);
            }
            else $result->error = 'Please provide name';
        }
        else $result->error = 'Please try again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }

    public function mdesdelAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('mdesdelResort',$id);
    }
    protected function mdesdelResort($result,$id) {
        $result->report = array();
        if( $id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                if( $post['confirm'] === 'jawohl' ) {
                    $this->getDescTable()->deleteDescList($result->emp_id,$id);
                    $result->list_id = $id;
                }
                else $result->error = 'Please confirm';
            }
            else $result->error = 'Please stop';
        }
        else $result->error = 'Please try again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }

    public function mdeseditAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('mdeseditResort',$id);
    }
    protected function mdeseditResort($result,$id) {
        $result->report = array();
        if( $id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $post = $request->getPost()->toArray();
                $post['uid'] = $result->emp_id;
                $post['list_id'] = $id; // this is redundant, not sure if I need it here
                $this->getDescTable()->editDescForm($post);
                $result->name = htmlentities($post['name']);
                $result->description = htmlentities($post['description']);
                $result->list_id = $id;
            }
            else $result->error = 'Please stop';
        }
        else $result->error = 'Please try again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
    }
    
    public function mdessaveAction() { // actually mlistsave
        $id = (int) $this->params()->fromRoute('id', 0);
        return $this->getResortObject('mdessaveResort',$id);
    }
    protected function mdessaveResort($result,$id) {
        $store = array('physician'=>'physicais_look1','client'=>'clients_look1','midlevel'=>'midmevels_look1');
        $method = array('physician'=>'physiciansTable','client'=>'clientsTable','midlevel'=>'midlevelsTable');
        $result->report = array();
        if( !$id ) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $y = $request->getPost()->toArray();
                $part = $y['part']; $lid = (int) $y['list'];
                $row = $this->getDescTable()->fetchOne($result->emp_id, $lid);
                if( $row && $store[$part] ) {
                    $container = new Container($store[$part]);
                    if ($container->post !== null) {
                        $post = $container->post;
                        $result->ret = $this->reportTableFactory($method[$part],'')
                            ->saveResortPages($result->emp_id,$lid,$post);
                        $result->list_id = $lid;
                    }
                    else $result->error = 'Search again';
                }
                else $result->error = 'Not found';
                //$result->report[] = "part $part";
                //$result->report[] = "lid $lid";
            }
            else $result->error = 'Please provide name';
        }
        else $result->error = 'Please try again';
        $result->id = $id;
        $result->code = Response::STATUS_CODE_200; // OK
        return $result;
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
    public function getMidlevelsTable() {
        return $this->reportTableFactory('midlevelsTable','');
    }
    public function getDescTable() {
        return $this->reportTableFactory('descTable','Mail');
    }

    protected function reportTableFactory($table,$submodel = 'Report\\') {
        if (!$this->$table) {
            $sm = $this->getServiceLocator();
            $this->$table = $sm->get("Pinnacle\\Model\\$submodel".ucfirst($table));
        }
        return $this->$table;
    }
}
