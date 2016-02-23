<?php
// module/Pinnacle/src/Pinnacle/Form/GeozipForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class GeozipForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('geozip');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'iplat',
            'attributes' => array(
                'type'  => 'text',
                'required' => 'required',
                'id' => 'geo-iplat',
                'value' => '0',
            ),
            'options' => array(
                'label' => 'Lattitude (numeric)',
            ),
        ));
        $this->add(array(
            'name' => 'iplong',
            'attributes' => array(
                'type'  => 'text',
                'required' => 'required',
                'id' => 'geo-iplong',
                'value' => '0',
            ),
            'options' => array(
                'label' => 'Longitude (numeric)',
            ),
        ));
        $this->add(array(
            'name' => 'miles',
            'attributes' => array(
                'type'  => 'text',
                'required' => 'required',
                'id' => 'geo-miles',
                'value' => '0',
            ),
            'options' => array(
                'label' => 'Distance (miles)',
            ),
        ));
        $this->add(array(
            'name' => 'zipcode',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'geo-code',
            ),
            'options' => array(
                'label' => 'Zip code lookup',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Search',
                'id' => 'submitbutton',
            ),
        ));
        $this->add(array(
            'name' => 'reset',
            'attributes' => array(
                'type'  => 'reset',
                'value' => 'Reset',
                'id' => 'resetbutton',
            ),
        ));
        
        $inputFilter = new InputFilter();
        $factory     = new InputFactory();
        // attach all inputs
        $inputFilter->add($factory->createInput(array(
            'name'     => 'iplat',
            'required' => true,
            'filters'  => array(
                array(
                    'name' => 'NumberFormat',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Float',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'iplong',
            'required' => true,
            'filters'  => array(
                array(
                    'name' => 'NumberFormat',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Float',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'miles',
            'required' => true,
            'filters'  => array(
                array(
                    'name' => 'NumberFormat',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Float',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'zipcode',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'PostCode',
                    'options' => array(
                        'locale' => 'en_US',
                    ),
                ),
            ),
        )));

        $this->setInputFilter($inputFilter);
    }
}

