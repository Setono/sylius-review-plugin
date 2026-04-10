<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Tests\Unit\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\CompositeAutoApprovalChecker;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\ProductAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\StoreAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\DependencyInjection\Compiler\RegisterAutoApprovalCheckersPass;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RegisterAutoApprovalCheckersPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterAutoApprovalCheckersPass());
    }

    /** @test */
    public function it_tags_generic_checker_for_both_store_and_product(): void
    {
        $this->registerCompositeServices();
        $this->registerService('my_checker', GenericAutoApprovalChecker::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('my_checker', 'setono_sylius_review.store_review_auto_approval_checker');
        $this->assertContainerBuilderHasServiceDefinitionWithTag('my_checker', 'setono_sylius_review.product_review_auto_approval_checker');
    }

    /** @test */
    public function it_does_not_tag_store_specific_checker_for_product(): void
    {
        $this->registerCompositeServices();
        $this->registerService('store_checker', StoreOnlyAutoApprovalChecker::class);

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('store_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
        self::assertFalse(
            $this->container->getDefinition('store_checker')->hasTag('setono_sylius_review.product_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_does_not_tag_product_specific_checker_for_store(): void
    {
        $this->registerCompositeServices();
        $this->registerService('product_checker', ProductOnlyAutoApprovalChecker::class);

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('product_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
        self::assertFalse(
            $this->container->getDefinition('product_checker')->hasTag('setono_sylius_review.product_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_does_not_tag_the_composite_service_itself(): void
    {
        $this->registerCompositeServices();

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('setono_sylius_review.checker.auto_approval.store_review')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_does_not_tag_already_tagged_services(): void
    {
        $this->registerCompositeServices();
        $this->registerService('my_checker', GenericAutoApprovalChecker::class)
            ->addTag('setono_sylius_review.store_review_auto_approval_checker');

        $this->compile();

        $tags = $this->container->getDefinition('my_checker')->getTag('setono_sylius_review.store_review_auto_approval_checker');
        self::assertCount(1, $tags, 'Tag should not be duplicated');
    }

    /** @test */
    public function it_does_nothing_when_composite_services_are_not_registered(): void
    {
        $this->registerService('my_checker', GenericAutoApprovalChecker::class);

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('my_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
        self::assertFalse(
            $this->container->getDefinition('my_checker')->hasTag('setono_sylius_review.product_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_skips_definitions_with_unresolvable_class(): void
    {
        $this->registerCompositeServices();
        $this->registerService('unresolvable_checker', 'App\\NonExistent\\ClassName');

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('unresolvable_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_skips_definitions_with_unresolved_parameters_in_class(): void
    {
        $this->registerCompositeServices();
        $this->registerService('parameterized_checker', '%hwi_oauth.resource_owner.facebook.class%');

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('parameterized_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_skips_definitions_with_null_class(): void
    {
        $this->registerCompositeServices();
        $this->setDefinition('null_class_service', new \Symfony\Component\DependencyInjection\Definition());

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('null_class_service')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
    }

    /** @test */
    public function it_skips_abstract_definitions(): void
    {
        $this->registerCompositeServices();
        $this->registerService('abstract_checker', GenericAutoApprovalChecker::class)
            ->setAbstract(true);

        $this->compile();

        self::assertFalse(
            $this->container->getDefinition('abstract_checker')->hasTag('setono_sylius_review.store_review_auto_approval_checker'),
        );
    }

    private function registerCompositeServices(): void
    {
        $this->registerService('setono_sylius_review.checker.auto_approval.store_review', CompositeAutoApprovalChecker::class);
        $this->registerService('setono_sylius_review.checker.auto_approval.product_review', CompositeAutoApprovalChecker::class);
    }
}

/** @implements AutoApprovalCheckerInterface<ReviewInterface> */
class GenericAutoApprovalChecker implements AutoApprovalCheckerInterface
{
    public function shouldAutoApprove(ReviewInterface $review): bool
    {
        return true;
    }
}

class StoreOnlyAutoApprovalChecker implements StoreAutoApprovalCheckerInterface
{
    public function shouldAutoApprove(ReviewInterface $review): bool
    {
        return true;
    }
}

class ProductOnlyAutoApprovalChecker implements ProductAutoApprovalCheckerInterface
{
    public function shouldAutoApprove(ReviewInterface $review): bool
    {
        return true;
    }
}
