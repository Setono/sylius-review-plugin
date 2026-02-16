<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber\ReviewRequest;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest\ResetSubscriber;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

final class ResetSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_clears_ineligibility_reason_and_processing_error(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->setIneligibilityReason(null)->shouldBeCalledOnce();
        $reviewRequest->setProcessingError(null)->shouldBeCalledOnce();

        $subscriber = new ResetSubscriber();
        $subscriber->reset(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_subscribes_to_review_request_processing_started_with_priority_400(): void
    {
        $events = ResetSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(ReviewRequestProcessingStarted::class, $events);
        self::assertSame(['reset', 400], $events[ReviewRequestProcessingStarted::class]);
    }
}
