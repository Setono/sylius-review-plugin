<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class StoreReviewTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_correct_defaults_after_construction(): void
    {
        $review = new StoreReview();

        self::assertNull($review->getId());
        self::assertNull($review->getRating());
        self::assertNull($review->getTitle());
        self::assertNull($review->getComment());
        self::assertSame(ReviewInterface::STATUS_NEW, $review->getStatus());
        self::assertNull($review->getOrder());
        self::assertNull($review->getAuthor());
        self::assertNull($review->getReviewSubject());
        self::assertNull($review->getChannel());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_title(): void
    {
        $review = new StoreReview();

        $review->setTitle('Great store');

        self::assertSame('Great store', $review->getTitle());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_rating(): void
    {
        $review = new StoreReview();

        $review->setRating(5);

        self::assertSame(5, $review->getRating());

        $review->setRating(null);

        self::assertNull($review->getRating());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_comment(): void
    {
        $review = new StoreReview();

        $review->setComment('Excellent service');

        self::assertSame('Excellent service', $review->getComment());

        $review->setComment(null);

        self::assertNull($review->getComment());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_author(): void
    {
        $review = new StoreReview();
        $author = $this->createMock(ReviewerInterface::class);

        $review->setAuthor($author);

        self::assertSame($author, $review->getAuthor());

        $review->setAuthor(null);

        self::assertNull($review->getAuthor());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_status(): void
    {
        $review = new StoreReview();

        $review->setStatus(ReviewInterface::STATUS_ACCEPTED);

        self::assertSame(ReviewInterface::STATUS_ACCEPTED, $review->getStatus());

        $review->setStatus(null);

        self::assertNull($review->getStatus());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_review_subject(): void
    {
        $review = new StoreReview();
        $channel = $this->createMock(ChannelInterface::class);

        $review->setReviewSubject($channel);

        self::assertSame($channel, $review->getReviewSubject());

        $review->setReviewSubject(null);

        self::assertNull($review->getReviewSubject());
    }

    /**
     * @test
     */
    public function it_throws_when_setting_review_subject_with_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $review = new StoreReview();
        $review->setReviewSubject($this->createMock(ReviewableInterface::class));
    }

    /**
     * @test
     */
    public function it_can_set_and_get_order(): void
    {
        $review = new StoreReview();
        $order = $this->createMock(OrderInterface::class);

        $review->setOrder($order);

        self::assertSame($order, $review->getOrder());

        $review->setOrder(null);

        self::assertNull($review->getOrder());
    }

    /**
     * @test
     */
    public function it_returns_review_subject_as_channel(): void
    {
        $review = new StoreReview();
        $channel = $this->createMock(ChannelInterface::class);

        $review->setReviewSubject($channel);

        self::assertSame($channel, $review->getChannel());
    }

    /**
     * @test
     */
    public function it_can_set_channel_directly(): void
    {
        $review = new StoreReview();
        $channel = $this->createMock(ChannelInterface::class);

        $review->setChannel($channel);

        self::assertSame($channel, $review->getReviewSubject());
        self::assertSame($channel, $review->getChannel());

        $review->setChannel(null);

        self::assertNull($review->getReviewSubject());
    }

    /**
     * @test
     */
    public function it_throws_when_setting_channel_with_wrong_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $review = new StoreReview();
        $review->setChannel($this->createMock(BaseChannelInterface::class));
    }
}
