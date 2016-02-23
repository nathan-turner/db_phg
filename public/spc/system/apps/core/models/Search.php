<?php

class Core_Model_Search extends SpcDbTable {
    public function search($word, $module) {
        //search calendar events
        if ($module == 'calendar') {
            $calSearchModel = new Calendar_Model_Search;
            return $calSearchModel->search($word);
        }
    }
}