<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventSubscriber\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\EventSubscriber\Doctrine\ReviewAutoApprovalSubscriber;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewAutoApprovalSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_sets_accepted_status_when_store_review_is_auto_approved(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);

        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(true);
        $storeReview->setStatus(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $subscriber = new ReviewAutoApprovalSubscriber($storeChecker->reveal(), $productChecker->reveal());
        $subscriber->prePersist($args);
    }

    /**
     * @test
     */
    public function it_does_not_change_status_when_store_review_is_not_auto_approved(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);

        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(false);
        $storeReview->setStatus(ReviewInterface::STATUS_ACCEPTED)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $subscriber = new ReviewAutoApprovalSubscriber($storeChecker->reveal(), $productChecker->reveal());
        $subscriber->prePersist($args);
    }

    /**
     * @test
     */
    public function it_sets_accepted_status_when_product_review_is_auto_approved(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);

        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(true);
        $productReview->setStatus(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $subscriber = new ReviewAutoApprovalSubscriber($storeChecker->reveal(), $productChecker->reveal());
        $subscriber->prePersist($args);
    }

    /**
     * @test
     */
    public function it_does_not_change_status_when_product_review_is_not_auto_approved(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);

        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(false);
        $productReview->setStatus(ReviewInterface::STATUS_ACCEPTED)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $subscriber = new ReviewAutoApprovalSubscriber($storeChecker->reveal(), $productChecker->reveal());
        $subscriber->prePersist($args);
    }

    /**
     * @test
     */
    public function it_ignores_entities_that_are_neither_store_nor_product_reviews(): void
    {
        $entity = new \stdClass();
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);

        $args = new PrePersistEventArgs($entity, $this->prophesize(EntityManagerInterface::class)->reveal());

        $subscriber = new ReviewAutoApprovalSubscriber($storeChecker->reveal(), $productChecker->reveal());
        $subscriber->prePersist($args);

        $storeChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
        $productChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
    }
}
