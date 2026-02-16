<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin;

use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\CompositeReviewableOrderChecker;
use Setono\SyliusReviewPlugin\DependencyInjection\Compiler\ConfigureAverageRatingCalculatorCachePass;
use Setono\SyliusReviewPlugin\DependencyInjection\Compiler\RegisterAutoApprovalCheckersPass;
use Setono\SyliusReviewPlugin\EligibilityChecker\CompositeReviewRequestEligibilityChecker;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusReviewPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeReviewRequestEligibilityChecker::class,
            'setono_sylius_review.review_request_eligibility_checker',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeReviewableOrderChecker::class,
            'setono_sylius_review.reviewable_order_checker',
        ));

        $container->addCompilerPass(new ConfigureAverageRatingCalculatorCachePass());
        $container->addCompilerPass(new RegisterAutoApprovalCheckersPass());

        $container->addCompilerPass(new CompositeCompilerPass(
            'setono_sylius_review.checker.auto_approval.store_review',
            'setono_sylius_review.store_review_auto_approval_checker',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            'setono_sylius_review.checker.auto_approval.product_review',
            'setono_sylius_review.product_review_auto_approval_checker',
        ));
    }

    /**
     * @return list<string>
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }
}
