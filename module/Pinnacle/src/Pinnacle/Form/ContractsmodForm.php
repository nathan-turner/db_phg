<?php
// module/Pinnacle/src/Pinnacle/Form/ClientmodForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\Hostname;
use Pinnacle\Model\Utility;
use Pinnacle\Model\ClientsTable;
use Pinnacle\Model\ContractsTable;

class ContractsmodForm extends Form
{

    public function __construct(Array $ar2, Array $spec, Array $rec, Array $mid, Array $marketers)//$part = null)
    {
        parent::__construct('contractsmod');
        $this->setAttribute('method', 'post');
        $states = Utility::getClass('Pinnacle\Model\DictStates');
        $dept = Utility::getClass('Pinnacle\Model\DictDepartments');
		$sources =  Utility::getClass('Pinnacle\Model\DictClientSources');
		$statuses =  Utility::getClass('Pinnacle\Model\DictStatuses');
		//$marketers = Utility::getClass('Pinnacle\Model\DictUsers');
		//$clientstable = new ClientsTable();
		//$marketers = $clientstable->getMarketers();
		
		//echo var_dump($ar2);
		echo $ar2->ctr_nu_type;

        $this->add(array(
            'name' => 'ctr_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		$this->add(array(
            'name' => 'cli_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_status',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
       		
		$this->add(array(
            'name' => 'ctr_no',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_no',
            ),
            'options' => array(
                'label' => 'Contract #',
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_nurse',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(
                'type'  => 'radio',
                'id' => 'ctr_nurse',
				'disabled' => 'true',
				
				
            ),
            'options' => array(
                'label' => 'Search for',
                'value_options' => array('0'=>' Physician','1'=>' Mid-Level'),
            ),			
        ));
		
		$this->add(array(
            'name' => 'ctr_cli_bill',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_cli_bill',
				'value' => $ar->ctr_cli_bill,
            ),
            'options' => array(
                'label' => 'Child Client #',
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_spec',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_spec',
				'value' => $ar->ctr_spec
            ),
            'options' => array(
                'label' => 'Specialty',
                'value_options' => $spec,
            ),			
        ));
		
		$this->add(array(
            'name' => 'ctr_mid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_mid',
				'value' => $ar2["ctr_nu_type"]
				//'disabled' => 'true'
            ),			
            'options' => array(
                'label' => 'Specialty (Midlevel)',
                'value_options' => $mid,				
            ),			
        ));
		
		$this->add(array(
            'name' => 'st_name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'st_name',
				'disabled' => true,
            ),
            'options' => array(
                'label' => 'Status',
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_rec',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_rec',
				'value' => $ar->ctr_rec
            ),
            'options' => array(
                'label' => 'Recruiter',
                'value_options' => $rec,
            ),			
        ));
		$this->add(array(
            'name' => 'ctr_manager',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_manager',
				'value' => $ar->ctr_manager
            ),
            'options' => array(
                'label' => 'Co-Recruiter',
                'value_options' => $rec,
            ),			
        ));
		
		$this->add(array(
            'name' => 'datepicker',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'datepicker',
				'value' => $ar->ctr_date2,

            ),
            'options' => array(
                'label' => 'Date',
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_date',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'ctr_date',
				'value' => $ar->ctr_date
            ),
            'options' => array(
                
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_type',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_type',
            ),
            'options' => array(
                'label' => 'Type',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_marketer',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_marketer',
				'value' => $ar->ctr_marketer
            ),
            'options' => array(
                'label' => 'Marketer',
                'value_options' => $marketers,
            ),
        ));
		$this->add(array(
            'name' => 'ctr_amount',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_amount',
            ),
            'options' => array(
                'label' => 'Amount',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_monthly',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_monthly',
            ),
            'options' => array(
                'label' => 'Monthly Installment',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_location_c',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'ctr_location_c',
            ),
            'options' => array(
                'label' => 'Location City',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_location_s',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'ctr_location_s',
				'value' => $ar->ctr_location_s
            ),
            'options' => array(
                'label' => 'State',
                'value_options' => $states->getSelectOptions(),
            ),			
        ));
		$this->add(array(
            'name' => 'retaindatepicker',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'retaindatepicker',
				'value' => $ar->ctr_retain_date,
            ),
            'options' => array(
                'label' => 'Retain Date',
            ),
        ));
		
		$this->add(array(
            'name' => 'ctr_retain_date',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'ctr_retain_date',
				'value' => $ar->ctr_retain_date2
            ),
            'options' => array(
                
            ),
        ));
		$this->add(array(
            'name' => 'ctr_shortnote',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'ctr_shortnote',
				'value' => $ar->ctr_shortnote,
            ),
            'options' => array(
                'label' => 'Notice',
            ),
        ));
		$this->add(array(
            'name' => 'ctr_nomktg',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'ctr_nomktg',
            ),
            'options' => array(
                'label' => 'Mktg Write-Off:',
				'checked_value' => '1',
                'unchecked_value' => '0'
            ),
        ));
		$this->add(array(
            'name' => 'ctr_wkupdate',
			'type'  => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'type'  => 'checkbox',
                'id' => 'ctr_wkupdate',
            ),
            'options' => array(
                'label' => 'Do Weekly Updates',
				'checked_value' => '1',
                'unchecked_value' => '0'
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
        //if( !$inputFilter )
            $inputFilter = new InputFilter();
        $factory     = new InputFactory();
        // attach all inputs
        if($part === 'add') {
            
        }
        else {
            /*$inputFilter->add($factory->createInput(array(
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
            )));*/
        }
		
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_id',
            'required' => true,
            'filters'  => array(
                array('name' => 'Digits'),
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
            'name' => 'ctr_no',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 25,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'cli_id',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
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
            'name' => 'ctr_status',
            'required' => true,
            'filters'  => array(
                array('name' => 'Digits'),
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
            'name' => 'ctr_date',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 20,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_cli_bill',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
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
            'name' => 'ctr_spec',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_mid',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 15,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_rec',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_manager',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_type',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'ctr_marketer',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_amount',
            'required' => false,            
            'validators' => array(
                array(
                    'name'    => 'Float',
                    'options' => array(
                        'max' => 10,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_monthly',
            'required' => false,            
            'validators' => array(
                array(
                    'name'    => 'Float',
                    'options' => array(
                        'max' => 10,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_location_c',
            'required' => true,
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
            'name' => 'ctr_location_s',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_retain_date',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 20,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_nomktg',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_shortnote',
            'required' => false,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            /*'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 20,
                    ),
                ),
            ),*/
        )));
		$inputFilter->add($factory->createInput(array(
            'name' => 'ctr_wkupdate',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'max' => 4,
                    ),
                ),
            ),
        )));
        

        $this->setInputFilter($inputFilter);
		$this->setValidationGroup('ctr_nurse','ctr_id','ctr_no','cli_id','ctr_status','ctr_date','ctr_cli_bill','ctr_spec','ctr_mid','ctr_rec','ctr_manager','ctr_type','ctr_marketer','ctr_amount','ctr_monthly','ctr_location_c','ctr_location_s','ctr_retain_date','ctr_nomktg','ctr_wkupdate','ctr_shortnote');
    }
    

}
