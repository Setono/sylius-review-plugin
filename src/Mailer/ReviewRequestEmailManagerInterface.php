<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Mailer;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

interface ReviewRequestEmailManagerInterface
{
    public function sendReviewRequest(ReviewRequestInterface $reviewRequest): void;
}
