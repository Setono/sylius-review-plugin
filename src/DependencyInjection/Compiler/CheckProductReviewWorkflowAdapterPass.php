<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection\Compiler;

use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CheckProductReviewWorkflowAdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $adapter = $this->resolveAdapter($container);

        if ('symfony_workflow' !== $adapter) {
            throw new \LogicException(sprintf(
                'The Setono Sylius Review plugin requires the "%s" state machine to use the "symfony_workflow" adapter, but it is configured to use "%s". Add the following to your Sylius configuration: sylius_state_machine_abstraction: graphs_to_adapters_mapping: %s: symfony_workflow',
                ProductReviewWorkflow::NAME,
                $adapter,
                ProductReviewWorkflow::NAME,
            ));
        }
    }

    private function resolveAdapter(ContainerBuilder $container): string
    {
        /** @var array<string, string> $mapping */
        $mapping = $container->hasParameter('sylius_abstraction.state_machine.graphs_to_adapters_mapping')
            ? (array) $container->getParameter('sylius_abstraction.state_machine.graphs_to_adapters_mapping')
            : [];

        if (isset($mapping[ProductReviewWorkflow::NAME])) {
            return $mapping[ProductReviewWorkflow::NAME];
        }

        return $container->hasParameter('sylius_abstraction.state_machine.default_adapter')
            ? (string) $container->getParameter('sylius_abstraction.state_machine.default_adapter')
            : 'winzou_state_machine';
    }
}
