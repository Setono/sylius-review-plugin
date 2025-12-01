<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Calculator;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedAverageRatingCalculator implements ReviewableRatingCalculatorInterface
{
    public function __construct(
        private readonly ReviewableRatingCalculatorInterface $decorated,
        private readonly CacheInterface $cache,
        private readonly int $cacheLifetime = 900,
    ) {
    }

    public function calculate(ReviewableInterface $reviewable): float
    {
        if (!$reviewable instanceof ResourceInterface) {
            return $this->decorated->calculate($reviewable);
        }

        $id = $reviewable->getId();
        if (null === $id) {
            return $this->decorated->calculate($reviewable);
        }

        $key = sprintf(
            'setono_sylius_review_avg_rating_%s_%s',
            str_replace('\\', '_', $reviewable::class),
            (string) $id,
        );

        return $this->cache->get($key, function (ItemInterface $item) use ($reviewable): float {
            $item->expiresAfter($this->cacheLifetime);

            return $this->decorated->calculate($reviewable);
        });
    }
}
