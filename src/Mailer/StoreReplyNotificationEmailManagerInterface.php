<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Mailer;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;

interface StoreReplyNotificationEmailManagerInterface
{
    public function sendNotification(ReviewInterface $review): void;
}
