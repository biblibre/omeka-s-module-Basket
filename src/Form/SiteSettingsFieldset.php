<?php
namespace Basket\Form;

use Zend\Form\Element;
use Zend\Form\Fieldset;

class SiteSettingsFieldset extends Fieldset
{
    /**
     * @var string
     */
    protected $label = 'Basket module'; // @translate

    public function init()
    {
        $this
            ->add([
                'name' => 'basket_visitor_allow',
                'type' => Element\Checkbox::class,
                'options' => [
                    'label' => 'Enable session basket for visitors', // @translate
                    'info' => 'The selection is automatically saved for logged users.', // @translate
                ],
                'attributes' => [
                    'id' => 'basket_visitor_allow',
                ],
            ])
        ;
    }
}
