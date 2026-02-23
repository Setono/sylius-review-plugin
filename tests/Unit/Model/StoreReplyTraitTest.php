<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Model\StoreReview;

final class StoreReplyTraitTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_null_store_reply_by_default(): void
    {
        $review = new StoreReview();

        self::assertNull($review->getStoreReply());
        self::assertNull($review->getStoreRepliedAt());
    }

    /**
     * @test
     */
    public function it_auto_sets_store_replied_at_when_setting_a_reply(): void
    {
        $review = new StoreReview();
        $before = new \DateTime();

        $review->setStoreReply('Thank you for your feedback!');

        self::assertSame('Thank you for your feedback!', $review->getStoreReply());
        self::assertNotNull($review->getStoreRepliedAt());
        self::assertGreaterThanOrEqual($before, $review->getStoreRepliedAt());
        self::assertLessThanOrEqual(new \DateTime(), $review->getStoreRepliedAt());
    }

    /**
     * @test
     */
    public function it_clears_store_replied_at_when_clearing_the_reply(): void
    {
        $review = new StoreReview();
        $review->setStoreReply('Initial reply');

        self::assertNotNull($review->getStoreRepliedAt());

        $review->setStoreReply(null);

        self::assertNull($review->getStoreReply());
        self::assertNull($review->getStoreRepliedAt());
    }

    /**
     * @test
     */
    public function it_allows_setting_store_replied_at_directly(): void
    {
        $review = new StoreReview();
        $date = new \DateTime('2025-06-15 12:00:00');

        $review->setStoreRepliedAt($date);

        self::assertEquals($date, $review->getStoreRepliedAt());
    }

    /**
     * @test
     */
    public function it_clears_store_replied_at_when_set_to_null_directly(): void
    {
        $review = new StoreReview();
        $review->setStoreReply('Some reply');

        $review->setStoreRepliedAt(null);

        self::assertNull($review->getStoreRepliedAt());
    }
}
