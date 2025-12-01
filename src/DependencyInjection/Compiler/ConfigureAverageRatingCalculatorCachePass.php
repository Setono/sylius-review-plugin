<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection\Compiler;

use Setono\SyliusReviewPlugin\Calculator\CachedAverageRatingCalculator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigureAverageRatingCalculatorCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(CachedAverageRatingCalculator::class)) {
            return;
        }

        if ((bool) $container->getParameter('kernel.debug')) {
            $container->removeDefinition(CachedAverageRatingCalculator::class);
        }
    }
}
