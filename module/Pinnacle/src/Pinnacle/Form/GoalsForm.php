<?php
// module/Pinnacle/src/Pinnacle/Form/GoalsForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
//use Zend\InputFilter\InputFilter;
//use Zend\InputFilter\Factory as InputFactory;

class GoalsForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('goals');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'g_emp_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'g_year',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'man_mode',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'g_class',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'g-class',
            ),
            'options' => array(
                'label' => 'Employee Goals Class',
                'value_options' => array(
                    '0' => 'No Goals',
                    '1' => 'Recruiting',
//                    '2' => 'Recruiting Assistant',
                    '3' => 'Marketing',
                    '4' => 'Marketing Assistant',
                ),
            ),
        ));
        for( $i = 1; $i <= 4; $i++ ) 
            for( $j = 1; $j <= 12; $j++ )
                $this->add(array(
                    'name' => "g_${i}_$j",
                    'type' => 'Zend\Form\Element\Number',
                    'attributes' => array(
                        'type'  => 'number',
                        'id' => "g-$i-$j",
                        'min' => 0, 'max' => 999, 'step' => 1,
                        'class' => 'input-small',
                        'value' => '0',
                    ),
                ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-large',
            ),
        ));
        // no real need for custom validatiors
    }
}
