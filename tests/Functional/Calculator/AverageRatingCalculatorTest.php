<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Functional\Calculator;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusReviewPlugin\Calculator\AverageRatingCalculator;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AverageRatingCalculatorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private AverageRatingCalculator $calculator;

    private ProductInterface $product;

    private CustomerInterface $customer;

    protected function setUp(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        /** @var AverageRatingCalculator $calculator */
        $calculator = self::getContainer()->get('sylius.calculator.average_rating');
        $this->calculator = $calculator;

        /** @var ProductInterface|null $product */
        $product = $this->entityManager->getRepository(ProductInterface::class)->findOneBy([]);
        self::assertNotNull($product, 'No fixture product found. Make sure Sylius fixtures are loaded.');
        $this->product = $product;

        /** @var CustomerInterface|null $customer */
        $customer = $this->entityManager->getRepository(CustomerInterface::class)->findOneBy([]);
        self::assertNotNull($customer, 'No fixture customer found. Make sure Sylius fixtures are loaded.');
        $this->customer = $customer;

        $this->removeExistingReviews();
    }

    /** @test */
    public function it_returns_correct_average_for_product_with_accepted_reviews(): void
    {
        $this->createReview(4, ReviewInterface::STATUS_ACCEPTED);
        $this->createReview(5, ReviewInterface::STATUS_ACCEPTED);
        $this->createReview(3, ReviewInterface::STATUS_ACCEPTED);

        $average = $this->calculator->calculate($this->product);

        self::assertEqualsWithDelta(4.0, $average, 0.001);
    }

    /** @test */
    public function it_returns_zero_when_product_has_no_accepted_reviews(): void
    {
        $this->createReview(5, ReviewInterface::STATUS_NEW);
        $this->createReview(3, ReviewInterface::STATUS_REJECTED);

        $average = $this->calculator->calculate($this->product);

        self::assertSame(0.0, $average);
    }

    /** @test */
    public function it_only_includes_accepted_reviews_in_the_average(): void
    {
        $this->createReview(5, ReviewInterface::STATUS_ACCEPTED);
        $this->createReview(1, ReviewInterface::STATUS_ACCEPTED);
        $this->createReview(3, ReviewInterface::STATUS_NEW);
        $this->createReview(2, ReviewInterface::STATUS_REJECTED);

        $average = $this->calculator->calculate($this->product);

        self::assertEqualsWithDelta(3.0, $average, 0.001);
    }

    private function createReview(int $rating, string $status): void
    {
        $review = new ProductReview();
        $review->setRating($rating);
        $review->setReviewSubject($this->product);
        $review->setAuthor($this->customer);
        $review->setTitle('Test review');

        // Persist first (ReviewAutoApprovalSubscriber may override status on prePersist)
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        // Then set the desired status after persist to bypass the auto-approval listener
        $review->setStatus($status);
        $this->entityManager->flush();
    }

    private function removeExistingReviews(): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(ProductReview::class, 'r')
            ->andWhere('r.reviewSubject = :product')
            ->setParameter('product', $this->product)
            ->getQuery()
            ->execute()
        ;
    }
}
