<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DataProvider;

use Doctrine\Persistence\ManagerRegistry;
use DoctrineBatchUtils\BatchProcessing\SelectBatchIteratorAggregate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\Event\QueryBuilderForReviewRequestCreationCreated;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderForReviewRequestDataProvider implements OrderForReviewRequestDataProviderInterface
{
    use ORMTrait;

    /**
     * @param class-string<OrderInterface> $orderClass
     * @param class-string<ReviewRequestInterface> $reviewRequestClass
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $orderClass,
        private readonly string $reviewRequestClass,
        private readonly string $threshold,
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    public function getOrders(): iterable
    {
        $qb = $this
            ->getManager($this->orderClass)
            ->createQueryBuilder()
            ->select('o')
            ->from($this->orderClass, 'o')
            ->leftJoin($this->reviewRequestClass, 'rr', 'WITH', 'rr.order = o')
            ->andWhere('rr.id IS NULL')
            ->andWhere('o.state = :state')
            ->andWhere('o.checkoutCompletedAt >= :threshold')
            ->setParameter('state', OrderInterface::STATE_FULFILLED)
            ->setParameter('threshold', new \DateTimeImmutable($this->threshold))
        ;

        $this->eventDispatcher->dispatch(new QueryBuilderForReviewRequestCreationCreated($qb));

        /** @var SelectBatchIteratorAggregate<array-key, OrderInterface> $batch */
        $batch = SelectBatchIteratorAggregate::fromQuery($qb->getQuery(), 100);

        yield from $batch;
    }
}
