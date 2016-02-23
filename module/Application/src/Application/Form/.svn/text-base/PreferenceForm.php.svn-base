<?php
// module/Application/src/Application/Form/PreferenceForm.php:
namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class PreferenceForm extends Form
{
    public function __construct($name = null)
    {
       // we want to ignore the name passed
        parent::__construct('preference');
        $this->setAttribute('method', 'post');
        /*
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type'  => 'email',
                'required' => 'required',
                'id' => 'my-username',
            ),
            'options' => array(
                'label' => 'User name (Email)',
            ),
        ));*/
        $this->add(array(
            'name' => 'password0',
            'attributes' => array(
                'type'  => 'password',
                'required' => 'required',
                'id' => 'my-password0',
            ),
            'options' => array(
                'label' => 'Old Password',
            ),
        ));
        $this->add(array(
            'name' => 'password1',
            'attributes' => array(
                'type'  => 'password',
                'required' => 'required',
                'id' => 'my-password1',
            ),
            'options' => array(
                'label' => 'New Password',
            ),
        ));
        $this->add(array(
            'name' => 'password2',
            'attributes' => array(
                'type'  => 'password',
                'required' => 'required',
                'id' => 'my-password2',
            ),
            'options' => array(
                'label' => 'New Password again',
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                     'timeout' => 600,
                ),
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

        $inputFilter = new InputFilter();
        $factory     = new InputFactory();
        // attach all inputs
        $inputFilter->add($factory->createInput(array(
            'name'     => 'password1',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6, 'max' => 100,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'password2',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6, 'max' => 100,
                    ),
                ),
            ),
        )));

        $this->setInputFilter($inputFilter);
    }
    

}
