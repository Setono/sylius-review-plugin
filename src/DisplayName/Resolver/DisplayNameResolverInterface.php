<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DisplayName\Resolver;

use Sylius\Component\Review\Model\ReviewInterface;

interface DisplayNameResolverInterface
{
    public function resolve(ReviewInterface $review): string;
}
