<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventListener\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\EventListener\Doctrine\ReviewAutoApprovalListener;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewAutoApprovalListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_auto_approves_store_review_via_workflow(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $storeWorkflow = $this->prophesize(WorkflowInterface::class);
        $productWorkflow = $this->prophesize(WorkflowInterface::class);

        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(true);
        $storeWorkflow->can($storeReview->reveal(), StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $storeWorkflow->apply($storeReview->reveal(), StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $storeWorkflow->reveal(), $productWorkflow->reveal());
        $listener->prePersist($args);
    }

    /**
     * @test
     */
    public function it_does_not_auto_approve_store_review_when_checker_returns_false(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $storeWorkflow = $this->prophesize(WorkflowInterface::class);
        $productWorkflow = $this->prophesize(WorkflowInterface::class);

        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(false);
        $storeWorkflow->apply($storeReview->reveal(), StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $storeWorkflow->reveal(), $productWorkflow->reveal());
        $listener->prePersist($args);
    }

    /**
     * @test
     */
    public function it_auto_approves_product_review_via_workflow(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $storeWorkflow = $this->prophesize(WorkflowInterface::class);
        $productWorkflow = $this->prophesize(WorkflowInterface::class);

        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(true);
        $productWorkflow->can($productReview->reveal(), ProductReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $productWorkflow->apply($productReview->reveal(), ProductReviewWorkflow::TRANSITION_ACCEPT)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $storeWorkflow->reveal(), $productWorkflow->reveal());
        $listener->prePersist($args);
    }

    /**
     * @test
     */
    public function it_does_not_auto_approve_product_review_when_checker_returns_false(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $storeWorkflow = $this->prophesize(WorkflowInterface::class);
        $productWorkflow = $this->prophesize(WorkflowInterface::class);

        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(false);
        $productWorkflow->apply($productReview->reveal(), ProductReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $storeWorkflow->reveal(), $productWorkflow->reveal());
        $listener->prePersist($args);
    }

    /**
     * @test
     */
    public function it_ignores_entities_that_are_neither_store_nor_product_reviews(): void
    {
        $entity = new \stdClass();
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $storeWorkflow = $this->prophesize(WorkflowInterface::class);
        $productWorkflow = $this->prophesize(WorkflowInterface::class);

        $args = new PrePersistEventArgs($entity, $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $storeWorkflow->reveal(), $productWorkflow->reveal());
        $listener->prePersist($args);

        $storeChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
        $productChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
    }
}
