<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Mailer;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Mailer\ReviewRequestEmailManager;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ReviewRequestEmailManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sends_review_request_email_with_correct_arguments(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('john@example.com');

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer->reveal());
        $order->getChannel()->willReturn($channel->reveal());
        $order->getLocaleCode()->willReturn('en_US');

        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn($order->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);
        $emailSender->send(
            Emails::REVIEW_REQUEST,
            ['john@example.com'],
            [
                'reviewRequest' => $reviewRequest->reveal(),
                'channel' => $channel->reveal(),
                'localeCode' => 'en_US',
            ],
        )->shouldBeCalledOnce();

        $manager = new ReviewRequestEmailManager($emailSender->reveal());
        $manager->sendReviewRequest($reviewRequest->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_order_is_null(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn(null);

        $emailSender = $this->prophesize(SenderInterface::class);

        $manager = new ReviewRequestEmailManager($emailSender->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $manager->sendReviewRequest($reviewRequest->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_customer_is_null(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn(null);

        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn($order->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);

        $manager = new ReviewRequestEmailManager($emailSender->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $manager->sendReviewRequest($reviewRequest->reveal());
    }

    /**
     * @test
     */
    public function it_throws_exception_when_customer_email_is_null(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn(null);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer->reveal());

        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);
        $reviewRequest->getOrder()->willReturn($order->reveal());

        $emailSender = $this->prophesize(SenderInterface::class);

        $manager = new ReviewRequestEmailManager($emailSender->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $manager->sendReviewRequest($reviewRequest->reveal());
    }
}
