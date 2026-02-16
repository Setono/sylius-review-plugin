<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\ReviewableOrder;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheck;

final class ReviewableOrderCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_a_reviewable_check(): void
    {
        $check = ReviewableOrderCheck::reviewable();

        self::assertTrue($check->reviewable);
        self::assertNull($check->reason);
    }

    /**
     * @test
     */
    public function it_creates_a_not_reviewable_check_with_reason(): void
    {
        $check = ReviewableOrderCheck::notReviewable('some.reason');

        self::assertFalse($check->reviewable);
        self::assertSame('some.reason', $check->reason);
    }
}
