<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Model\ReviewRequest;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ReviewRequestRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private ReviewRequestRepositoryInterface $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var ReviewRequestRepositoryInterface $repository */
        $repository = self::getContainer()->get('setono_sylius_review.repository.review_request');
        $this->repository = $repository;
    }

    /** @test */
    public function it_returns_pending_request_with_past_eligibility_check_date(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('-1 hour'));
        $this->entityManager->flush();

        /** @var list<ReviewRequestInterface> $results */
        $results = $this->repository->createForProcessingQueryBuilder()->getQuery()->getResult();

        self::assertContains($reviewRequest, $results);
    }

    /** @test */
    public function it_does_not_return_pending_request_with_future_eligibility_check_date(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('+1 day'));
        $this->entityManager->flush();

        /** @var list<ReviewRequestInterface> $results */
        $results = $this->repository->createForProcessingQueryBuilder()->getQuery()->getResult();

        self::assertNotContains($reviewRequest, $results);
    }

    /** @test */
    public function it_does_not_return_completed_request(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $reviewRequest->setState(ReviewRequestInterface::STATE_COMPLETED);
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('-1 hour'));
        $this->entityManager->flush();

        /** @var list<ReviewRequestInterface> $results */
        $results = $this->repository->createForProcessingQueryBuilder()->getQuery()->getResult();

        self::assertNotContains($reviewRequest, $results);
    }

    /** @test */
    public function it_does_not_return_cancelled_request(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $reviewRequest->setState(ReviewRequestInterface::STATE_CANCELLED);
        $reviewRequest->setNextEligibilityCheckAt(new \DateTimeImmutable('-1 hour'));
        $this->entityManager->flush();

        /** @var list<ReviewRequestInterface> $results */
        $results = $this->repository->createForProcessingQueryBuilder()->getQuery()->getResult();

        self::assertNotContains($reviewRequest, $results);
    }

    /** @test */
    public function it_removes_requests_created_before_threshold(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $this->setCreatedAt($reviewRequest, new \DateTimeImmutable('-2 months'));
        $this->entityManager->flush();

        $id = $reviewRequest->getId();
        self::assertNotNull($id);

        $this->repository->removeBefore(new \DateTimeImmutable('-1 month'));

        $this->entityManager->clear();

        self::assertNull($this->repository->find($id));
    }

    /** @test */
    public function it_does_not_remove_requests_created_after_threshold(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $this->entityManager->flush();

        $id = $reviewRequest->getId();
        self::assertNotNull($id);

        $this->repository->removeBefore(new \DateTimeImmutable('-1 month'));

        $this->entityManager->clear();

        self::assertNotNull($this->repository->find($id));
    }

    /** @test */
    public function it_removes_cancelled_requests(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $reviewRequest->setState(ReviewRequestInterface::STATE_CANCELLED);
        $this->entityManager->flush();

        $id = $reviewRequest->getId();
        self::assertNotNull($id);

        $this->repository->removeCancelled();

        $this->entityManager->clear();

        self::assertNull($this->repository->find($id));
    }

    /** @test */
    public function it_does_not_remove_pending_requests_when_removing_cancelled(): void
    {
        $order = $this->findOrder();

        $reviewRequest = $this->createReviewRequest($order);
        $this->entityManager->flush();

        $id = $reviewRequest->getId();
        self::assertNotNull($id);

        $this->repository->removeCancelled();

        $this->entityManager->clear();

        self::assertNotNull($this->repository->find($id));
    }

    /** @test */
    public function it_returns_true_when_review_request_exists_for_order(): void
    {
        $order = $this->findOrder();

        $this->createReviewRequest($order);
        $this->entityManager->flush();

        self::assertTrue($this->repository->hasExistingForOrder($order));
    }

    /** @test */
    public function it_returns_false_when_no_review_request_exists_for_order(): void
    {
        $order = $this->findOrder();

        self::assertFalse($this->repository->hasExistingForOrder($order));
    }

    private function findOrder(): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->entityManager->getRepository(OrderInterface::class)->findOneBy([]);
        self::assertNotNull($order, 'No fixture order found. Make sure Sylius fixtures are loaded.');

        return $order;
    }

    private function createReviewRequest(OrderInterface $order): ReviewRequest
    {
        $reviewRequest = new ReviewRequest();
        $reviewRequest->setOrder($order);

        $this->entityManager->persist($reviewRequest);

        return $reviewRequest;
    }

    private function setCreatedAt(ReviewRequest $reviewRequest, \DateTimeImmutable $date): void
    {
        $reflection = new \ReflectionProperty(ReviewRequest::class, 'createdAt');
        $reflection->setValue($reviewRequest, $date);
    }
}
