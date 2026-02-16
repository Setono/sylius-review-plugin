<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection\Compiler;

use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\CompositeAutoApprovalChecker;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\ProductAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\StoreAutoApprovalCheckerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass tags services implementing AutoApprovalCheckerInterface
 * (but not the specific StoreAutoApprovalCheckerInterface or ProductAutoApprovalCheckerInterface)
 * with both store and product review auto-approval checker tags.
 */
final class RegisterAutoApprovalCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->isAbstract()) {
                continue;
            }

            $reflectionClass = $container->getReflectionClass($definition->getClass(), false);
            if (null === $reflectionClass) {
                continue;
            }

            if (!$reflectionClass->implementsInterface(AutoApprovalCheckerInterface::class)) {
                continue;
            }

            // Skip the composite service itself to prevent infinite recursion
            if ($reflectionClass->getName() === CompositeAutoApprovalChecker::class) {
                continue;
            }

            if ($reflectionClass->implementsInterface(StoreAutoApprovalCheckerInterface::class) || $reflectionClass->implementsInterface(ProductAutoApprovalCheckerInterface::class)) {
                continue;
            }

            if (!$definition->hasTag('setono_sylius_review.store_review_auto_approval_checker')) {
                $definition->addTag('setono_sylius_review.store_review_auto_approval_checker');
            }

            if (!$definition->hasTag('setono_sylius_review.product_review_auto_approval_checker')) {
                $definition->addTag('setono_sylius_review.product_review_auto_approval_checker');
            }
        }
    }
}
