<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\Calculator;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Calculator\AverageRatingCalculator;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AverageRatingCalculatorTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_falls_back_to_decorated_when_no_manager_exists_for_entity(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn(null);

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(3.5);

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(3.5, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_falls_back_to_decorated_when_entity_has_no_reviews_association(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);

        $classMetadata = $this->prophesize(ClassMetadata::class);
        $classMetadata->hasAssociation('reviews')->willReturn(false);

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata($reviewable->reveal()::class)->willReturn($classMetadata->reveal());

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn($manager->reveal());

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(2.0);

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(2.0, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_falls_back_to_decorated_when_association_has_no_target_entity(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);

        $classMetadata = $this->prophesize(ClassMetadata::class);
        $classMetadata->hasAssociation('reviews')->willReturn(true);
        $classMetadata->getAssociationMapping('reviews')->willReturn([]);

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata($reviewable->reveal()::class)->willReturn($classMetadata->reveal());

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn($manager->reveal());

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(1.5);

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(1.5, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_falls_back_to_decorated_when_target_entity_does_not_implement_review_interface(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);

        $classMetadata = $this->prophesize(ClassMetadata::class);
        $classMetadata->hasAssociation('reviews')->willReturn(true);
        $classMetadata->getAssociationMapping('reviews')->willReturn([
            'targetEntity' => \stdClass::class,
        ]);

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata($reviewable->reveal()::class)->willReturn($classMetadata->reveal());

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn($manager->reveal());

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate($reviewable->reveal())->willReturn(4.0);

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(4.0, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_calculates_average_rating_using_database_query(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);
        $targetEntity = $this->prophesize(ReviewInterface::class)->reveal()::class;

        $query = $this->prophesize(AbstractQuery::class);
        $query->getSingleScalarResult()->willReturn('4.5');

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select('AVG(r.rating)')->willReturn($queryBuilder->reveal());
        $queryBuilder->from($targetEntity, 'r')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere('r.reviewSubject = :reviewable')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere('r.status = :status')->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter('reviewable', $reviewable->reveal())->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->willReturn($queryBuilder->reveal());
        $queryBuilder->getQuery()->willReturn($query->reveal());

        $classMetadata = $this->prophesize(ClassMetadata::class);
        $classMetadata->hasAssociation('reviews')->willReturn(true);
        $classMetadata->getAssociationMapping('reviews')->willReturn([
            'targetEntity' => $targetEntity,
        ]);

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata($reviewable->reveal()::class)->willReturn($classMetadata->reveal());
        $manager->createQueryBuilder()->willReturn($queryBuilder->reveal());

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn($manager->reveal());

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);
        $decorated->calculate(Argument::any())->shouldNotBeCalled();

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(4.5, $calculator->calculate($reviewable->reveal()));
    }

    /** @test */
    public function it_returns_zero_when_query_returns_null(): void
    {
        $reviewable = $this->prophesize(ReviewableInterface::class);
        $targetEntity = $this->prophesize(ReviewInterface::class)->reveal()::class;

        $query = $this->prophesize(AbstractQuery::class);
        $query->getSingleScalarResult()->willReturn(null);

        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $queryBuilder->select('AVG(r.rating)')->willReturn($queryBuilder->reveal());
        $queryBuilder->from($targetEntity, 'r')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere('r.reviewSubject = :reviewable')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere('r.status = :status')->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter('reviewable', $reviewable->reveal())->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->willReturn($queryBuilder->reveal());
        $queryBuilder->getQuery()->willReturn($query->reveal());

        $classMetadata = $this->prophesize(ClassMetadata::class);
        $classMetadata->hasAssociation('reviews')->willReturn(true);
        $classMetadata->getAssociationMapping('reviews')->willReturn([
            'targetEntity' => $targetEntity,
        ]);

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata($reviewable->reveal()::class)->willReturn($classMetadata->reveal());
        $manager->createQueryBuilder()->willReturn($queryBuilder->reveal());

        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $managerRegistry->getManagerForClass($reviewable->reveal()::class)->willReturn($manager->reveal());

        $decorated = $this->prophesize(ReviewableRatingCalculatorInterface::class);

        $calculator = new AverageRatingCalculator($managerRegistry->reveal(), $decorated->reveal());

        self::assertSame(0.0, $calculator->calculate($reviewable->reveal()));
    }
}
