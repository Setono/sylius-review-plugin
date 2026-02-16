<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber\ReviewRequest;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest\UpdateNextEligibilityCheckSubscriber;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

final class UpdateNextEligibilityCheckSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sets_next_eligibility_check_to_24_hours_for_first_check(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(1);

        $expected = new \DateTimeImmutable('+24 hours');

        $reviewRequest->setNextEligibilityCheckAt(Argument::that(
            fn (\DateTimeInterface $date): bool => abs($date->getTimestamp() - $expected->getTimestamp()) < 5,
        ))->shouldBeCalledOnce();

        $subscriber = new UpdateNextEligibilityCheckSubscriber();
        $subscriber->updateNextEligibilityCheck(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_sets_next_eligibility_check_to_48_hours_for_second_check(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(2);

        $expected = new \DateTimeImmutable('+48 hours');

        $reviewRequest->setNextEligibilityCheckAt(Argument::that(
            fn (\DateTimeInterface $date): bool => abs($date->getTimestamp() - $expected->getTimestamp()) < 5,
        ))->shouldBeCalledOnce();

        $subscriber = new UpdateNextEligibilityCheckSubscriber();
        $subscriber->updateNextEligibilityCheck(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_uses_custom_initial_delay_hours(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(1);

        $expected = new \DateTimeImmutable('+12 hours');

        $reviewRequest->setNextEligibilityCheckAt(Argument::that(
            fn (\DateTimeInterface $date): bool => abs($date->getTimestamp() - $expected->getTimestamp()) < 5,
        ))->shouldBeCalledOnce();

        $subscriber = new UpdateNextEligibilityCheckSubscriber(12);
        $subscriber->updateNextEligibilityCheck(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_subscribes_to_review_request_processing_started_with_priority_100(): void
    {
        $events = UpdateNextEligibilityCheckSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(ReviewRequestProcessingStarted::class, $events);
        self::assertSame(['updateNextEligibilityCheck', 100], $events[ReviewRequestProcessingStarted::class]);
    }
}
