<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\CompositeAutoApprovalChecker;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\MinimumRatingAutoApprovalChecker;
use Setono\SyliusReviewPlugin\DependencyInjection\SetonoSyliusReviewExtension;
use Setono\SyliusReviewPlugin\EventListener\Doctrine\AutoApprovalListener;

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

    /** @test */
    public function it_registers_auto_approval_services_for_both_types_by_default(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.store_review', CompositeAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.product_review', CompositeAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.minimum_rating.store_review', MinimumRatingAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.minimum_rating.product_review', MinimumRatingAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.listener.auto_approval.store_review', AutoApprovalListener::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.listener.auto_approval.product_review', AutoApprovalListener::class);
    }

    /** @test */
    public function it_does_not_register_store_auto_approval_services_when_disabled(): void
    {
        $this->load([
            'auto_approval' => [
                'store_review' => [
                    'enabled' => false,
                ],
            ],
        ]);

        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.store_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.minimum_rating.store_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.listener.auto_approval.store_review');

        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.product_review', CompositeAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.listener.auto_approval.product_review', AutoApprovalListener::class);
    }

    /** @test */
    public function it_does_not_register_product_auto_approval_services_when_disabled(): void
    {
        $this->load([
            'auto_approval' => [
                'product_review' => [
                    'enabled' => false,
                ],
            ],
        ]);

        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.product_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.minimum_rating.product_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.listener.auto_approval.product_review');

        $this->assertContainerBuilderHasService('setono_sylius_review.checker.auto_approval.store_review', CompositeAutoApprovalChecker::class);
        $this->assertContainerBuilderHasService('setono_sylius_review.listener.auto_approval.store_review', AutoApprovalListener::class);
    }

    /** @test */
    public function it_does_not_register_any_auto_approval_services_when_both_types_disabled(): void
    {
        $this->load([
            'auto_approval' => [
                'store_review' => [
                    'enabled' => false,
                ],
                'product_review' => [
                    'enabled' => false,
                ],
            ],
        ]);

        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.store_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.product_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.minimum_rating.store_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.checker.auto_approval.minimum_rating.product_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.listener.auto_approval.store_review');
        $this->assertContainerBuilderNotHasService('setono_sylius_review.listener.auto_approval.product_review');
    }

    /** @test */
    public function it_uses_custom_minimum_rating_thresholds(): void
    {
        $this->load([
            'auto_approval' => [
                'store_review' => [
                    'minimum_rating' => 3,
                ],
                'product_review' => [
                    'minimum_rating' => 5,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'setono_sylius_review.checker.auto_approval.minimum_rating.store_review',
            0,
            3,
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'setono_sylius_review.checker.auto_approval.minimum_rating.product_review',
            0,
            5,
        );
    }
}
