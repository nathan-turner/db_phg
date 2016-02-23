<?php
// module/Pinnacle/src/Pinnacle/Form/UsermodForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\Hostname;
use Pinnacle\Model\Utility;

class UsermodForm extends Form
{

    public function __construct($part = null)
    {
        parent::__construct('usermod');
        $this->setAttribute('method', 'post');
        $states = Utility::getClass('Pinnacle\Model\DictStates');
        $dept = Utility::getClass('Pinnacle\Model\DictDepartments');

        $this->add(array(
            'name' => 'emp_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'emp_access',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'emp_realname',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        if($part === 'add') {
            $this->add(array(
                'name' => 'ctct_name',
                'attributes' => array(
                    'type'  => 'hidden',
                ),
            ));
            $this->add(array(
                'name' => 'first_name',
                'attributes' => array(
                    'type'  => 'text',
                    'required' => 'required',
                    'id' => 'first-name',
                ),
                'options' => array(
                    'label' => 'First Name*',
                ),
            ));
            $this->add(array(
                'name' => 'last_name',
                'attributes' => array(
                    'type'  => 'text',
                    'required' => 'required',
                    'id' => 'last-name',
                ),
                'options' => array(
                    'label' => 'Last Name*',
                ),
            ));
        }
        else {
            $this->add(array(
                'name' => 'ctct_name',
                'attributes' => array(
                    'type'  => 'text',
                    'required' => 'required',
                    'id' => 'my-name',
                ),
                'options' => array(
                    'label' => 'Name*',
                ),
            ));
        }
        $this->add(array(
            'name' => 'emp_uname',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'type'  => 'email',
                'required' => 'required',
                'id' => 'my-username',
            ),
            'options' => array(
                'label' => 'Email (User name)*',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_title',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'emp_dept',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-dept',
            ),
            'options' => array(
                'label' => 'Department',
                'value_options' => $dept->getDepartments(),
            ),
        ));
        $this->add(array(
            'name' => 'ctct_phone',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-phone',
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_ext1',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-ext1',
            ),
            'options' => array(
                'label' => 'Ext.',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_fax',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-fax',
            ),
            'options' => array(
                'label' => 'Fax',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_cell',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-cell',
            ),
            'options' => array(
                'label' => 'Cell Phone',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_hphone',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'my-home',
            ),
            'options' => array(
                'label' => 'Home Phone',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_url',
            'type' => 'Zend\Form\Element\Url',
            'attributes' => array(
                'type'  => 'url',
                'id' => 'my-url',
				'value' => 'http://www.phg.com',
            ),
            'options' => array(
                'label' => 'Web-site (http://www.example.com)',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_addr_1',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-addr1',
            ),
            'options' => array(
                'label' => 'Street Address line 1',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_addr_2',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-addr2',
            ),
            'options' => array(
                'label' => 'Street Address line 2',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_addr_c',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-addrc',
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));
        $this->add(array(
            'name' => 'ctct_st_code',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-state',
            ),
            'options' => array(
                'label' => 'State',
                'value_options' => $states->getStates(), //$this->states,
            ),
        ));
        $this->add(array(
            'name' => 'ctct_addr_z',
            'attributes' => array(
                'type'  => 'tel',
                'id' => 'zip-code',
            ),
            'options' => array(
                'label' => 'Zip code',
            ),
        ));
        $this->add(array(
            'name' => 'password3',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'set-password3',
                'title' => 'Leave blank if no change',
            ),
            'options' => array(
                'label' => 'New Password*',
                'label_attributes' => array(
                    'title' => 'Leave blank if no change',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'emp_status',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-status',
            ),
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '1' => 'Active', '0' => 'Former/Inactive',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'emp_admin',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'me-admin',
            ),
            'options' => array(
                'label' => 'DB Admin',
                'checked_value' => '1',
                'unchecked_value' => '0',
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
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-large',
            ),
        ));
        
        
        $inputFilter = $this->getInputFilter();
        if( !$inputFilter )
            $inputFilter = new InputFilter();
        $factory     = new InputFactory();
        // attach all inputs
        if($part === 'add') {
            /*$inputFilter->add($factory->createInput(array(
                'name'     => 'first_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 1, 'max' => 60,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'last_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 1, 'max' => 60,
                        ),
                    ),
                ),
            )));*/
        }
        else {
            $inputFilter->add($factory->createInput(array(
                'name'     => 'ctct_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 3, 'max' => 120,
                        ),
                    ),
                ),
            )));
        }
        $inputFilter->add($factory->createInput(array(
            'name' => 'emp_uname',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6, 'max' => 120,
                    ),
                ),
                array(
                    'name'    => 'EmailAddress',
                    'options' => array(
                        'allow' => Hostname::ALLOW_DNS |
                                   Hostname::ALLOW_LOCAL
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_title',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 60,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_phone',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 18,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_ext1',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 6,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_fax',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 18,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_cell',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 18,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_hphone',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 18,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_url',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 120,
                    ),
                ),
                array(
                    'name'    => 'Uri',
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_addr_1',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 60,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_addr_2',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 60,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_addr_c',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 50,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name' => 'ctct_addr_z',
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
        $inputFilter->add($factory->createInput(array(
            'name'     => 'password3',
            'required' => false,
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
