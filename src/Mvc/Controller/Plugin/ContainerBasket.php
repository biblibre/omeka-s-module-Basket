<?php

namespace Basket\Mvc\Controller\Plugin;

use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class ContainerBasket extends AbstractPlugin
{
    /**
     * Check and get the seleciton list in session container.
     *
     * @return Container
     */
    public function __invoke()
    {
        $controller = $this->getController();

        // Check if the container is ready for the current user.
        $container = new Container('Basket');
        if (empty($container->init)) {
            $container->user = sha1(microtime() . random_bytes(20));
            $container->records = [];
            $container->init = true;
        } elseif (!isset($container->records)) {
            $container->records = [];
        }

        // Sync with the user selected items.
        $user = $controller->identity();
        if ($user) {
            // TODO Add an option to limit size of basket.
            $container->records = [];
            /** @var \Basket\Api\Representation\BasketItemRepresentation[] $basketItems*/
            $basketItems = $controller->api()->search('basket_items', ['user_id' => $user->getId()])->getContent();
            foreach ($basketItems as $basketItem) {
                $resource = $basketItem->resource();
                $container->records[$resource->id()] = $this->basketItemForResource($resource, true);
            }
        }

        return $container;
    }

    /**
     * Format a resource for the container.
     *
     * Copy in \Basket\Controller\BasketController::basketItemForResource()
     *
     * @param AbstractResourceEntityRepresentation $resource
     * @param bool $isSelected
     * @return array
     */
    protected function basketItemForResource(AbstractResourceEntityRepresentation $resource, $isSelected)
    {
        static $siteSlug;
        static $url;
        if (is_null($siteSlug)) {
            $controller = $this->getController();
            $siteSlug = $controller->currentSite()->slug();
            $url = $controller->url();
        }
        return [
            'id' => $resource->id(),
            'type' => $resource->getControllerName(),
            'url' => $resource->siteUrl($siteSlug, true),
            'url_remove' => $url->fromRoute('site/basket-id', ['site-slug' => $siteSlug, 'action' => 'delete', 'id' => $resource->id()]),
            // String is required to avoid error in container when the title is
            // a resource.
            'title' => (string) $resource->displayTitle(),
            'value' => $isSelected ? 'selected' : 'unselected',
        ];
    }
}
