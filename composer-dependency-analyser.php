<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
use Synolia\SyliusMailTesterPlugin\Form\Type\LimitedEntityType;
use Synolia\SyliusMailTesterPlugin\Resolver\ResolvableFormTypeInterface;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    ->ignoreUnknownClasses([
        LimitedEntityType::class,
        ResolvableFormTypeInterface::class,
    ])
    ->ignoreErrorsOnPackage('symfony/validator', [ErrorType::UNUSED_DEPENDENCY]) // we use the validator for validation constraints
    ->ignoreErrorsOnPackage('league/commonmark', [ErrorType::UNUSED_DEPENDENCY]) // used for rendering markdown in emails
    ->ignoreErrorsOnPackage('twig/extra-bundle', [ErrorType::UNUSED_DEPENDENCY]) // used for rendering markdown in emails
    ->ignoreErrorsOnPackage('twig/markdown-extra', [ErrorType::UNUSED_DEPENDENCY]) // used for rendering markdown in emails
;
