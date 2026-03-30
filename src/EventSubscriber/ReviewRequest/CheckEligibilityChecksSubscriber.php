<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest;

use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CheckEligibilityChecksSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly StateMachineInterface $stateMachine,
        private readonly int $maximumChecks,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReviewRequestProcessingStarted::class => ['check', 200],
        ];
    }

    public function check(ReviewRequestProcessingStarted $event): void
    {
        if ($event->reviewRequest->getEligibilityChecks() <= $this->maximumChecks) {
            return;
        }

        $this->stateMachine->apply($event->reviewRequest, ReviewRequestWorkflow::NAME, ReviewRequestWorkflow::TRANSITION_CANCEL);
        $event->reviewRequest->setProcessingError('Maximum number of eligibility checks reached');
    }
}
