<?php
// module/Pinnacle/src/Pinnacle/Form/ViewForm.php:
namespace Pinnacle\Form;

use Pinnacle\Model\Utility;
use Zend\Form\Form;

class ViewForm extends Form
{
    public function __construct() {
        parent::__construct('viewform');
        
		$this->add(array(
            'name' => 'cli_city',
            'attributes' => array(
                'type'  => 'text',
                'id' => 'my-city',
				//'value' => 'Atlanta',
            ),
            'options' => array(
                'label' => 'City: ',
				
            ),
        ));
        

    }

}
