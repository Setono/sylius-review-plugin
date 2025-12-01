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
;
