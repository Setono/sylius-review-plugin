<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin;

use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\CompositeReviewableOrderChecker;
use Setono\SyliusReviewPlugin\DependencyInjection\Compiler\OverrideProductReviewWorkflowPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusReviewPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeCompilerPass(
            'setono_sylius_review.review_request_eligibility_checker.composite',
            'setono_sylius_review.review_request_eligibility_checker',
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            CompositeReviewableOrderChecker::class,
            'setono_sylius_review.reviewable_order_checker',
        ));

        // Must run after the FrameworkBundle's WorkflowPass (PassConfig::TYPE_BEFORE_OPTIMIZATION, priority 0)
        $container->addCompilerPass(new OverrideProductReviewWorkflowPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -10);
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
