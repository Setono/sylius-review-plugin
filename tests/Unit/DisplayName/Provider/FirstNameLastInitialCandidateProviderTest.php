<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DisplayName\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\DisplayName\Provider\FirstNameLastInitialCandidateProvider;
use Sylius\Component\Review\Model\ReviewerInterface;

final class FirstNameLastInitialCandidateProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_first_name_and_last_initial_when_both_names_are_set(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn('John');
        $reviewer->getLastName()->willReturn('Doe');

        $provider = new FirstNameLastInitialCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame(['John D.'], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_no_candidates_when_first_name_is_null(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn(null);
        $reviewer->getLastName()->willReturn('Doe');

        $provider = new FirstNameLastInitialCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_no_candidates_when_last_name_is_null(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn('John');
        $reviewer->getLastName()->willReturn(null);

        $provider = new FirstNameLastInitialCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_no_candidates_when_last_name_is_empty(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn('John');
        $reviewer->getLastName()->willReturn('');

        $provider = new FirstNameLastInitialCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }
}
