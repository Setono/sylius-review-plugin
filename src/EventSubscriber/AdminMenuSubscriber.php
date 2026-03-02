<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminMenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.menu.admin.main' => 'addStoreReviewsMenuItem',
        ];
    }

    public function addStoreReviewsMenuItem(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $marketing = $menu->getChild('marketing');
        if (!$marketing instanceof ItemInterface) {
            return;
        }

        $marketing
            ->addChild('store_reviews', [
                'route' => 'setono_sylius_review_admin_store_review_index',
                'extras' => ['routes' => [
                    ['route' => 'setono_sylius_review_admin_store_review_update'],
                ]],
            ])
            ->setLabel('setono_sylius_review.ui.store_reviews')
            ->setLabelAttribute('icon', 'newspaper')
        ;
    }
}
