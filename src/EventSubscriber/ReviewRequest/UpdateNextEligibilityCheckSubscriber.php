<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest;

use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UpdateNextEligibilityCheckSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly int $initialDelayHours = 24)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReviewRequestProcessingStarted::class => ['updateNextEligibilityCheck', 100],
        ];
    }

    public function updateNextEligibilityCheck(ReviewRequestProcessingStarted $event): void
    {
        $nextEligibilityCheck = new \DateTimeImmutable(sprintf(
            '+%d hours',
            $this->initialDelayHours * 2 ** ($event->reviewRequest->getEligibilityChecks() - 1),
        ));

        $event->reviewRequest->setNextEligibilityCheckAt($nextEligibilityCheck);
    }
}
