<?php
// module/Pinnacle/src/Pinnacle/Form/ClientsForm.php:
namespace Pinnacle\Form;

use Pinnacle\Model\Utility;
use Zend\Form\Form;

class ClientsForm extends Form
{
    public function __construct(array $mlArr) {
        parent::__construct('clientsform');
        $states = array('0' => ' Any State') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        $statuses = array('-1' => 'Any', '-2' => 'Past & Present', '-3' => 'Locum Tenens') + Utility::getClass('Pinnacle\Model\DictStatuses')->getSelectOptions(1);
        $types = array('0' => ' ') + Utility::getClass('Pinnacle\Model\DictClientTypes')->getClientTypes();
        $hotties = array('0' => ' ', '2' => 'Lead List 1', '4' => 'Lead List 2',
                     '8' => 'Pending', '1' => 'Long-term');
        $sources = array('-1' => ' ') + Utility::getClass('Pinnacle\Model\DictClientSources')->getClientSources();
        
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
            'name' => 'cli_state',
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
            'name' => 'cli_status',
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
            'name' => 'cli_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-type',
            ),
            'options' => array(
                'label' => 'Type: ',
                'value_options' => $types,
            ),
        ));
        $this->add(array(
            'name' => 'cli_source',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-source',
            ),
            'options' => array(
                'label' => 'Data Source: ',
                'value_options' => $sources,
            ),
        ));
        $this->add(array(
            'name' => 'cli_hotlist',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-hotlist',
            ),
            'options' => array(
                'label' => 'Hot: ',
                'value_options' => $hotties,
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
        
        $this->add(array(
            'name' => 'cli_id',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-xid',
            ),
            'options' => array(
                'label' => 'ID#: ',
            ),
        ));
        $this->add(array(
            'name' => 'cli_name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-cname',
            ),
            'options' => array(
                'label' => 'Facility: ',
            ),
        ));
        $this->add(array(
            'name' => 'cli_city',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-city',
            ),
            'options' => array(
                'label' => 'City: ',
            ),
        ));
        $this->add(array(
            'name' => 'cli_sys',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-sys',
            ),
            'options' => array(
                'label' => 'System: ',
            ),
        ));
        
        $this->add(array(
            'name' => 'cli_phone',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-phone',
            ),
            'options' => array(
                'label' => 'Phone: ',
            ),
        ));
        $this->add(array(
            'name' => 'cli_zip',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-zipcode',
            ),
            'options' => array(
                'label' => 'Zip: ',
            ),
        ));
        $this->add(array(
            'name' => 'cli_beds',
            'type' => 'Zend\Form\Element\Range',
            'attributes' => array(
                'id' => 'my-beds',
                'value' => 0,
                'min'=> 0, 'max'=>500, 'step'=>10,
            ),
            'options' => array(
                'label' => 'Beds > ',
            ),
        ));
        $this->add(array(
            'name' => 'instant',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'my-instant',
                'checked' => 'checked',
            ),
            'options' => array(
                'label' => 'Instant Search ',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
        ));
        $this->add(array(
            'name' => 'search',
            'type' => 'Zend\Form\Element\Button',
            'attributes' => array(
                'value' => 'search',
                'id' => 'searchbutton',
                'class' => 'btn btn-success btn-large',
                'data-loading-text' => 'Wait ....',
            ),
            'options' => array(
                'label' => 'Search',
            ),
        ));
        $this->setAttribute('autocomplete','off');

    }

}
