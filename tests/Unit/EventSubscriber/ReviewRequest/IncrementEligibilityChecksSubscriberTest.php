<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber\ReviewRequest;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest\IncrementEligibilityChecksSubscriber;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

final class IncrementEligibilityChecksSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_increments_eligibility_checks_on_review_request(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->incrementEligibilityChecks()->shouldBeCalledOnce();

        $subscriber = new IncrementEligibilityChecksSubscriber();
        $subscriber->incrementEligibilityChecks(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_subscribes_to_review_request_processing_started_with_priority_300(): void
    {
        $events = IncrementEligibilityChecksSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(ReviewRequestProcessingStarted::class, $events);
        self::assertSame(['incrementEligibilityChecks', 300], $events[ReviewRequestProcessingStarted::class]);
    }
}
