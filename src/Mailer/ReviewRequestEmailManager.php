<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Mailer;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class ReviewRequestEmailManager implements ReviewRequestEmailManagerInterface
{
    public function __construct(private readonly SenderInterface $emailSender)
    {
    }

    public function sendReviewRequest(ReviewRequestInterface $reviewRequest): void
    {
        $order = $reviewRequest->getOrder();
        Assert::notNull($order, 'The order on the review request cannot be null');

        $email = $order->getCustomer()?->getEmail();
        Assert::notNull($email, 'The email on the order cannot be null');

        /** @psalm-suppress DeprecatedMethod */
        $this->emailSender->send(
            Emails::REVIEW_REQUEST,
            [$email],
            [
                'reviewRequest' => $reviewRequest,
                'channel' => $order->getChannel(),
                'localeCode' => $order->getLocaleCode(),
            ],
        );
    }
}
