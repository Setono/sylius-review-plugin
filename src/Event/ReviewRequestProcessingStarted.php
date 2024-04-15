<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Event;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

final class ReviewRequestProcessingStarted
{
    public function __construct(public readonly ReviewRequestInterface $reviewRequest)
    {
    }
}
