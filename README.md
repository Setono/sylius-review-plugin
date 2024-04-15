# Sylius Review Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Send review requests to your customers to receive reviews for your store.


```bash
php init
(cd tests/Application && yarn install)
(cd tests/Application && yarn build)
(cd tests/Application && bin/console assets:install)

(cd tests/Application && bin/console doctrine:database:create)
(cd tests/Application && bin/console doctrine:schema:create)

(cd tests/Application && bin/console sylius:fixtures:load -n)
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
