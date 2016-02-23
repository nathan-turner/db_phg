<?php
// module/Pinnacle/src/Pinnacle/Form/Report/MarketlogForm.php:
namespace Pinnacle\Form\Report;

use Pinnacle\Model\UsersTable;
use Zend\Form\Form;

class MarketlogForm extends Form
{
    public function __construct(UsersTable $uTable) {
        parent::__construct('marketlog');
        $this->setAttribute('class', 'form-inline');
        
        $users = array('0'=>' All') + $uTable->getSelectOptions('M');
        
        $this->add(array(
            'name' => 'ord',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'emp_id',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type'  => 'select',
                'id' => 'my-emplist',
            ),
            'options' => array(
                'label' => 'Marketer: ',
                'value_options' => $users,
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
