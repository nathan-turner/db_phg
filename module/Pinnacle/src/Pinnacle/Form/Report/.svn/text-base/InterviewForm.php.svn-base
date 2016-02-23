<?php
// module/Pinnacle/src/Pinnacle/Form/Report/InterviewForm.php:
namespace Pinnacle\Form\Report;

use Zend\Form\Form;

class InterviewForm extends Form
{
    public function __construct(array $months) {
        parent::__construct('ivform');
        $this->setAttribute('class', 'form-inline');
        
        $this->add(array(
            'name' => 'mon',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-month',
            ),
            'options' => array(
                'label' => 'Month: ',
                'value_options' => $months,
            ),
        ));
        
        $this->add(array(
            'name' => 'yer',
            'type' => 'Zend\Form\Element\Number',
            'attributes' => array(
                'type'  => 'number',
                'id' => 'my-yer',
                'min'=> 1994, 'max'=>(date('Y')+1), 'step'=>1,
            ),
            'options' => array(
                'label' => 'Year: ',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Select',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));

    }

}
