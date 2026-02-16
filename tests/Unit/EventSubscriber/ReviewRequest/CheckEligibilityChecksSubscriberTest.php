<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber\ReviewRequest;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest\CheckEligibilityChecksSubscriber;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

final class CheckEligibilityChecksSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_cancels_review_request_when_eligibility_checks_exceed_maximum(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(6);
        $reviewRequest->setProcessingError('Maximum number of eligibility checks reached')->shouldBeCalledOnce();

        $workflow = $this->prophesize(WorkflowInterface::class);
        $workflow->apply($reviewRequest->reveal(), ReviewRequestWorkflow::TRANSITION_CANCEL)->shouldBeCalledOnce();

        $subscriber = new CheckEligibilityChecksSubscriber($workflow->reveal(), 5);
        $subscriber->check(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_does_not_cancel_when_eligibility_checks_equal_maximum(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(5);
        $reviewRequest->setProcessingError()->shouldNotBeCalled();

        $workflow = $this->prophesize(WorkflowInterface::class);
        $workflow->apply()->shouldNotBeCalled();

        $subscriber = new CheckEligibilityChecksSubscriber($workflow->reveal(), 5);
        $subscriber->check(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_does_not_cancel_when_eligibility_checks_are_below_maximum(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getEligibilityChecks()->willReturn(2);
        $reviewRequest->setProcessingError()->shouldNotBeCalled();

        $workflow = $this->prophesize(WorkflowInterface::class);
        $workflow->apply()->shouldNotBeCalled();

        $subscriber = new CheckEligibilityChecksSubscriber($workflow->reveal(), 5);
        $subscriber->check(new ReviewRequestProcessingStarted($reviewRequest->reveal()));
    }

    /**
     * @test
     */
    public function it_subscribes_to_review_request_processing_started_with_priority_200(): void
    {
        $events = CheckEligibilityChecksSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(ReviewRequestProcessingStarted::class, $events);
        self::assertSame(['check', 200], $events[ReviewRequestProcessingStarted::class]);
    }
}
