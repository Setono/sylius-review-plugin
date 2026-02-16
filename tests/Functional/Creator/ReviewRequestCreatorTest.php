<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Creator;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Creator\ReviewRequestCreatorInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ReviewRequestCreatorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private ReviewRequestCreatorInterface $creator;

    private ReviewRequestRepositoryInterface $reviewRequestRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var ReviewRequestCreatorInterface $creator */
        $creator = self::getContainer()->get('setono_sylius_review.creator.review_request');
        $this->creator = $creator;

        /** @var ReviewRequestRepositoryInterface $reviewRequestRepository */
        $reviewRequestRepository = self::getContainer()->get('setono_sylius_review.repository.review_request');
        $this->reviewRequestRepository = $reviewRequestRepository;
    }

    /** @test */
    public function it_creates_review_request_for_fulfilled_order(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_FULFILLED);
        $order->setCheckoutCompletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->creator->create();

        self::assertTrue(
            $this->reviewRequestRepository->hasExistingForOrder($order),
            'Expected a review request to be created for the fulfilled order.',
        );
    }

    /** @test */
    public function it_does_not_create_review_request_for_non_fulfilled_order(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_NEW);
        $this->entityManager->flush();

        $this->creator->create();

        self::assertFalse(
            $this->reviewRequestRepository->hasExistingForOrder($order),
            'Expected no review request for a non-fulfilled order.',
        );
    }

    /** @test */
    public function it_does_not_create_duplicate_review_request(): void
    {
        $order = $this->findOrder();
        $order->setState(OrderInterface::STATE_FULFILLED);
        $order->setCheckoutCompletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        // Calling create() twice to verify the second call does not create a duplicate review request
        $this->creator->create();
        $this->creator->create();

        $count = (int) $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(rr.id)')
            ->from(ReviewRequestInterface::class, 'rr')
            ->andWhere('rr.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        self::assertSame(1, $count, 'Expected exactly one review request, not a duplicate.');
    }

    private function findOrder(): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        return $order;
    }
}
