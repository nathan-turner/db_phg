<?php
// module/Pinnacle/src/Pinnacle/Form/MidlevelsForm.php:
namespace Pinnacle\Form;

use Pinnacle\Model\Utility;

class MidlevelsForm extends DateForm
{
    public function __construct(array $spec, array $mlArr) {
        parent::__construct(2,array(
                    'label1'=>'Available From: ',
                    'button'=>'btn btn-success btn-large',
                    'submit'=>'Search',
        ));
        $states = array('0' => ' ') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        $statuses = array('-1' => 'Any') + Utility::getClass('Pinnacle\Model\DictStatuses')->getSelectOptions(3); 
        $leads = Utility::getClass('Pinnacle\Model\DictSrcDates')->getSelectOptions();
        $visas = array('0' => ' ',
                      '1' => 'US Citizen',
                      '2' => 'Perm. Res.',
                      '3' => 'H visas',
                      '4' => 'Other visas',
                      '5' => 'None',
        );
        $exp = array('0' => 'Any',
                     '1' => 'Entry-level',
                     '2' => 'Mid-level',
                     '3' => 'Advanced',
                     '4' => 'Senior level',
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
            'name' => 'an_licenses',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-license',
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'License State: ',
                'value_options' => $states,
            ),
        ));
        $this->add(array(
            'name' => 'an_pref_state',
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
            'name' => 'an_status',
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
            'name' => 'an_citizen',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-visas',
            ),
            'options' => array(
                'label' => 'Visa: ',
                'value_options' => $visas,
            ),
        ));
        $this->add(array(
            'name' => 'an_type0',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'mdl-cats', // this id is referred in .js
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'Category: ',
                'value_options' => $spec,
            ),
        ));
        $this->add(array(
            'name' => 'an_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'mdl-spec', // this id is referred in .js
                'class' => 'phg-cour',
            ),
            'options' => array(
                'label' => 'Sub-Category: ',
                'value_options' => array('0'=>'Loading...'),
            ),
        ));
        $this->add(array(
            'name' => 'an_experience',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-experience',
            ),
            'options' => array(
                'label' => 'Experience: ',
                'value_options' => $exp,
            ),
        ));
        $this->add(array(
            'name' => 'an_date_add',
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
            'name' => 'an_id',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-xid',
            ),
            'options' => array(
                'label' => 'ID#: ',
            ),
        ));
        $this->add(array(
            'name' => 'an_lname',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-lname',
            ),
            'options' => array(
                'label' => 'Last Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'an_fname',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-fname',
            ),
            'options' => array(
                'label' => 'First Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'an_dea',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-dea',
            ),
            'options' => array(
                'label' => 'DEA Licensed: ',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
        ));
        $this->add(array(
            'name' => 'an_locums',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-locums',
            ),
            'options' => array(
                'label' => 'Pref Locums: ',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
        ));
        
        
        $this->add(array(
            'name' => 'instant',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'my-instant',
                'checked' => 'checked', // also change default in .js
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
