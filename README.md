# Sylius Review Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Send review requests to your customers to receive reviews for your store.

## Installation

```bash
composer require setono/sylius-review-plugin
```

### Add plugin class to your `bundles.php`:

Make sure you add it before `SyliusGridBundle`, otherwise you'll get
`You have requested a non-existent parameter "setono_sylius_review.model.review_request.class".` exception.

```php
<?php
$bundles = [
    // ...
    Setono\SyliusReviewPlugin\SetonoSyliusReviewPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
];
```

### Update your database:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```
   
[ico-version]: https://poser.pugx.org/setono/sylius-review-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-review-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-review-plugin/actions/workflows/build.yaml/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-review-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2FSyliusPluginSkeleton%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-review-plugin
[link-github-actions]: https://github.com/Setono/sylius-review-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-review-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-review-plugin/master
