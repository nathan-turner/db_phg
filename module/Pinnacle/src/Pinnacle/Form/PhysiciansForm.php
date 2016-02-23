<?php
// module/Pinnacle/src/Pinnacle/Form/PhysiciansForm.php:
namespace Pinnacle\Form;

use Pinnacle\Model\Utility;
use Pinnacle\Model\UsersTable;
use Pinnacle\Model\Physician\SkillTable;

class PhysiciansForm extends DateForm
{
    public function __construct(UsersTable $uTable, SkillTable $specTable, array $mlArr) {
        parent::__construct(2,array(
                    'label1'=>'CV Date From: ',
                    'button'=>'btn btn-success btn-large',
                    'submit'=>'Search',
        ));
        $spec = array('0'=>' ') + $specTable->getSelectOptions(1);
        $users = array('0'=>' ') + $uTable->getSelectOptions('R');
        $states = array('0' => ' ') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
		$states2 = array('0' => ' ', '0null0'=> 'NULL') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        $statuses = array('-1' => 'Any') + Utility::getClass('Pinnacle\Model\DictStatuses')->getSelectOptions(3);
        $regions = array('-1' => 'Any') + Utility::getClass('Pinnacle\Model\DictRegions')->getRegions();
        $leads = Utility::getClass('Pinnacle\Model\DictSrcDates')->getSelectOptions();
        $fmgs = array('0' => ' ',
                      'AMG' => 'AMG',
                      'FMG' => 'FMG',
                      'CAN' => 'CAN',
                      'J1' => 'J1',
                      'H1B' => 'H1B',
        );
                      
        
        $this->add(array(
            'name' => 'mail_list',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-list-sel',
                'class' => 'myml-class',
            ),
            'options' => array(
                'label' => 'Save to list: ',
                'value_options' => $mlArr,
            ),
        ));
        $this->add(array(
            'name' => 'ctct_st_code',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-state',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'Home State: ',
                'value_options' => $states,
            ),
        ));
        $this->add(array(
            'name' => 'ph_licenses',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-license',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'License State: ',
                'value_options' => $states2,
            ),
        ));
        $this->add(array(
            'name' => 'ph_pref_state',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-pref-state',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'Pref State: ',
                'value_options' => $states,
            ),
        ));
        $this->add(array(
            'name' => 'ph_pref_region',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-pref-region',
            ),
            'options' => array(
                'label' => 'Pref Region: ',
                'value_options' => $regions,
            ),
        ));
        $this->add(array(
            'name' => 'ph_status',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-status',
            ),
            'options' => array(
                'label' => 'Status: ',
                'value_options' => $statuses,
            ),
        ));
        $this->add(array(
            'name' => 'ph_citizen',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ph-visas',
            ),
            'options' => array(
                'label' => 'AMG/FMG: ',
                'value_options' => $fmgs,
            ),
        ));
        $this->add(array(
            'name' => 'ph_spec_main',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-spec',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'Specialty: ',
                'value_options' => $spec,
            ),
        ));
        $this->add(array(
            'name' => 'ph_recruiter',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-recruiter',
            ),
            'options' => array(
                'label' => 'Recruiter: ',
                'value_options' => $users,
            ),
        ));
        $this->add(array(
            'name' => 'ph_src_date',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-src-date',
            ),
            'options' => array(
                'label' => 'Lead date: ',
                'value_options' => $leads,
            ),
        ));
        
        $this->add(array(
            'name' => 'ph_id',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-xid',
            ),
            'options' => array(
                'label' => 'ID#: ',
            ),
        ));
        $this->add(array(
            'name' => 'ph_lname',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-lname',
            ),
            'options' => array(
                'label' => 'Last Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'ph_fname',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-fname',
            ),
            'options' => array(
                'label' => 'First Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'ph_subspec',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-subspec',
            ),
            'options' => array(
                'label' => 'Subspecialty: ',
            ),
        ));
        $this->add(array(
            'name' => 'ph_locums',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-locums',
                'class' => 'checkbox inline',
            ),
            'options' => array(
                'label' => 'Prefers Locums',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label_attributes' => array( 'class' => 'checkbox inline' ),
            ),
        ));
		
		$this->add(array(
            'name' => 'has_cv',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'has-cv',
                'class' => 'checkbox inline',
            ),
            'options' => array(
                'label' => 'Has CV',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label_attributes' => array( 'class' => 'checkbox inline' ),
            ),
        ));
        
        
        $this->add(array(
            'name' => 'instant',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-instant',
                'checked' => 'checked', // also change default in .js
                'class' => 'checkbox inline',
            ),
            'options' => array(
                'label' => 'Instant Search',
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label_attributes' => array( 'class' => 'checkbox inline' ),
            ),
        ));
        $this->add(array(
            'name' => 'orcond',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-orcond',
                //'checked' => 'checked', // also change default in .js
                'class' => 'checkbox inline',
                'title' => 'Default is AND, unchecked',
            ),
            'options' => array(
                'label' => 'OR Condition for States: '.Utility::NBSP,
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label_attributes' => array( 'class' => 'checkbox inline' ),
            ),
        ));
        $this->add(array(
            'name' => 'pg_size',
            'type' => 'Zend\Form\Element\Range',
            'attributes' => array(
                'id' => 'my-pagesize',
                'value' => 25,
                'min'=> 25, 'max'=>200, 'step'=>25,
            ),
            'options' => array(
                'label' => 'Page Size: ',
            ),
        ));
        $this->setAttribute('autocomplete','off');

    }

}