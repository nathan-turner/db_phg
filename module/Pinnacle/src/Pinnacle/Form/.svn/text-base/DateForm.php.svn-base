<?php
// module/Pinnacle/src/Pinnacle/Form/DateForm.php:
namespace Pinnacle\Form;

use Zend\Form\Form;
use Zend\Form\Element\Date as DateElement;

class OptionalDate extends DateElement
{
    public function getInputSpecification() {
        $ar = parent::getInputSpecification();
        $ar['required'] = false;
        return $ar;
    }
}

class DateForm extends Form
{
    protected $defaultOpt = array('label1'=>'From: ','label2'=>'To: ','submit'=>'Select');
    protected $defaultAttr = array('type'=>'date','id'=>'my-date1');
    protected $defaultButton = 'btn btn-success';
    
    /**
     * @param   int $onetwo     1 or 2 date fields.
     * @param array $options    'label1'=>'From', 'label2'=>'To', 'submit'=>'Select',
     *                          'min'=> min_date, 'max'=> max_date,
     *                          'inline'=> true for inline or false for block form,
     *                          'class'=>input_css_class, 'style'=>input_css_style,
     *                          'required'=>true/false,
     *                          'button'=>button_css_class
    */
    public function __construct($onetwo = 1,array $options = null) {
        parent::__construct('dateform');
        
        $this->setAttribute('method', 'post');
        if( !is_array($options) ) $options = $this->defaultOpt;
        if( $options['inline'] ) $this->setAttribute('class', 'form-inline');
        
        $attr = $this->defaultAttr; $butt = $this->defaultButton;
        
        if( $options['min'] ) $attr['min'] = $options['min'];
        if( $options['max'] ) $attr['max'] = $options['max'];
        if( $options['class'] ) $attr['class'] = $options['class'];
        if( $options['style'] ) $attr['style'] = $options['style'];
        if( $options['button'] ) $butt = $options['button'];
        if( $options['required'] ) $attr['required'] = 'required';
        if( ! $options['label1'] ) $options['label1'] = $this->defaultOpt['label1'];
        if( ! $options['label2'] ) $options['label2'] = $this->defaultOpt['label2'];
        if( ! $options['submit'] ) $options['submit'] = $this->defaultOpt['submit'];
        
        $this->add(array(
            'name' => 'date1',
            'type' => ($options['required']?
                       'Zend\Form\Element\Date':'Pinnacle\Form\OptionalDate'),
            'attributes' => $attr,
            'options' => array(
                'label' => $options['label1'],
            ),
        ));

        $attr['id'] = 'my-date2';
        if( $onetwo > 1 ) $this->add(array(
            'name' => 'date2',
            'type' => ($options['required']?
                       'Zend\Form\Element\Date':'Pinnacle\Form\OptionalDate'),
            'attributes' => $attr,
            'options' => array(
                'label' => $options['label2'],
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => $options['submit'],
                'id' => 'submitbutton',
                'class' => $butt,
            ),
        ));

    }

}
