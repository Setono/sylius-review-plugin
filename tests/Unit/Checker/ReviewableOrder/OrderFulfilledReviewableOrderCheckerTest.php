<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\ReviewableOrder;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\OrderFulfilledReviewableOrderChecker;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderFulfilledReviewableOrderCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_reviewable_when_order_is_fulfilled(): void
    {
        $checker = new OrderFulfilledReviewableOrderChecker();
        $order = $this->prophesize(OrderInterface::class);
        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $check = $checker->check($order->reveal());

        self::assertTrue($check->reviewable);
    }

    /**
     * @test
     */
    public function it_returns_not_reviewable_when_order_is_not_in_a_reviewable_state(): void
    {
        $checker = new OrderFulfilledReviewableOrderChecker();
        $order = $this->prophesize(OrderInterface::class);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $check = $checker->check($order->reveal());

        self::assertFalse($check->reviewable);
        self::assertSame('setono_sylius_review.ui.order_not_fulfilled', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_reviewable_when_order_matches_custom_reviewable_states(): void
    {
        $checker = new OrderFulfilledReviewableOrderChecker([OrderInterface::STATE_FULFILLED, 'completed']);
        $order = $this->prophesize(OrderInterface::class);
        $order->getState()->willReturn('completed');

        $check = $checker->check($order->reveal());

        self::assertTrue($check->reviewable);
    }
}
