<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Mailer;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Webmozart\Assert\Assert;

final class StoreReplyNotificationEmailManager implements StoreReplyNotificationEmailManagerInterface
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private readonly SenderInterface $emailSender,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function sendNotification(ReviewInterface $review): void
    {
        $author = $review->getAuthor();
        $email = $author?->getEmail();
        if (null === $email) {
            return;
        }

        $isStoreReview = $review instanceof StoreReviewInterface;
        $reviewSubject = $review->getReviewSubject();

        if ($review instanceof StoreReviewInterface) {
            $order = $review->getOrder();
            Assert::notNull($order, 'The order on the store review cannot be null');

            $channel = $reviewSubject;
            $localeCode = $order->getLocaleCode();
        } else {
            Assert::isInstanceOf($author, CustomerInterface::class, 'The author must be a customer');

            $latestOrder = $this->orderRepository->findLatestByCustomer($author);
            Assert::notNull($latestOrder, 'The customer must have at least one order');

            $channel = $latestOrder->getChannel();
            $localeCode = $latestOrder->getLocaleCode();
        }

        Assert::notNull($localeCode, 'The locale code on the order cannot be null');

        $this->emailSender->send(
            Emails::STORE_REPLY_NOTIFICATION,
            [$email],
            [
                'review' => $review,
                'isStoreReview' => $isStoreReview,
                'isProductReview' => !$isStoreReview,
                'reviewSubject' => $reviewSubject,
                'reviewSubjectName' => $reviewSubject?->getName(),
                'channel' => $channel,
                'localeCode' => $localeCode,
            ],
        );
    }
}
