<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest;

use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IncrementEligibilityChecksSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ReviewRequestProcessingStarted::class => ['incrementEligibilityChecks', 100],
        ];
    }

    public function incrementEligibilityChecks(ReviewRequestProcessingStarted $event): void
    {
        $event->reviewRequest->incrementEligibilityChecks();
    }
}
