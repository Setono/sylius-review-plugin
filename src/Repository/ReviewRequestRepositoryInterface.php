<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ReviewRequestRepositoryInterface extends RepositoryInterface
{
    public function createForProcessingQueryBuilder(): QueryBuilder;
}
