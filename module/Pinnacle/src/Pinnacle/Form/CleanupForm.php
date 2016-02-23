<?php
// module/Pinnacle/src/Pinnacle/Form/CleanupForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Pinnacle\Model\Admin\Cleanup;

class CleanupForm extends Form
{
    public function __construct($part = null)
    {
        parent::__construct('cleanup');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'part',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-part',
            ),
            'options' => array(
                'label' => 'Record type',
                'value_options' => Cleanup::getTypes(),
            ),
        ));
        $this->add(array(
            'name' => 'no',
            'attributes' => array(
                'type'  => 'tel',
                'required' => 'required',
                'id' => 'rec-no',
            ),
            'options' => array(
                'label' => 'Record #',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                     'timeout' => 900,
                ),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-large',
            ),
        ));

        $inputFilter = $this->getInputFilter();
        if( !$inputFilter )
            $inputFilter = new InputFilter();
        $factory = new InputFactory();
        $inputFilter->add($factory->createInput(array(
            'name' => 'no',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 1, 'max' => 18,
                    ),
                ),
            ),
        )));
        $this->setInputFilter($inputFilter);
    }

}
