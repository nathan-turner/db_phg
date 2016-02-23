<?php
// module/Pinnacle/src/Pinnacle/Form/ClientmodForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\Hostname;
use Pinnacle\Model\Utility;
use Pinnacle\Model\ClientsTable;

class ClientmodForm extends Form
{

    public function __construct(Array $marketers, Array $types, Array $ar2)//$part = null)
    {
        parent::__construct('clientmod');
        $this->setAttribute('method', 'post');
        $states = Utility::getClass('Pinnacle\Model\DictStates');
        $dept = Utility::getClass('Pinnacle\Model\DictDepartments');
		$sources =  Utility::getClass('Pinnacle\Model\DictClientSources');
		$statuses =  Utility::getClass('Pinnacle\Model\DictStatuses');
		//$marketers = Utility::getClass('Pinnacle\Model\DictUsers');
		//$clientstable = new ClientsTable();
		//$marketers = $clientstable->getMarketers();
		
		

        $this->add(array(
            'name' => 'cli_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'cli_emp_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));		
		$this->add(array(
            'name' => 'cli_sys',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli-system',
            ),
            'options' => array(
                'label' => 'System',
            ),
        ));
		$this->add(array(
            'name' => 'cli_xid',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli-xid',
            ),
            'options' => array(
                'label' => 'Old ID#',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_name',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_title',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_company',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_company',
            ),
            'options' => array(
                'label' => 'Company',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_phone',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_phone',
            ),
            'options' => array(
                'label' => 'Phone',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_ext1',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_ext1',
            ),
            'options' => array(
                'label' => 'Ext1',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_fax',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_fax',
            ),
            'options' => array(
                'label' => 'Fax',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_ext2',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli-system',
            ),
            'options' => array(
                'label' => 'Ext2',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_title',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_title',
            ),
            'options' => array(
                'label' => 'Phone Notes',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_company',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_company',
            ),
            'options' => array(
                'label' => 'Fax Notes',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_email',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_addr_1',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_addr_1',
            ),
            'options' => array(
                'label' => 'Address',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_addr_2',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_addr_2',
            ),
            'options' => array(
                'label' => 'Address2',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_addr_c',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_addr_c',
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));
		$this->add(array(
            'name' => 'ctct_addr_z',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_addr_z',
            ),
            'options' => array(
                'label' => 'Zip Code',
            ),
        ));
		/*$this->add(array(
            'name' => 'ctct_st_code',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_st_code',
            ),
            'options' => array(
                'label' => 'State',
            ),
        ));*/
		$this->add(array(
            'name' => 'ctct_st_code',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctct_st_code',
				'value' => $ar->ctct_st_code
            ),
            'options' => array(
                'label' => 'State',
                'value_options' => $states->getSelectOptions(1),
            ),			
        ));
		$this->add(array(
            'name' => 'ctct_url',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctct_url',
            ),
            'options' => array(
                'label' => 'URL',
            ),
        ));
		$this->add(array(
            'name' => 'cli_sys',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_sys',
            ),
            'options' => array(
                'label' => 'System',
            ),
        ));
		$this->add(array(
            'name' => 'cli_fed_tax',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_fed_tax',
            ),
            'options' => array(
                'label' => 'Fed Tax ID#',
            ),
        ));
		$this->add(array(
            'name' => 'cli_beds',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_beds',
            ),
            'options' => array(
                'label' => 'Beds',
            ),
        ));
		$this->add(array(
            'name' => 'cli_grp',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_grp',
            ),
            'options' => array(
                'label' => 'Group',
            ),
        ));
		$this->add(array(
            'name' => 'cli_status',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'cli_status',
				'value' => $ar->cli_status
            ),
            'options' => array(
                'label' => 'Status',
                'value_options' => $statuses->getSelectOptions(1),
            ),
        ));
		$this->add(array(
            'name' => 'cli_source',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'cli_source',
				'value' => $ar->cli_source
            ),
            'options' => array(
                'label' => 'Source',
                'value_options' => $sources->getClientSources(),
            ),			
        ));
		$this->add(array(
            'name' => 'cli_emp_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'cli_emp_id',
				'value' => $ar->cli_emp_id
            ),
            'options' => array(
                'label' => 'Marketer',
                'value_options' => $marketers,
            ),
        ));
		$this->add(array(
            'name' => 'cli_type',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'cli_type',
				'value' => $ar->cli_type
            ),
            'options' => array(
                'label' => 'Type',
                'value_options' => $types,
            ),
        ));
		$this->add(array(
            'name' => 'cs_name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cs_name',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
		$this->add(array(
            'name' => 'cli_population',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_population',
            ),
            'options' => array(
                'label' => 'Population',
            ),
        ));
		$this->add(array(
            'name' => 'cli_specialty',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_specialty',
            ),
            'options' => array(
                'label' => 'Group Specialty',
            ),
        ));
		$this->add(array(
            'name' => 'cli_fuzion',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'cli_fuzion',
            ),
            'options' => array(
                'label' => 'Fuzion Master ID#',
            ),
        ));
		
		$this->add(array(
            'name' => 'cli_locumactive',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'cli_locumactive',
            ),
            'options' => array(
                'label' => 'Locumactive',
				'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
		if($ar2['cli_status']>0)
		{
			$this->add(array(
            'name' => 'ctobe',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'ctobe',
				'disabled' => 'true'
            ),
            'options' => array(
                'label' => 'To Be Deleted',
				'checked_value' => '1',
                'unchecked_value' => '0'				
            ),
        ));
		}
		else{
		$this->add(array(
            'name' => 'ctobe',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'ctobe',
            ),
            'options' => array(
                'label' => 'To Be Deleted',
				'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
		}
		if($ar2['cli_status']>0)
		{
			$this->add(array(
            'name' => 'cdupe',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'cdupe',
				'disabled' => 'true'
            ),
            'options' => array(
                'label' => 'Duplicate?',
				'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
		}
		else
		{
		$this->add(array(
            'name' => 'cdupe',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'cdupe',
            ),
            'options' => array(
                'label' => 'Duplicate?',
				'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
		}
		$this->add(array(
            'name' => 'cli_specnote',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'cli_specnote',
            ),
            'options' => array(
                'label' => 'Special Contract Terms',
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
		
       
        
        
        //$inputFilter = $this->getInputFilter();
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
            'name' => 'cli_xid',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 10,
                    ),
                ),
            ),
        )));
		
        $inputFilter->add($factory->createInput(array(
                'name'     => 'ctct_st_code',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'max' => 2,
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
                        'max' => 80,
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
            'required' => true,
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
        

        $this->setInputFilter($inputFilter);
    }
    

}
