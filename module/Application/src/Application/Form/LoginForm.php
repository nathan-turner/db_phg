<?php
// module/Application/src/Application/Form/LoginForm.php:
namespace Application\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
       // we want to ignore the name passed
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        //
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
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
                'required' => 'required',
                'id' => 'my-password',
            ),
            'options' => array(
                'label' => 'Password',
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
                'value' => 'Log in',
                'id' => 'submitbutton',
                'class' => 'btn btn-success btn-large',
            ),
        ));

    }
    

}
