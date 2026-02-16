<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\EligibilityChecker\OrderFulfilledReviewRequestEligibilityChecker;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderFulfilledReviewRequestEligibilityCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_eligible_when_order_is_fulfilled(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn($order->reveal());

        $checker = new OrderFulfilledReviewRequestEligibilityChecker();
        $check = $checker->check($reviewRequest->reveal());

        self::assertTrue($check->eligible);
    }

    /**
     * @test
     */
    public function it_returns_ineligible_when_order_is_not_fulfilled(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn($order->reveal());

        $checker = new OrderFulfilledReviewRequestEligibilityChecker();
        $check = $checker->check($reviewRequest->reveal());

        self::assertFalse($check->eligible);
        self::assertSame('Order is not fulfilled', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_ineligible_when_order_is_null(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn(null);

        $checker = new OrderFulfilledReviewRequestEligibilityChecker();
        $check = $checker->check($reviewRequest->reveal());

        self::assertFalse($check->eligible);
        self::assertSame('Order is not fulfilled', $check->reason);
    }
}
