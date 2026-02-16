<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Setono\SyliusReviewPlugin\EligibilityChecker\EligibilityCheck;

final class EligibilityCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_an_eligible_check(): void
    {
        $check = EligibilityCheck::eligible();

        self::assertTrue($check->eligible);
        self::assertNull($check->reason);
    }

    /**
     * @test
     */
    public function it_creates_an_ineligible_check_with_reason(): void
    {
        $check = EligibilityCheck::ineligible('Order is not fulfilled');

        self::assertFalse($check->eligible);
        self::assertSame('Order is not fulfilled', $check->reason);
    }
}
