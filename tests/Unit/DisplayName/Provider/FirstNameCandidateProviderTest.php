<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DisplayName\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\DisplayName\Provider\FirstNameCandidateProvider;
use Sylius\Component\Review\Model\ReviewerInterface;

final class FirstNameCandidateProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_first_name_when_set(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn('John');

        $provider = new FirstNameCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame(['John'], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_no_candidates_when_first_name_is_null(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn(null);

        $provider = new FirstNameCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_no_candidates_when_first_name_is_empty(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);
        $reviewer->getFirstName()->willReturn('');

        $provider = new FirstNameCandidateProvider();
        $candidates = [...$provider->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }
}
