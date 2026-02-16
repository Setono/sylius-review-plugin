<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Calculator;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Calculator\CachedAverageRatingCalculator;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedAverageRatingCalculatorTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_falls_back_to_decorated_calculator_when_reviewable_is_not_a_resource(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(3.5);

        $cache = $this->prophesize(CacheInterface::class);
        $cache->get(Argument::cetera())->shouldNotBeCalled();

        $calculator = new CachedAverageRatingCalculator($decorated->reveal(), $cache->reveal());

        self::assertSame(3.5, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_falls_back_to_decorated_calculator_when_reviewable_has_null_id(): void
    {
        $reviewable = $this->prophesize(ReviewableResourceInterface::class);
        $reviewable->getId()->willReturn(null);

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(2.0);

        $cache = $this->prophesize(CacheInterface::class);
        $cache->get(Argument::cetera())->shouldNotBeCalled();

        $calculator = new CachedAverageRatingCalculator($decorated->reveal(), $cache->reveal());

        self::assertSame(2.0, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_caches_result_with_correct_key_format(): void
    {
        $reviewable = $this->prophesize(ReviewableResourceInterface::class);
        $reviewable->getId()->willReturn(42);

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(4.5);

        $item = $this->prophesize(ItemInterface::class);
        $item->expiresAfter(900)->willReturn($item->reveal())->shouldBeCalledOnce();

        $cache = $this->prophesize(CacheInterface::class);
        $cache->get(
            Argument::that(function (string $key) use ($reviewable): bool {
                $expectedClass = str_replace('\\', '_', $reviewable->reveal()::class);

                return str_starts_with($key, 'setono_sylius_review_avg_rating_') &&
                    str_contains($key, $expectedClass) &&
                    str_ends_with($key, '_42');
            }),
            Argument::type('callable'),
        )->will(function (array $args) use ($item) {
            /** @var callable $callback */
            $callback = $args[1];

            return $callback($item->reveal());
        })->shouldBeCalledOnce();

        $calculator = new CachedAverageRatingCalculator($decorated->reveal(), $cache->reveal());

        self::assertSame(4.5, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_respects_custom_cache_lifetime(): void
    {
        $reviewable = $this->prophesize(ReviewableResourceInterface::class);
        $reviewable->getId()->willReturn(7);

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(3.0);

        $item = $this->prophesize(ItemInterface::class);
        $item->expiresAfter(1800)->willReturn($item->reveal())->shouldBeCalledOnce();

        $cache = $this->prophesize(CacheInterface::class);
        $cache->get(Argument::type('string'), Argument::type('callable'))
            ->will(function (array $args) use ($item) {
                /** @var callable $callback */
                $callback = $args[1];

                return $callback($item->reveal());
            })->shouldBeCalledOnce();

        $calculator = new CachedAverageRatingCalculator($decorated->reveal(), $cache->reveal(), 1800);

        self::assertSame(3.0, $calculator->calculate($reviewable->reveal()));
    }
}

interface ReviewableResourceInterface extends ReviewableInterface, ResourceInterface
{
}
