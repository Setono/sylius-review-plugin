<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection\Compiler;

use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Workflow\Transition;

/**
 * This compiler pass overrides Sylius's sylius_product_review workflow definition
 * to add a 'pending' state as the initial state
 */
final class OverrideProductReviewWorkflowPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $workflowName = ProductReviewWorkflow::NAME;
        // For state_machine type workflows, Symfony uses "state_machine." prefix
        $definitionServiceId = sprintf('state_machine.%s.definition', $workflowName);

        if (!$container->hasDefinition($definitionServiceId)) {
            return;
        }

        $definition = $container->getDefinition($definitionServiceId);
        $arguments = $definition->getArguments();

        // Get the places (first argument) and add 'pending' state
        /** @var list<string> $places */
        $places = $arguments[0] ?? [];
        if (!in_array(ProductReviewWorkflow::STATE_PENDING, $places, true)) {
            array_unshift($places, ProductReviewWorkflow::STATE_PENDING);
            $definition->setArgument(0, $places);
        }

        // Get the transitions (second argument) - these are References to transition service definitions
        /** @var list<Reference> $transitionRefs */
        $transitionRefs = $arguments[1] ?? [];

        // Check if we already have the submit transition by looking at the service IDs
        $submitTransitionServiceId = sprintf('state_machine.%s.transition.%s', $workflowName, ProductReviewWorkflow::TRANSITION_SUBMIT);
        $hasSubmitTransition = false;
        foreach ($transitionRefs as $ref) {
            if ((string) $ref === $submitTransitionServiceId) {
                $hasSubmitTransition = true;

                break;
            }
        }

        if (!$hasSubmitTransition) {
            // Create a new transition service definition
            $transitionDefinition = new Definition(Transition::class, [
                ProductReviewWorkflow::TRANSITION_SUBMIT,
                [ProductReviewWorkflow::STATE_PENDING],
                [ReviewInterface::STATUS_NEW],
            ]);
            $container->setDefinition($submitTransitionServiceId, $transitionDefinition);

            // Add the reference to the transitions array
            array_unshift($transitionRefs, new Reference($submitTransitionServiceId));
            $definition->setArgument(1, $transitionRefs);
        }

        // Update the initial marking (third argument)
        $definition->setArgument(2, [ProductReviewWorkflow::STATE_PENDING]);
    }
}
