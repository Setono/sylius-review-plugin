<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\EventListener\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\EventListener\Doctrine\AutoApprovalListener;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Abstraction\StateMachine\StateMachineInterface;

final class AutoApprovalListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_auto_approves_when_checker_returns_true(): void
    {
        $review = $this->prophesize(StoreReviewInterface::class);
        $checker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $checker->shouldAutoApprove($review->reveal())->willReturn(true);
        $stateMachine->apply($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldBeCalledOnce();

        $args = new PrePersistEventArgs($review->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new AutoApprovalListener(
            StoreReviewInterface::class,
            $checker->reveal(),
            $stateMachine->reveal(),
            StoreReviewWorkflow::NAME,
        );
        $listener->prePersist($args);
    }

    /** @test */
    public function it_does_not_auto_approve_when_checker_returns_false(): void
    {
        $review = $this->prophesize(StoreReviewInterface::class);
        $checker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(true);
        $checker->shouldAutoApprove($review->reveal())->willReturn(false);
        $stateMachine->apply($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($review->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new AutoApprovalListener(
            StoreReviewInterface::class,
            $checker->reveal(),
            $stateMachine->reveal(),
            StoreReviewWorkflow::NAME,
        );
        $listener->prePersist($args);
    }

    /** @test */
    public function it_ignores_entities_that_do_not_match_the_configured_class(): void
    {
        $entity = new \stdClass();
        $checker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $args = new PrePersistEventArgs($entity, $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new AutoApprovalListener(
            StoreReviewInterface::class,
            $checker->reveal(),
            $stateMachine->reveal(),
            StoreReviewWorkflow::NAME,
        );
        $listener->prePersist($args);

        $checker->shouldAutoApprove($entity)->shouldNotHaveBeenCalled();
        $stateMachine->can($entity, StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function it_does_not_invoke_checker_when_state_machine_transition_is_not_available(): void
    {
        $review = $this->prophesize(StoreReviewInterface::class);
        $checker = $this->prophesize(AutoApprovalCheckerInterface::class);
        $stateMachine = $this->prophesize(StateMachineInterface::class);

        $stateMachine->can($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->willReturn(false);
        $checker->shouldAutoApprove($review->reveal())->shouldNotBeCalled();
        $stateMachine->apply($review->reveal(), StoreReviewWorkflow::NAME, StoreReviewWorkflow::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $args = new PrePersistEventArgs($review->reveal(), $this->prophesize(EntityManagerInterface::class)->reveal());

        $listener = new AutoApprovalListener(
            StoreReviewInterface::class,
            $checker->reveal(),
            $stateMachine->reveal(),
            StoreReviewWorkflow::NAME,
        );
        $listener->prePersist($args);
    }
}
