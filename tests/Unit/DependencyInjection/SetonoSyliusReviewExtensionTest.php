<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusReviewPlugin\DependencyInjection\SetonoSyliusReviewExtension;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusReviewExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusReviewExtension(),
        ];
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_review.eligibility.initial_delay', '+1 week');
        $this->assertContainerBuilderHasParameter('setono_sylius_review.eligibility.maximum_checks', 5);
        $this->assertContainerBuilderHasParameter('setono_sylius_review.pruning.threshold', '-1 month');
    }
}
