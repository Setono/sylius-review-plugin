<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Model\ChannelTrait;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ChannelTraitTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_reviews_collection(): void
    {
        $channel = $this->createChannel();

        self::assertCount(0, $channel->getReviews());
    }

    /**
     * @test
     */
    public function it_adds_a_review(): void
    {
        $channel = $this->createChannel();
        $review = $this->createMock(ReviewInterface::class);
        $review->expects(self::once())->method('setReviewSubject')->with($channel);

        $channel->addReview($review);

        self::assertCount(1, $channel->getReviews());
        self::assertTrue($channel->getReviews()->contains($review));
    }

    /**
     * @test
     */
    public function it_does_not_add_duplicate_review(): void
    {
        $channel = $this->createChannel();
        $review = $this->createMock(ReviewInterface::class);
        $review->expects(self::once())->method('setReviewSubject');

        $channel->addReview($review);
        $channel->addReview($review);

        self::assertCount(1, $channel->getReviews());
    }

    /**
     * @test
     */
    public function it_removes_a_review(): void
    {
        $channel = $this->createChannel();
        $review = $this->createMock(ReviewInterface::class);
        $review->expects(self::once())->method('setReviewSubject');

        $channel->addReview($review);
        $channel->removeReview($review);

        self::assertCount(0, $channel->getReviews());
    }

    /**
     * @test
     */
    public function it_has_null_average_rating_by_default(): void
    {
        $channel = $this->createChannel();

        self::assertNull($channel->getAverageRating());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_average_rating(): void
    {
        $channel = $this->createChannel();

        $channel->setAverageRating(4.5);

        self::assertSame(4.5, $channel->getAverageRating());
    }

    private function createChannel(): ReviewableInterface
    {
        return new class() implements ReviewableInterface {
            use ChannelTrait;

            public function __construct()
            {
                $this->reviews = new ArrayCollection();
            }

            public function getName(): string
            {
                return 'Test Channel';
            }
        };
    }
}
