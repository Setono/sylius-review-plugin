<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\ReviewableOrder;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\StoreReviewEditableReviewableOrderChecker;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class StoreReviewEditableReviewableOrderCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_reviewable_when_no_existing_review_exists(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $repository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $repository->findOneByOrder($order->reveal())->willReturn(null);

        $checker = new StoreReviewEditableReviewableOrderChecker($repository->reveal());

        $check = $checker->check($order->reveal());

        self::assertTrue($check->reviewable);
    }

    /**
     * @test
     */
    public function it_returns_reviewable_when_existing_review_is_within_editable_period(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getCreatedAt()->willReturn(new \DateTimeImmutable('-1 hour'));

        $repository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $repository->findOneByOrder($order->reveal())->willReturn($review->reveal());

        $checker = new StoreReviewEditableReviewableOrderChecker($repository->reveal(), '+24 hours');

        $check = $checker->check($order->reveal());

        self::assertTrue($check->reviewable);
    }

    /**
     * @test
     */
    public function it_returns_not_reviewable_when_existing_review_is_past_editable_period(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getCreatedAt()->willReturn(new \DateTimeImmutable('-48 hours'));

        $repository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $repository->findOneByOrder($order->reveal())->willReturn($review->reveal());

        $checker = new StoreReviewEditableReviewableOrderChecker($repository->reveal(), '+24 hours');

        $check = $checker->check($order->reveal());

        self::assertFalse($check->reviewable);
        self::assertSame('setono_sylius_review.ui.review_period_expired', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_not_reviewable_when_editing_is_disabled(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $review = $this->prophesize(StoreReviewInterface::class);

        $repository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $repository->findOneByOrder($order->reveal())->willReturn($review->reveal());

        $checker = new StoreReviewEditableReviewableOrderChecker($repository->reveal(), null);

        $check = $checker->check($order->reveal());

        self::assertFalse($check->reviewable);
        self::assertSame('setono_sylius_review.ui.review_already_submitted', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_reviewable_when_existing_review_has_null_created_at(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getCreatedAt()->willReturn(null);

        $repository = $this->prophesize(StoreReviewRepositoryInterface::class);
        $repository->findOneByOrder($order->reveal())->willReturn($review->reveal());

        $checker = new StoreReviewEditableReviewableOrderChecker($repository->reveal(), '+24 hours');

        $check = $checker->check($order->reveal());

        self::assertTrue($check->reviewable);
    }
}
