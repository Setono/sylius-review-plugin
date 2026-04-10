<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\EventSubscriber\AdminMenuSubscriber;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_subscribes_to_the_admin_main_menu_event(): void
    {
        self::assertArrayHasKey('sylius.menu.admin.main', AdminMenuSubscriber::getSubscribedEvents());
    }

    /** @test */
    public function it_adds_store_reviews_menu_item_under_marketing(): void
    {
        $storeReviewsItem = $this->prophesize(ItemInterface::class);
        $storeReviewsItem->setLabel('setono_sylius_review.ui.store_reviews')->willReturn($storeReviewsItem->reveal());
        $storeReviewsItem->setLabelAttribute('icon', 'newspaper')->shouldBeCalledOnce();

        $marketing = $this->prophesize(ItemInterface::class);
        $marketing->addChild('store_reviews', [
            'route' => 'setono_sylius_review_admin_store_review_index',
            'extras' => ['routes' => [
                ['route' => 'setono_sylius_review_admin_store_review_update'],
            ]],
        ])->willReturn($storeReviewsItem->reveal());

        $menu = $this->prophesize(ItemInterface::class);
        $menu->getChild('marketing')->willReturn($marketing->reveal());

        $event = $this->prophesize(MenuBuilderEvent::class);
        $event->getMenu()->willReturn($menu->reveal());

        $subscriber = new AdminMenuSubscriber();
        $subscriber->addStoreReviewsMenuItem($event->reveal());
    }

    /** @test */
    public function it_does_nothing_when_marketing_menu_is_missing(): void
    {
        $menu = $this->prophesize(ItemInterface::class);
        $menu->getChild('marketing')->willReturn(null);
        $menu->addChild()->shouldNotBeCalled();

        $event = $this->prophesize(MenuBuilderEvent::class);
        $event->getMenu()->willReturn($menu->reveal());

        $subscriber = new AdminMenuSubscriber();
        $subscriber->addStoreReviewsMenuItem($event->reveal());
    }
}
