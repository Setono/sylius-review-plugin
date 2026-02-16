<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\EligibilityChecker\CompositeReviewRequestEligibilityChecker;
use Setono\SyliusReviewPlugin\EligibilityChecker\EligibilityCheck;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

final class CompositeReviewRequestEligibilityCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_eligible_when_no_checkers_are_registered(): void
    {
        $composite = new CompositeReviewRequestEligibilityChecker();
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);

        $check = $composite->check($reviewRequest->reveal());

        self::assertTrue($check->eligible);
    }

    /**
     * @test
     */
    public function it_returns_eligible_when_all_checkers_return_eligible(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);

        $checker1 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker1->check($reviewRequest->reveal())->willReturn(EligibilityCheck::eligible());

        $checker2 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker2->check($reviewRequest->reveal())->willReturn(EligibilityCheck::eligible());

        $composite = new CompositeReviewRequestEligibilityChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($reviewRequest->reveal());

        self::assertTrue($check->eligible);
    }

    /**
     * @test
     */
    public function it_returns_ineligible_from_first_checker_and_does_not_call_subsequent_checkers(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);

        $checker1 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker1->check($reviewRequest->reveal())->willReturn(EligibilityCheck::ineligible('First checker failed'));

        $checker2 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker2->check($reviewRequest->reveal())->shouldNotBeCalled();

        $composite = new CompositeReviewRequestEligibilityChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($reviewRequest->reveal());

        self::assertFalse($check->eligible);
        self::assertSame('First checker failed', $check->reason);
    }

    /**
     * @test
     */
    public function it_returns_ineligible_from_second_checker_when_first_is_eligible(): void
    {
        $reviewRequest = $this->prophesize(ReviewRequestInterface::class);

        $checker1 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker1->check($reviewRequest->reveal())->willReturn(EligibilityCheck::eligible());

        $checker2 = $this->prophesize(ReviewRequestEligibilityCheckerInterface::class);
        $checker2->check($reviewRequest->reveal())->willReturn(EligibilityCheck::ineligible('Second checker failed'));

        $composite = new CompositeReviewRequestEligibilityChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        $check = $composite->check($reviewRequest->reveal());

        self::assertFalse($check->eligible);
        self::assertSame('Second checker failed', $check->reason);
    }
}
