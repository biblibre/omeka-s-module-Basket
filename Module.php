<?php
namespace Basket;

if (!class_exists(\Generic\AbstractModule::class)) {
    require file_exists(dirname(__DIR__) . '/Generic/AbstractModule.php')
        ? dirname(__DIR__) . '/Generic/AbstractModule.php'
        : __DIR__ . '/src/Generic/AbstractModule.php';
}

use Generic\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

/**
 * Basket.
 *
 * @copyright Biblibre, 2016
 * @copyright Daniel Berthereau 2019-2020
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */
class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;

    // Guest is an optional dependency, not a required one.
    // protected $dependency = 'Guest';

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $services = $this->getServiceLocator();
        $acl = $services->get('Omeka\Acl');

        // Since Omeka 1.4, modules are ordered, so Guest come after Basket.
        // See \Guest\Module::onBootstrap().
        if (!$acl->hasRole('guest')) {
            $acl->addRole('guest');
        }

        $roles = $acl->getRoles();

        $acl
            ->allow(
                $roles,
                [
                    Entity\BasketItem::class,
                    Api\Adapter\BasketItemAdapter::class,
                    'Basket\Controller\Site\Basket',
                    'Basket\Controller\Site\GuestBoard',
                ]
            )
            // This right is checked in controller in order to avoid to check
            // the site here.
            ->allow(
                null,
                'Basket\Controller\Site\Basket'
            )
        ;
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $controllers = [
            'Omeka\Controller\Site\Item',
            'Omeka\Controller\Site\ItemSet',
            'Omeka\Controller\Site\Media',
        ];
        foreach ($controllers as $controller) {
            $sharedEventManager->attach(
                $controller,
                'view.show.after',
                [$this, 'handleViewShowAfter']
            );
        }

        // Guest integration.
        $sharedEventManager->attach(
            \Guest\Controller\Site\GuestController::class,
            'guest.widgets',
            [$this, 'handleGuestWidgets']
        );

        $sharedEventManager->attach(
            \Omeka\Form\SiteSettingsForm::class,
            'form.add_elements',
            [$this, 'handleSiteSettings']
        );
    }

    public function handleViewShowAfter(Event $event)
    {
        $view = $event->getTarget();
        $siteSetting = $view->getHelperPluginManager()->get('siteSetting');

        $user = $view->identity();
        $allowVisitor = $siteSetting('basket_visitor_allow', true);
        if (!$user && !$allowVisitor) {
            return;
        }

        $containerBasket = $this->getServiceLocator()->get('ControllerPluginManager')->get('containerBasket');
        $containerBasket();

        echo $view->partial('common/basket-item');
    }

    public function handleGuestWidgets(Event $event)
    {
        $widgets = $event->getParam('widgets');
        $helpers = $this->getServiceLocator()->get('ViewHelperManager');
        $translate = $helpers->get('translate');
        $partial = $helpers->get('partial');

        $widget = [];
        $widget['label'] = $translate('Basket'); // @translate
        $widget['content'] = $partial('guest/site/guest/widget/basket');
        $widgets['basket'] = $widget;

        $event->setParam('widgets', $widgets);
    }
}
