<?php

class SpcAjaxFrontController {

    private $_appRequest;

    private $_module;

    private $_controller;

    private $_action;

    private $_params;

    public function __construct() {
        $appReq = null;
        if (isset($_POST['spcAppRequest'])) {
            $appReq = $_POST['spcAppRequest'];
        } else if (isset($_GET['spcAppRequest'])) {
            $appReq = $_GET['spcAppRequest'];
        } else {
            throw new Exception('SPC Application Request Is Not Defined');
        }

        $this->_appRequest = explode('/', $appReq);
        $this->_module = $this->_appRequest[0];
        $this->_controller = ucfirst($this->_module) . '_Controller_' . ucfirst($this->_appRequest[1]);
        $this->_action = isset($this->_appRequest[2]) ? $this->_appRequest[2] : 'index';
        $this->_params = isset($_POST['params']) ? $_POST['params'] : (isset($_GET['params']) ? $_GET['params'] : array());
        if (isset($this->_params['isArray'])) {
            unset($this->_params['isArray']);
            $this->_params = array($this->_params);
        }

        if (Spc::getSender() == 'public') {
            $this->checkPrivateMethods($this->_action);
        }
    }

    public function dispatch() {
        if (!method_exists($this->_controller, $this->_action)) {
            throw new InvalidArgumentException($this->_controller . '::' . $this->_action . ' method doesn\'t exist');
        }

        $controller = new $this->_controller;
        call_user_func_array(array($controller, $this->_action), $this->_params);

        //if there is no content send a success AJAX message
        $obContents = ob_get_contents();
        if ($obContents == '') {
            echo Spc::jsonEncode();
        }

        ob_end_flush();
        exit;
    }

    public function checkPrivateMethods($action) {
        if (preg_match('/create|delete|update|insert|add/', $action)) {
            exit('!!!');
        }
    }
}