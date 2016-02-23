<?php
// module/Pinnacle/src/Pinnacle/Form/ContractsForm.php:
namespace Pinnacle\Form;

use Pinnacle\Model\Utility;
use Pinnacle\Model\UsersTable;
use Pinnacle\Model\MidcatTable;
use Pinnacle\Model\SpecialtyTable;

class ContractsForm extends DateForm
{
    public function __construct(UsersTable $uTable, SpecialtyTable $specTable, MidcatTable $midCat) {
        parent::__construct(2,array(
                    'label1'=>'Date From: ','button'=>'btn btn-success btn-large','submit'=>'Search',
        ));
        $spec = array('0'=>' ') + $specTable->getSelectOptions(1);
        $users = array('0'=>' ') + $uTable->getSelectOptions('R');
        $cats = $midCat->getSelectOptions();
        $states = array('0' => ' Any State') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        $statuses = array('-1' => 'Any') + Utility::getClass('Pinnacle\Model\DictStatuses')->getSelectOptions(2);
        
        $this->add(array(
            'name' => 'ctr_state',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-state',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'State: ',
                'value_options' => $states,
            ),
        ));
        $this->add(array(
            'name' => 'ctr_status',
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
            'name' => 'nu_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'mid-cat',
                'class' => 'phg-cour',
                'disabled' => 'disabled'
            ),
            'options' => array(
                'label' => 'Category: ',
                'value_options' => $cats,
            ),
        ));
        $this->add(array(
            'name' => 'ctr_spec',
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
            'name' => 'ctr_recruiter',
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
            'name' => 'ctr_no',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-xid',
            ),
            'options' => array(
                'label' => 'Contract#: ',
            ),
        ));
        $this->add(array(
            'name' => 'ctr_cli',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-cname',
            ),
            'options' => array(
                'label' => 'Facility: ',
            ),
        ));
        $this->add(array(
            'name' => 'ctr_city',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-city',
            ),
            'options' => array(
                'label' => 'City: ',
            ),
        ));
        $this->add(array(
            'name' => 'ctr_type',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-ctype',
            ),
            'options' => array(
                'label' => 'Type: ',
            ),
        ));
        $this->add(array(
            'name' => 'ctr_nurse',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'my-midl',
            ),
            'options' => array(
                'label' => 'Mid-level: ',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
        ));
        
        
        $this->add(array(
            'name' => 'instant',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'my-instant',
                //'checked' => 'checked', // also change default in .js
            ),
            'options' => array(
                'label' => 'Instant Search ',
                'checked_value' => '1',
                'unchecked_value' => '0',
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
