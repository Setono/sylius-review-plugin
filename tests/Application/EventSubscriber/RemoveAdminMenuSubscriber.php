<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Application\EventSubscriber;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RemoveAdminMenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.menu.admin.main' => ['removeMenuItems', -100],
        ];
    }

    public function removeMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->removeChild('official_support');
        $menu->removeChild('sylius.ui.administration');
    }
}
