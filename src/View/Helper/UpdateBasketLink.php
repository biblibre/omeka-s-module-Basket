<?php

namespace Basket\View\Helper;

use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Zend\View\Helper\AbstractHelper;

class UpdateBasketLink extends AbstractHelper
{
    /**
     * The default partial view script.
     */
    const PARTIAL_NAME = 'common/basket-button';

    /**
     * Create a button to add or remove a resource to/from the basket.
     *
     * @param AbstractResourceEntityRepresentation $resource
     * @param array $options Options for the partial. Managed key:
     * - action: "add" or "delete". If not specified, the action is "toggle".
     * @return string
     */
    public function __invoke(AbstractResourceEntityRepresentation $resource, array $options = [])
    {
        $view = $this->getView();
        $siteSetting = $view->plugin('siteSetting');

        $user = $view->identity();
        $allowVisitor = $siteSetting('basket_visitor_allow', true);
        if (!$allowVisitor && !$user) {
            return '';
        }

        $container = new \Zend\Session\Container('Basket');
        $options['basketItem'] = isset($container->records[$resource->id()]);

        $defaultOptions = [
            'template' => self::PARTIAL_NAME,
            'action' => 'toggle',
        ];
        $options += $defaultOptions;

        $template = $options['template'];
        unset($options['template']);

        $params = [
            'resource' => $resource,
            'url' => $view->url('site/basket-id', ['action' => $options['action'], 'id' => $resource->id()], true),
        ];

        return $view->partial($template, $params + $options);
    }
}
