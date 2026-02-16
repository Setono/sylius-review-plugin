<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Event;

use Doctrine\ORM\QueryBuilder;

final class QueryBuilderForReviewRequestCreationCreated
{
    public function __construct(public readonly QueryBuilder $queryBuilder)
    {
    }
}
