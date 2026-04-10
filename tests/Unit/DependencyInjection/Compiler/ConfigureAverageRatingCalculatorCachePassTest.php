<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Setono\SyliusReviewPlugin\Calculator\CachedAverageRatingCalculator;
use Setono\SyliusReviewPlugin\DependencyInjection\Compiler\ConfigureAverageRatingCalculatorCachePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigureAverageRatingCalculatorCachePassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConfigureAverageRatingCalculatorCachePass());
    }

    /** @test */
    public function it_removes_cached_calculator_in_debug_mode(): void
    {
        $this->registerService(CachedAverageRatingCalculator::class, CachedAverageRatingCalculator::class);
        $this->setParameter('kernel.debug', true);

        $this->compile();

        $this->assertContainerBuilderNotHasService(CachedAverageRatingCalculator::class);
    }

    /** @test */
    public function it_keeps_cached_calculator_when_not_in_debug_mode(): void
    {
        $this->registerService(CachedAverageRatingCalculator::class, CachedAverageRatingCalculator::class);
        $this->setParameter('kernel.debug', false);

        $this->compile();

        $this->assertContainerBuilderHasService(CachedAverageRatingCalculator::class);
    }

    /** @test */
    public function it_does_nothing_when_cached_calculator_is_not_registered(): void
    {
        $this->setParameter('kernel.debug', true);

        $this->compile();

        $this->assertContainerBuilderNotHasService(CachedAverageRatingCalculator::class);
    }
}
