<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Creator;

use Doctrine\Persistence\ManagerRegistry;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\Doctrine\ORMTrait;
use Setono\SyliusReviewPlugin\Event\QueryBuilderForReviewRequestCreationCreated;
use Setono\SyliusReviewPlugin\Factory\ReviewRequestFactoryInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReviewRequestCreator implements ReviewRequestCreatorInterface, LoggerAwareInterface
{
    use ORMTrait;

    private LoggerInterface $logger;

    /**
     * @param class-string<OrderInterface> $orderClass
     * @param class-string<ReviewRequestInterface> $reviewRequestClass
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ReviewRequestFactoryInterface $reviewRequestFactory,
        private readonly string $orderClass,
        private readonly string $reviewRequestClass,
        private readonly string $threshold,
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->logger = new NullLogger();
    }

    public function create(): void
    {
        $manager = $this->getManager($this->orderClass);

        $qb = $manager
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

        /** @var SimpleBatchIteratorAggregate<array-key, OrderInterface> $orders */
        $orders = SimpleBatchIteratorAggregate::fromQuery($qb->getQuery(), 100);

        $i = 0;

        foreach ($orders as $order) {
            $reviewRequest = $this->reviewRequestFactory->createFromOrder($order);
            $manager->persist($reviewRequest);

            ++$i;
        }

        $this->logger->debug(sprintf('%d review request%s created', $i, 1 === $i ? '' : 's'));
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
