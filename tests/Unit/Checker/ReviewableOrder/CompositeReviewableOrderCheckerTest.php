<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\ReviewableOrder;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\CompositeReviewableOrderChecker;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheck;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CompositeReviewableOrderCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_reviewable_when_no_checkers_are_registered(): void
    {
        $composite = new CompositeReviewableOrderChecker();
        $order = $this->prophesize(OrderInterface::class);

        $check = $composite->check($order->reveal());

        self::assertTrue($check->reviewable);
    }

    /**
     * @test
     */
    public function it_returns_reviewable_when_all_checkers_pass(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $checker1 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker1->check($order->reveal())->willReturn(ReviewableOrderCheck::reviewable());

        $checker2 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker2->check($order->reveal())->willReturn(ReviewableOrderCheck::reviewable());

        $composite = new CompositeReviewableOrderChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($order->reveal());

        self::assertTrue($check->reviewable);
    }

    /**
     * @test
     */
    public function it_returns_not_reviewable_on_first_failing_checker_and_does_not_call_subsequent_checkers(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $checker1 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker1->check($order->reveal())->willReturn(ReviewableOrderCheck::notReviewable('first.reason'));

        $checker2 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker2->check($order->reveal())->shouldNotBeCalled();

        $composite = new CompositeReviewableOrderChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($order->reveal());

        self::assertFalse($check->reviewable);
        self::assertSame('first.reason', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_not_reviewable_from_second_checker_when_first_passes(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $checker1 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker1->check($order->reveal())->willReturn(ReviewableOrderCheck::reviewable());

        $checker2 = $this->prophesize(ReviewableOrderCheckerInterface::class);
        $checker2->check($order->reveal())->willReturn(ReviewableOrderCheck::notReviewable('second.reason'));

        $composite = new CompositeReviewableOrderChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($order->reveal());

        self::assertFalse($check->reviewable);
        self::assertSame('second.reason', $check->reason);
    }
}
