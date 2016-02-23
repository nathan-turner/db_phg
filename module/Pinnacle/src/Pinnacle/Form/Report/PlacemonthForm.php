<?php
// module/Pinnacle/src/Pinnacle/Form/Report/PlacemonthForm.php:
namespace Pinnacle\Form\Report;

use Pinnacle\Model\Utility;
use Pinnacle\Model\SpecialtyTable;
use Zend\Form\Form;

class PlacemonthForm extends Form
{
    public function __construct(SpecialtyTable $specTable) {
        parent::__construct('placemonth');
        $states = array('0' => ' All') + Utility::getClass('Pinnacle\Model\DictStates')->getSelectOptions(1);
        
        $spec = array('0'=>' All') + $specTable->getSelectOptions();
        $spec['---'] = 'Mid-Levels';
        
        $this->add(array(
            'name' => 'st_code',
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
            'name' => 'spec',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-spec',
                'class' => 'phg-cour span4',
            ),
            'options' => array(
                'label' => 'Specialty: ',
                'value_options' => $spec,
            ),
        ));

        $this->add(array(
            'name' => 'yer',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => array(
                'type'  => 'number',
                'id' => 'my-yer',
                'value' => date('Y'),
                'min'=> 1994, 'max'=>(date('Y')+1), 'step'=>1,
            ),
            'options' => array(
                'label' => 'Placements for the Year: ',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Select',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-large',
            ),
        ));

    }

}
