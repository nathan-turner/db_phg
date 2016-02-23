<?php

class Core_Controller_Search extends SpcController {

    /**
     * Core Search Model
     *
     * @var object
     */
    public $search;

     /**
     * Constructor
     *
     * Inits core search model and userid to use it in its methods
     */
    public function __construct() {
        parent::__construct();
        $this->search = new Core_Model_Search;
    }

    public function search($word, $module) {
       if ($module == 'calendar') {
            $result = $this->search->search($word, $module);
            echo Spc::jsonEncode(array('result' => $result));
       }
    }
}