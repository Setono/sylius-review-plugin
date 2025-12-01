<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Calculator;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Doctrine\ORMTrait;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * A performant average rating calculator that uses database queries instead of loading all reviews into memory.
 */
final class AverageRatingCalculator implements ReviewableRatingCalculatorInterface
{
    use ORMTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly ReviewableRatingCalculatorInterface $decorated,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function calculate(ReviewableInterface $reviewable): float
    {
        try {
            $manager = $this->getManager($reviewable::class);
        } catch (\Throwable) {
            return $this->decorated->calculate($reviewable);
        }

        $classMetadata = $manager->getClassMetadata($reviewable::class);
        if (!$classMetadata->hasAssociation('reviews')) {
            return $this->decorated->calculate($reviewable);
        }

        $association = $classMetadata->getAssociationMapping('reviews');

        if (!isset($association['targetEntity'])) {
            return $this->decorated->calculate($reviewable);
        }

        $targetEntity = $association['targetEntity'];
        if (!is_a($targetEntity, ReviewInterface::class, true)) {
            return $this->decorated->calculate($reviewable);
        }

        return (float) $manager->createQueryBuilder()
            ->select('AVG(r.rating)')
            ->from($targetEntity, 'r')
            ->andWhere('r.reviewSubject = :reviewable')
            ->andWhere('r.status = :status')
            ->setParameter('reviewable', $reviewable)
            ->setParameter('status', ReviewInterface::STATUS_ACCEPTED)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
