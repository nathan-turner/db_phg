<?php
// module/Pinnacle/src/Pinnacle/Form/MailDescForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;

class MailDescForm extends Form
{
    public function __construct() {
        parent::__construct('maildesc');
        $this->setAttribute('class', 'form-inline');
        
        $this->add(array(
            'name' => 'uid',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'list_id',
            'attributes' => array(
                'type'  => 'hidden',
                'id' => 'my-list',
            ),
        ));
        $this->add(array(
            'name' => 'shared',
            'attributes' => array(
                'type'  => 'hidden',
                'value' => '0',
            ),
        ));
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-name',
            ),
            'options' => array(
                'label' => 'Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'id' => 'my-desc',
                'class' => 'span5',
            ),
            'options' => array(
                'label' => 'Description: ',
            ),
        ));

        $this->add(array(
            'name' => 'editbutton',
            'type' => 'Zend\Form\Element\Button',
            'attributes' => array(
                'value' => 'edit',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
            'options' => array(
                'label' => 'Save',
            ),
        ));

    }

}
