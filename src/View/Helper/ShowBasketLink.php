<?php

namespace Basket\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ShowBasketLink extends AbstractHelper
{
    /**
     * The default partial view script.
     */
    const PARTIAL_NAME = 'common/basket-link';

    /**
     * Get the link to the user basket.
     *
     * @param array $options Options for the partial.
     * @return string
     */
    public function __invoke(array $options = [])
    {
        $view = $this->getView();
        $template = isset($options['template']) ? $options['template'] : self::PARTIAL_NAME;
        unset($options['template']);
        return $view->partial($template, $options);
    }
}
