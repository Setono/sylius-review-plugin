<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection;

use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusReviewExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{
         *     eligibility: array{initial_delay: string, maximum_checks: int},
         *     pruning: array{threshold: string},
         *     resources: array
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_review.eligibility.initial_delay', $config['eligibility']['initial_delay']);
        $container->setParameter('setono_sylius_review.eligibility.maximum_checks', $config['eligibility']['maximum_checks']);
        $container->setParameter('setono_sylius_review.pruning.threshold', $config['pruning']['threshold']);

        $container
            ->registerForAutoconfiguration(ReviewRequestEligibilityCheckerInterface::class)
            ->addTag('setono_sylius_review.review_request_eligibility_checker')
        ;

        $loader->load('services.xml');

        $this->registerResources(
            'setono_sylius_review',
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            $config['resources'],
            $container,
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => ReviewRequestWorkflow::getConfig(),
        ]);

        $container->prependExtensionConfig('sylius_mailer', [
            'emails' => [
                Emails::REVIEW_REQUEST => [
                    'template' => '@SetonoSyliusReviewPlugin/email/review_request.html.twig',
                ],
            ],
        ]);
    }
}
