<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Processor;

use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;

final class ReviewRequestProcessor implements ReviewRequestProcessorInterface
{
    public function __construct(private readonly ReviewRequestRepositoryInterface $reviewRequestRepository)
    {
    }

    public function process(): void
    {
    }
}
