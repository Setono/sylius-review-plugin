<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DisplayName\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\DisplayName\Provider\CompositeDisplayNameCandidateProvider;
use Setono\SyliusReviewPlugin\DisplayName\Provider\DisplayNameCandidateProviderInterface;
use Sylius\Component\Review\Model\ReviewerInterface;

final class CompositeDisplayNameCandidateProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_aggregates_candidates_from_multiple_providers(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);

        $provider1 = $this->prophesize(DisplayNameCandidateProviderInterface::class);
        $provider1->candidates($reviewer->reveal())->willReturn(['John']);

        $provider2 = $this->prophesize(DisplayNameCandidateProviderInterface::class);
        $provider2->candidates($reviewer->reveal())->willReturn(['John D.']);

        $composite = new CompositeDisplayNameCandidateProvider();
        $composite->add($provider1->reveal());
        $composite->add($provider2->reveal());

        $candidates = [...$composite->candidates($reviewer->reveal())];

        self::assertSame(['John', 'John D.'], $candidates);
    }

    /**
     * @test
     */
    public function it_deduplicates_candidates(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);

        $provider1 = $this->prophesize(DisplayNameCandidateProviderInterface::class);
        $provider1->candidates($reviewer->reveal())->willReturn(['John']);

        $provider2 = $this->prophesize(DisplayNameCandidateProviderInterface::class);
        $provider2->candidates($reviewer->reveal())->willReturn(['John']);

        $composite = new CompositeDisplayNameCandidateProvider();
        $composite->add($provider1->reveal());
        $composite->add($provider2->reveal());

        $candidates = [...$composite->candidates($reviewer->reveal())];

        self::assertSame(['John'], $candidates);
    }

    /**
     * @test
     */
    public function it_skips_empty_string_candidates(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);

        $provider = $this->prophesize(DisplayNameCandidateProviderInterface::class);
        $provider->candidates($reviewer->reveal())->willReturn(['', 'John', '']);

        $composite = new CompositeDisplayNameCandidateProvider();
        $composite->add($provider->reveal());

        $candidates = [...$composite->candidates($reviewer->reveal())];

        self::assertSame(['John'], $candidates);
    }

    /**
     * @test
     */
    public function it_returns_empty_when_no_providers(): void
    {
        $reviewer = $this->prophesize(ReviewerInterface::class);

        $composite = new CompositeDisplayNameCandidateProvider();
        $candidates = [...$composite->candidates($reviewer->reveal())];

        self::assertSame([], $candidates);
    }
}
