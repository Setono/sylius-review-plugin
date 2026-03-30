<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Model\ReviewRequest;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReviewRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_correct_defaults_after_construction(): void
    {
        $before = new \DateTimeImmutable();
        $reviewRequest = new ReviewRequest();
        $after = new \DateTimeImmutable();

        self::assertNull($reviewRequest->getId());
        self::assertSame(ReviewRequestInterface::STATE_PENDING, $reviewRequest->getState());
        self::assertSame(0, $reviewRequest->getEligibilityChecks());
        self::assertNull($reviewRequest->getIneligibilityReason());
        self::assertNull($reviewRequest->getProcessingError());
        self::assertNull($reviewRequest->getOrder());
        self::assertGreaterThanOrEqual($before, $reviewRequest->getCreatedAt());
        self::assertLessThanOrEqual($after, $reviewRequest->getCreatedAt());
        self::assertGreaterThanOrEqual($before, $reviewRequest->getNextEligibilityCheckAt());
        self::assertLessThanOrEqual($after, $reviewRequest->getNextEligibilityCheckAt());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_state(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->setState(ReviewRequestInterface::STATE_COMPLETED);
        self::assertSame(ReviewRequestInterface::STATE_COMPLETED, $reviewRequest->getState());

        $reviewRequest->setState(ReviewRequestInterface::STATE_CANCELLED);
        self::assertSame(ReviewRequestInterface::STATE_CANCELLED, $reviewRequest->getState());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_next_eligibility_check_at(): void
    {
        $reviewRequest = new ReviewRequest();
        $date = new \DateTimeImmutable('2026-06-15 12:00:00');

        $reviewRequest->setNextEligibilityCheckAt($date);

        self::assertSame($date, $reviewRequest->getNextEligibilityCheckAt());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_eligibility_checks(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->setEligibilityChecks(3);

        self::assertSame(3, $reviewRequest->getEligibilityChecks());
    }

    /**
     * @test
     */
    public function it_throws_when_setting_negative_eligibility_checks(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $reviewRequest = new ReviewRequest();
        $reviewRequest->setEligibilityChecks(-1);
    }

    /**
     * @test
     */
    public function it_increments_eligibility_checks_by_one_by_default(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->incrementEligibilityChecks();

        self::assertSame(1, $reviewRequest->getEligibilityChecks());

        $reviewRequest->incrementEligibilityChecks();

        self::assertSame(2, $reviewRequest->getEligibilityChecks());
    }

    /**
     * @test
     */
    public function it_increments_eligibility_checks_by_given_amount(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->incrementEligibilityChecks(5);

        self::assertSame(5, $reviewRequest->getEligibilityChecks());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_ineligibility_reason(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->setIneligibilityReason('Order not fulfilled');

        self::assertSame('Order not fulfilled', $reviewRequest->getIneligibilityReason());

        $reviewRequest->setIneligibilityReason(null);

        self::assertNull($reviewRequest->getIneligibilityReason());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_processing_error(): void
    {
        $reviewRequest = new ReviewRequest();

        $reviewRequest->setProcessingError('Email delivery failed');

        self::assertSame('Email delivery failed', $reviewRequest->getProcessingError());

        $reviewRequest->setProcessingError(null);

        self::assertNull($reviewRequest->getProcessingError());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_order(): void
    {
        $reviewRequest = new ReviewRequest();
        $order = $this->createMock(OrderInterface::class);

        $reviewRequest->setOrder($order);

        self::assertSame($order, $reviewRequest->getOrder());

        $reviewRequest->setOrder(null);

        self::assertNull($reviewRequest->getOrder());
    }
}
