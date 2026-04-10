<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Controller\ReviewCommand;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;

final class ReviewCommandTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_has_null_defaults(): void
    {
        $command = new ReviewCommand();

        self::assertNull($command->getDisplayName());
        self::assertNull($command->getStoreReview());
        self::assertTrue($command->getProductReviews()->isEmpty());
    }

    /** @test */
    public function it_sets_and_gets_display_name(): void
    {
        $command = new ReviewCommand();
        $command->setDisplayName('John');

        self::assertSame('John', $command->getDisplayName());
    }

    /** @test */
    public function it_sets_and_gets_store_review(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class)->reveal();

        $command = new ReviewCommand();
        $command->setStoreReview($storeReview);

        self::assertSame($storeReview, $command->getStoreReview());
    }

    /** @test */
    public function it_sets_and_gets_product_reviews_collection(): void
    {
        $review = $this->prophesize(ProductReviewInterface::class)->reveal();
        $collection = new ArrayCollection([$review]);

        $command = new ReviewCommand();
        $command->setProductReviews($collection);

        self::assertSame($collection, $command->getProductReviews());
    }

    /** @test */
    public function it_adds_product_review(): void
    {
        $review = $this->prophesize(ProductReviewInterface::class)->reveal();

        $command = new ReviewCommand();
        $command->addProductReview($review);

        self::assertCount(1, $command->getProductReviews());
        self::assertTrue($command->getProductReviews()->contains($review));
    }

    /** @test */
    public function it_does_not_add_duplicate_product_review(): void
    {
        $review = $this->prophesize(ProductReviewInterface::class)->reveal();

        $command = new ReviewCommand();
        $command->addProductReview($review);
        $command->addProductReview($review);

        self::assertCount(1, $command->getProductReviews());
    }

    /** @test */
    public function it_removes_product_review(): void
    {
        $review = $this->prophesize(ProductReviewInterface::class)->reveal();

        $command = new ReviewCommand();
        $command->addProductReview($review);
        $command->removeProductReview($review);

        self::assertTrue($command->getProductReviews()->isEmpty());
    }
}
