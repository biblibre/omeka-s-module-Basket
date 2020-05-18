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
                ],
                'attributes' => [
                    'id' => 'basket_visitor_allow',
                ],
            ])
            ->add([
                'name' => 'basket_user_fill_main',
                'type' => Element\Checkbox::class,
                'options' => [
                    'label' => 'For authenticated users, fill the main basket directly', // @translate
                ],
                'attributes' => [
                    'id' => 'basket_user_fill_main',
                ],
            ])
        ;
    }
}
