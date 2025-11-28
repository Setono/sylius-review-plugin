<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return (new Configuration())
    ->addPathToExclude(__DIR__ . '/tests')
    ->ignoreUnknownClasses([
        \Synolia\SyliusMailTesterPlugin\Form\Type\LimitedEntityType::class,
        \Synolia\SyliusMailTesterPlugin\Resolver\ResolvableFormTypeInterface::class,
    ])
;
