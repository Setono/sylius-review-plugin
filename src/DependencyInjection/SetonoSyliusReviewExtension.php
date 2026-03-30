<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection;

use Setono\SyliusReviewPlugin\Checker\AutoApproval\CompositeAutoApprovalChecker;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\MinimumRatingAutoApprovalChecker;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\ProductAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\StoreAutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Checker\ReviewableOrder\ReviewableOrderCheckerInterface;
use Setono\SyliusReviewPlugin\DisplayName\Provider\DisplayNameCandidateProviderInterface;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;
use Setono\SyliusReviewPlugin\EventListener\Doctrine\AutoApprovalListener;
use Setono\SyliusReviewPlugin\Form\Type\ReviewRequestEmailType;
use Setono\SyliusReviewPlugin\Form\Type\StoreReplyNotificationEmailType;
use Setono\SyliusReviewPlugin\Mailer\Emails;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\ReviewRequestWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class SetonoSyliusReviewExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @var array{
         *     auto_approval: array{store_review: array{enabled: bool, minimum_rating: int}, product_review: array{enabled: bool, minimum_rating: int}},
         *     eligibility: array{initial_delay: string, maximum_checks: int},
         *     reviewable_order: array{reviewable_states: list<string>, editable_period: string|null},
         *     pruning: array{threshold: string},
         *     resources: array<string, mixed>
         * } $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_review.eligibility.initial_delay', $config['eligibility']['initial_delay']);
        $container->setParameter('setono_sylius_review.eligibility.maximum_checks', $config['eligibility']['maximum_checks']);
        $container->setParameter('setono_sylius_review.reviewable_order.reviewable_states', $config['reviewable_order']['reviewable_states']);
        $container->setParameter('setono_sylius_review.reviewable_order.editable_period', $config['reviewable_order']['editable_period']);
        $container->setParameter('setono_sylius_review.pruning.threshold', $config['pruning']['threshold']);

        $container
            ->registerForAutoconfiguration(ReviewRequestEligibilityCheckerInterface::class)
            ->addTag('setono_sylius_review.review_request_eligibility_checker')
        ;

        $container
            ->registerForAutoconfiguration(ReviewableOrderCheckerInterface::class)
            ->addTag('setono_sylius_review.reviewable_order_checker')
        ;

        $container
            ->registerForAutoconfiguration(StoreAutoApprovalCheckerInterface::class)
            ->addTag('setono_sylius_review.store_review_auto_approval_checker')
        ;

        $container
            ->registerForAutoconfiguration(ProductAutoApprovalCheckerInterface::class)
            ->addTag('setono_sylius_review.product_review_auto_approval_checker')
        ;

        $container
            ->registerForAutoconfiguration(DisplayNameCandidateProviderInterface::class)
            ->addTag('setono_sylius_review.display_name_candidate_provider')
        ;

        self::registerEmailFormType($container);

        $loader->load('services.xml');

        self::registerAutoApproval($container, $config['auto_approval']);

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
            'workflows' => array_merge(
                ReviewRequestWorkflow::getSymfonyConfig(),
                StoreReviewWorkflow::getSymfonyConfig(),
                [
                    ProductReviewWorkflow::NAME => [
                        'transitions' => [
                            ProductReviewWorkflow::TRANSITION_REQUEST_EDIT => [
                                'from' => [ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED],
                                'to' => ReviewInterface::STATUS_NEW,
                            ],
                        ],
                    ],
                ],
            ),
        ]);

        $container->prependExtensionConfig('winzou_state_machine', array_merge(
            ReviewRequestWorkflow::getWinzouConfig(),
            StoreReviewWorkflow::getWinzouConfig(),
            [
                ProductReviewWorkflow::NAME => [
                    'transitions' => [
                        ProductReviewWorkflow::TRANSITION_REQUEST_EDIT => [
                            'from' => [ReviewInterface::STATUS_ACCEPTED, ReviewInterface::STATUS_REJECTED],
                            'to' => ReviewInterface::STATUS_NEW,
                        ],
                    ],
                ],
            ],
        ));

        $container->prependExtensionConfig('sylius_mailer', [
            'emails' => [
                Emails::REVIEW_REQUEST => [
                    'template' => '@SetonoSyliusReviewPlugin/email/review_request.html.twig',
                ],
                Emails::STORE_REPLY_NOTIFICATION => [
                    'template' => '@SetonoSyliusReviewPlugin/email/store_reply_notification.html.twig',
                ],
            ],
        ]);

        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                '@SetonoSyliusReviewPlugin/form/theme.html.twig',
            ],
        ]);

        $container->prependExtensionConfig('sylius_ui', [
            'events' => [
                'setono_sylius_review.admin.store_review.update.javascripts' => [
                    'blocks' => [
                        'markdown_toolbar_scripts' => [
                            'template' => '@SetonoSyliusReviewPlugin/admin/_markdown_toolbar_scripts.html.twig',
                            'priority' => 10,
                        ],
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('sylius_grid', [
            'grids' => [
                'setono_sylius_review_admin_store_review' => [
                    'driver' => [
                        'name' => 'doctrine/orm',
                        'options' => [
                            'class' => '%setono_sylius_review.model.store_review.class%',
                        ],
                    ],
                    'sorting' => [
                        'date' => 'desc',
                    ],
                    'fields' => [
                        'date' => [
                            'type' => 'datetime',
                            'label' => 'sylius.ui.date',
                            'path' => 'createdAt',
                            'sortable' => 'createdAt',
                            'options' => [
                                'format' => 'd-m-Y H:i:s',
                            ],
                        ],
                        'comment' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.comment',
                            'sortable' => null,
                        ],
                        'rating' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.rating',
                            'sortable' => null,
                        ],
                        'status' => [
                            'type' => 'twig',
                            'label' => 'sylius.ui.status',
                            'sortable' => null,
                            'options' => [
                                'template' => '@SyliusUi/Grid/Field/state.html.twig',
                                'vars' => [
                                    'labels' => '@SyliusAdmin/ProductReview/Label/Status',
                                ],
                            ],
                        ],
                        'reviewSubject' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.channel',
                        ],
                        'author' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.customer',
                        ],
                    ],
                    'filters' => [
                        'comment' => [
                            'type' => 'string',
                            'label' => 'sylius.ui.comment',
                        ],
                        'status' => [
                            'type' => 'select',
                            'label' => 'sylius.ui.status',
                            'form_options' => [
                                'choices' => [
                                    'sylius.ui.new' => 'new',
                                    'sylius.ui.accepted' => 'accepted',
                                    'sylius.ui.rejected' => 'rejected',
                                ],
                            ],
                        ],
                    ],
                    'actions' => [
                        'item' => [
                            'update' => [
                                'type' => 'update',
                            ],
                            'accept' => [
                                'type' => 'apply_transition',
                                'label' => 'sylius.ui.accept',
                                'icon' => 'checkmark',
                                'options' => [
                                    'link' => [
                                        'route' => 'setono_sylius_review_admin_store_review_accept',
                                        'parameters' => [
                                            'id' => 'resource.id',
                                        ],
                                    ],
                                    'class' => 'green',
                                    'transition' => 'accept',
                                    'graph' => StoreReviewWorkflow::NAME,
                                ],
                            ],
                            'reject' => [
                                'type' => 'apply_transition',
                                'label' => 'sylius.ui.reject',
                                'icon' => 'remove',
                                'options' => [
                                    'link' => [
                                        'route' => 'setono_sylius_review_admin_store_review_reject',
                                        'parameters' => [
                                            'id' => 'resource.id',
                                        ],
                                    ],
                                    'class' => 'yellow',
                                    'transition' => 'reject',
                                    'graph' => StoreReviewWorkflow::NAME,
                                ],
                            ],
                            'delete' => [
                                'type' => 'delete',
                            ],
                        ],
                        'bulk' => [
                            'delete' => [
                                'type' => 'delete',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param array{store_review: array{enabled: bool, minimum_rating: int}, product_review: array{enabled: bool, minimum_rating: int}} $autoApprovalConfig
     */
    private static function registerAutoApproval(ContainerBuilder $container, array $autoApprovalConfig): void
    {
        /** @var array{class: class-string<ReviewInterface>, compositeServiceId: string, checkerTag: string, workflowName: string, transition: string, listenerServiceId: string, checkerServiceId: string} $type */
        foreach ([
            'store_review' => [
                'class' => StoreReviewInterface::class,
                'compositeServiceId' => 'setono_sylius_review.checker.auto_approval.store_review',
                'checkerTag' => 'setono_sylius_review.store_review_auto_approval_checker',
                'workflowName' => StoreReviewWorkflow::NAME,
                'transition' => StoreReviewWorkflow::TRANSITION_ACCEPT,
                'listenerServiceId' => 'setono_sylius_review.listener.auto_approval.store_review',
                'checkerServiceId' => 'setono_sylius_review.checker.auto_approval.minimum_rating.store_review',
            ],
            'product_review' => [
                'class' => ProductReviewInterface::class,
                'compositeServiceId' => 'setono_sylius_review.checker.auto_approval.product_review',
                'checkerTag' => 'setono_sylius_review.product_review_auto_approval_checker',
                'workflowName' => ProductReviewWorkflow::NAME,
                'transition' => ProductReviewWorkflow::TRANSITION_ACCEPT,
                'listenerServiceId' => 'setono_sylius_review.listener.auto_approval.product_review',
                'checkerServiceId' => 'setono_sylius_review.checker.auto_approval.minimum_rating.product_review',
            ],
        ] as $key => $type) {
            if (!$autoApprovalConfig[$key]['enabled']) {
                continue;
            }

            $container->setDefinition(
                $type['compositeServiceId'],
                new Definition(CompositeAutoApprovalChecker::class),
            );

            $container
                ->setDefinition(
                    $type['checkerServiceId'],
                    new Definition(MinimumRatingAutoApprovalChecker::class, [$autoApprovalConfig[$key]['minimum_rating']]),
                )
                ->addTag($type['checkerTag'])
            ;

            $container
                ->setDefinition(
                    $type['listenerServiceId'],
                    new Definition(AutoApprovalListener::class, [
                        $type['class'],
                        new Reference($type['compositeServiceId']),
                        new Reference('sylius_abstraction.state_machine'),
                        $type['workflowName'],
                        $type['transition'],
                    ]),
                )
                ->addTag('doctrine.event_listener', ['event' => 'prePersist'])
                ->addTag('doctrine.event_listener', ['event' => 'preUpdate'])
            ;
        }
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
                ReviewRequestEmailType::class,
                new Definition(ReviewRequestEmailType::class, ['%setono_sylius_review.model.review_request.class%']),
            )
            ->addTag('form.type')
            ->addTag('app.resolvable_form_type.resolver')
        ;

        $container
            ->setDefinition(
                StoreReplyNotificationEmailType::class,
                new Definition(StoreReplyNotificationEmailType::class, ['%setono_sylius_review.model.store_review.class%']),
            )
            ->addTag('form.type')
            ->addTag('app.resolvable_form_type.resolver')
        ;
    }
}
