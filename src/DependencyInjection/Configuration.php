<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DependencyInjection;

use Setono\SyliusReviewPlugin\Model\ReviewRequest;
use Setono\SyliusReviewPlugin\Model\StoreReview;
use Setono\SyliusReviewPlugin\Repository\ReviewRequestRepository;
use Setono\SyliusReviewPlugin\Repository\StoreReviewRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_review');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('eligibility')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('initial_delay')
                            ->defaultValue('+1 week')
                            ->info('The initial delay before the first eligibility check. The string must be parseable by strtotime(). See https://www.php.net/strtotime')
                        ->end()
                        ->scalarNode('maximum_checks')
                            ->defaultValue(5)
                            ->info('The maximum number of eligibility checks before the review request is automatically cancelled')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('reviewable_order')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('reviewable_states')
                            ->scalarPrototype()->end()
                            ->defaultValue([OrderInterface::STATE_FULFILLED])
                            ->info('The order states that are considered reviewable')
                        ->end()
                        ->scalarNode('editable_period')
                            ->defaultValue('+24 hours')
                            ->info('The period during which a review can be edited after submission. Set to null to disable editing. The string must be parseable by strtotime(). See https://www.php.net/strtotime')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pruning')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('threshold')
                            ->defaultValue('-1 month')
                            ->info('Review requests older than this threshold will be pruned/removed. The string must be parseable by strtotime(). See https://www.php.net/strtotime')
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('review_request')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                    ->scalarNode('model')->defaultValue(ReviewRequest::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->defaultValue(ReviewRequestRepository::class)->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('store_review')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                    ->scalarNode('model')->defaultValue(StoreReview::class)->cannotBeEmpty()->end()
                                    ->scalarNode('repository')->defaultValue(StoreReviewRepository::class)->cannotBeEmpty()->end()
                                    ->scalarNode('factory')->defaultValue(Factory::class)->end()
        ;
    }
}
