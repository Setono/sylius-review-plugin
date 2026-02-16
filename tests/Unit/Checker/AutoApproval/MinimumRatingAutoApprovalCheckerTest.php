<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\AutoApproval;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\MinimumRatingAutoApprovalChecker;
use Sylius\Component\Review\Model\ReviewInterface;

final class MinimumRatingAutoApprovalCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_approves_when_rating_equals_default_threshold(): void
    {
        $checker = new MinimumRatingAutoApprovalChecker();
        $review = $this->prophesize(ReviewInterface::class);
        $review->getRating()->willReturn(4);

        self::assertTrue($checker->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_approves_when_rating_exceeds_default_threshold(): void
    {
        $checker = new MinimumRatingAutoApprovalChecker();
        $review = $this->prophesize(ReviewInterface::class);
        $review->getRating()->willReturn(5);

        self::assertTrue($checker->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_rejects_when_rating_is_below_default_threshold(): void
    {
        $checker = new MinimumRatingAutoApprovalChecker();
        $review = $this->prophesize(ReviewInterface::class);
        $review->getRating()->willReturn(3);

        self::assertFalse($checker->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_approves_at_boundary_with_custom_threshold(): void
    {
        $checker = new MinimumRatingAutoApprovalChecker(3);
        $review = $this->prophesize(ReviewInterface::class);
        $review->getRating()->willReturn(3);

        self::assertTrue($checker->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_rejects_when_rating_is_null(): void
    {
        $checker = new MinimumRatingAutoApprovalChecker();
        $review = $this->prophesize(ReviewInterface::class);
        $review->getRating()->willReturn(null);

        self::assertFalse($checker->shouldAutoApprove($review->reveal()));
    }
}
