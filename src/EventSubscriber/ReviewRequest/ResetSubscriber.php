<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\ReviewRequest;

use Setono\SyliusReviewPlugin\Event\ReviewRequestProcessingStarted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ResetSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ReviewRequestProcessingStarted::class => ['reset', 400],
        ];
    }

    public function reset(ReviewRequestProcessingStarted $event): void
    {
        $event->reviewRequest->setIneligibilityReason(null);
        $event->reviewRequest->setProcessingError(null);
    }
}
