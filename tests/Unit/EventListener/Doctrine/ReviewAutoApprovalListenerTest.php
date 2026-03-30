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
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;

final class ReviewAutoApprovalListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_auto_approves_store_review_via_state_machine(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($storeReview->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(true);
        $stateMachine->apply($storeReview->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $stateMachine->reveal());
        $listener->prePersist($args);
    }

    /** @test */
    public function it_does_not_auto_approve_store_review_when_checker_returns_false(): void
    {
        $storeReview = $this->prophesize(StoreReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($storeReview->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $storeChecker->shouldAutoApprove($storeReview->reveal())->willReturn(false);
        $stateMachine->apply($storeReview->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($storeReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $stateMachine->reveal());
        $listener->prePersist($args);
    }

    /** @test */
    public function it_auto_approves_product_review_via_state_machine(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($productReview->reveal(), ProductReviewWorkflow::NAME, ProductReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(true);
        $stateMachine->apply($productReview->reveal(), ProductReviewWorkflow::NAME, ProductReviewWorkflow::TRANSITION_ACCEPT)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $stateMachine->reveal());
        $listener->prePersist($args);
    }

    /** @test */
    public function it_does_not_auto_approve_product_review_when_checker_returns_false(): void
    {
        $productReview = $this->prophesize(ProductReviewInterface::class);
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($productReview->reveal(), ProductReviewWorkflow::NAME, ProductReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $productChecker->shouldAutoApprove($productReview->reveal())->willReturn(false);
        $stateMachine->apply($productReview->reveal(), ProductReviewWorkflow::NAME, ProductReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($productReview->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $stateMachine->reveal());
        $listener->prePersist($args);
    }

    /** @test */
    public function it_ignores_entities_that_are_neither_store_nor_product_reviews(): void
    {
        $entity = new \stdClass();
        $storeChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $productChecker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $args = new PrePersistEventArgs($entity, $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new ReviewAutoApprovalListener($storeChecker->reveal(), $productChecker->reveal(), $stateMachine->reveal());
        $listener->prePersist($args);

        $storeChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
        $productChecker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
    }
}
