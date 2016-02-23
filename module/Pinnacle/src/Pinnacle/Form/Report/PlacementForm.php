<?php
// module/Pinnacle/src/Pinnacle/Form/Report/PlacementForm.php:
namespace Pinnacle\Form\Report;

use Pinnacle\Model\Utility;
use Pinnacle\Model\SpecialtyTable;
use Pinnacle\Form\DateForm;

class PlacementForm extends DateForm
{
    public function __construct(SpecialtyTable $specTable) {
        parent::__construct(2,array(
                    'label1'=>'Placements From: ','button'=>'btn btn-success btn-large',
        ));
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

    }

}
