<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection;

use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\Form\Type\ReviewRequestEmailType;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webmozart\Assert\Assert;

final class SetonoSyliusReviewExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @var array{
         *     eligibility: array{initial_delay: string, maximum_checks: int},
         *     pruning: array{threshold: string},
         *     resources: array<string, mixed>
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

        self::registerEmailFormType($container);

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

    private static function registerEmailFormType(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('kernel.bundles')) {
            return;
        }

        $bundles = $container->getParameter('kernel.bundles');
        Assert::isArray($bundles);

        if (!isset($bundles['SynoliaSyliusMailTesterPlugin'])) {
            return;
        }

        $container
            ->setDefinition(
                'setono_sylius_review.form.type.review_request_email',
                new Definition(ReviewRequestEmailType::class, ['%setono_sylius_review.model.review_request.class%']),
            )
            ->addTag('form.type')
            ->addTag('app.resolvable_form_type.resolver')
        ;
    }
}
