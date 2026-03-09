<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Mailer;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Mailer\StoreReplyNotificationEmailManager;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Review\Model\ReviewerInterface;

final class StoreReplyNotificationEmailManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sends_notification_for_store_review(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getName()->willReturn('Fashion Web Store');

        $author = $this->prophesize(ReviewerInterface::class);
        $author->getEmail()->willReturn('john@example.com');

        $order = $this->prophesize(OrderInterface::class);
        $order->getLocaleCode()->willReturn('en_US');

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getAuthor()->willReturn($author->reveal());
        $review->getReviewSubject()->willReturn($channel->reveal());
        $review->getOrder()->willReturn($order->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);
        $emailSender->send(
            Emails::STORE_REPLY_NOTIFICATION,
            ['john@example.com'],
            [
                'review' => $review->reveal(),
                'isStoreReview' => true,
                'isProductReview' => false,
                'reviewSubject' => $channel->reveal(),
                'reviewSubjectName' => 'Fashion Web Store',
                'channel' => $channel->reveal(),
                'localeCode' => 'en_US',
            ],
        )->shouldBeCalledOnce();

        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());
        $manager->sendNotification($review->reveal());
    }

    /**
     * @test
     */
    public function it_sends_notification_for_product_review(): void
    {
        $productReviewAuthor = $this->prophesize(CustomerInterface::class);
        $productReviewAuthor->getEmail()->willReturn('jane@example.com');

        $reviewSubject = $this->prophesize(\Sylius\Component\Review\Model\ReviewableInterface::class);
        $reviewSubject->getName()->willReturn('Cool T-Shirt');

        $channel = $this->prophesize(\Sylius\Component\Core\Model\ChannelInterface::class);

        $latestOrder = $this->prophesize(OrderInterface::class);
        $latestOrder->getChannel()->willReturn($channel->reveal());
        $latestOrder->getLocaleCode()->willReturn('de_DE');

        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn($productReviewAuthor->reveal());
        $review->getReviewSubject()->willReturn($reviewSubject->reveal());

        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $orderRepository->findLatestByCustomer($productReviewAuthor->reveal())->willReturn($latestOrder->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);
        $emailSender->send(
            Emails::STORE_REPLY_NOTIFICATION,
            ['jane@example.com'],
            [
                'review' => $review->reveal(),
                'isStoreReview' => false,
                'isProductReview' => true,
                'reviewSubject' => $reviewSubject->reveal(),
                'reviewSubjectName' => 'Cool T-Shirt',
                'channel' => $channel->reveal(),
                'localeCode' => 'de_DE',
            ],
        )->shouldBeCalledOnce();

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());
        $manager->sendNotification($review->reveal());
    }

    /**
     * @test
     */
    public function it_skips_sending_when_author_has_no_email(): void
    {
        $author = $this->prophesize(ReviewerInterface::class);
        $author->getEmail()->willReturn(null);

        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn($author->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);
        $emailSender->send()->shouldNotBeCalled();

        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());
        $manager->sendNotification($review->reveal());
    }

    /**
     * @test
     */
    public function it_skips_sending_when_author_is_null(): void
    {
        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn(null);

        $emailSender = $this->prophesize(SenderInterface::class);
        $emailSender->send()->shouldNotBeCalled();

        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());
        $manager->sendNotification($review->reveal());
    }

    /**
     * @test
     */
    public function it_throws_when_store_review_has_no_order(): void
    {
        $author = $this->prophesize(ReviewerInterface::class);
        $author->getEmail()->willReturn('john@example.com');

        $review = $this->prophesize(StoreReviewInterface::class);
        $review->getAuthor()->willReturn($author->reveal());
        $review->getReviewSubject()->willReturn(null);
        $review->getOrder()->willReturn(null);

        $emailSender = $this->prophesize(SenderInterface::class);
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $manager->sendNotification($review->reveal());
    }

    /**
     * @test
     */
    public function it_throws_when_product_review_customer_has_no_orders(): void
    {
        $author = $this->prophesize(CustomerInterface::class);
        $author->getEmail()->willReturn('jane@example.com');

        $review = $this->prophesize(ReviewInterface::class);
        $review->getAuthor()->willReturn($author->reveal());
        $review->getReviewSubject()->willReturn(null);

        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $orderRepository->findLatestByCustomer($author->reveal())->willReturn(null);

        $emailSender = $this->prophesize(SenderInterface::class);

        $manager = new StoreReplyNotificationEmailManager($emailSender->reveal(), $orderRepository->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $manager->sendNotification($review->reveal());
    }
}
