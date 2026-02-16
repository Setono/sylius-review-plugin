<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Checker\AutoApproval;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\CompositeAutoApprovalChecker;
use Sylius\Component\Review\Model\ReviewInterface;

final class CompositeAutoApprovalCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_approves_when_no_checkers_are_registered(): void
    {
        $composite = new CompositeAutoApprovalChecker();
        $review = $this->prophesize(ReviewInterface::class);

        self::assertTrue($composite->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_approves_when_all_checkers_approve(): void
    {
        $review = $this->prophesize(ReviewInterface::class);

        $checker1 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker1->shouldAutoApprove($review->reveal())->willReturn(true);

        $checker2 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker2->shouldAutoApprove($review->reveal())->willReturn(true);

        $composite = new CompositeAutoApprovalChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        self::assertTrue($composite->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_rejects_on_first_failing_checker_and_does_not_call_subsequent_checkers(): void
    {
        $review = $this->prophesize(ReviewInterface::class);

        $checker1 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker1->shouldAutoApprove($review->reveal())->willReturn(false);

        $checker2 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker2->shouldAutoApprove($review->reveal())->shouldNotBeCalled();

        $composite = new CompositeAutoApprovalChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        self::assertFalse($composite->shouldAutoApprove($review->reveal()));
    }

    /**
     * @test
     */
    public function it_rejects_from_second_checker_when_first_approves(): void
    {
        $review = $this->prophesize(ReviewInterface::class);

        $checker1 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker1->shouldAutoApprove($review->reveal())->willReturn(true);

        $checker2 = $this->prophesize(AutoApprovalCheckerInterface::class);
        $checker2->shouldAutoApprove($review->reveal())->willReturn(false);

        $composite = new CompositeAutoApprovalChecker();
        $composite->add($checker1->reveal());
        $composite->add($checker2->reveal());

        self::assertFalse($composite->shouldAutoApprove($review->reveal()));
    }
}
