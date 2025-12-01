# Sylius Review Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Send review requests to your customers to receive reviews for your store.

The plugin will create a review request upon customers completing an order. After an initial delay ([configurable](#configuration)),
a review request will be sent as an email to the customer asking them to review your store.

When processing a review request (i.e. trying to send it), the plugin will run an eligibility check to see if the review
request is eligible to be sent. You can [hook into this process](#add-eligibility-checker) to decide whether a review request should be sent or not.

## Installation

```bash
composer require setono/sylius-review-plugin
```

### Add plugin class to your `bundles.php`:

```php
<?php
$bundles = [
    // ...
    Setono\SyliusReviewPlugin\SetonoSyliusReviewPlugin::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
];
```

Make sure you add it before `SyliusGridBundle`, otherwise you'll get
`You have requested a non-existent parameter "setono_sylius_review.model.review_request.class".` exception.

### Extend the ProductReview entity

This plugin extends Sylius's ProductReview entity with additional fields. You need to create your own ProductReview entity that implements the plugin's interface and uses its trait.

Create `src/Entity/ProductReview.php`:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\ProductReviewTrait;
use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\ProductReview as BaseProductReview;
use Sylius\Component\Order\Model\OrderInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_review')]
class ProductReview extends BaseProductReview implements ProductReviewInterface
{
    use ProductReviewTrait;

    #[ORM\ManyToOne(targetEntity: OrderInterface::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected ?OrderInterface $order = null;

    #[ORM\ManyToOne(targetEntity: ReviewInterface::class, inversedBy: 'productReviews')]
    #[ORM\JoinColumn(name: 'review_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected ?ReviewInterface $review = null;

    public function __construct()
    {
        parent::__construct();

        $this->status = self::STATUS_PENDING;
    }
}
```

### Extend the ProductReview repository

You also need to create a custom repository that implements the plugin's interface:

Create `src/Repository/ProductReviewRepository.php`:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Setono\SyliusReviewPlugin\Repository\ProductReviewRepositoryInterface;
use Setono\SyliusReviewPlugin\Repository\ProductReviewRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductReviewRepository as BaseProductReviewRepository;

class ProductReviewRepository extends BaseProductReviewRepository implements ProductReviewRepositoryInterface
{
    use ProductReviewRepositoryTrait;
}
```

### Configure Sylius resources

Configure Sylius to use your custom entity and repository in `config/packages/sylius_review.yaml`:

```yaml
sylius_review:
    resources:
        product:
            review:
                classes:
                    model: App\Entity\ProductReview
                    repository: App\Repository\ProductReviewRepository
```

### Update your database

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Run commands
There are two commands in this plugin. One for processing review requests and one for pruning the review request table.

```bash
php bin/console setono:sylius-review:process
php bin/console setono:sylius-review:prune
```

You decide yourself how often you want to run these commands.
The process command makes sense to run daily, while the prune command can be run weekly or monthly.

## Configuration

```yaml
setono_sylius_review:
    eligibility:
        # The initial delay before the first eligibility check. The string must be parseable by strtotime(). See https://www.php.net/strtotime
        initial_delay: '+1 week'

        # The maximum number of eligibility checks before the review request is automatically cancelled
        maximum_checks: 5
    
    pruning:
        # Review requests older than this threshold will be pruned/removed. The string must be parseable by strtotime(). See https://www.php.net/strtotime
        threshold: '-1 month'
    
    resources:
        review_request:
            options: ~
            classes:
                model: Setono\SyliusReviewPlugin\Model\ReviewRequest
                repository: Setono\SyliusReviewPlugin\Repository\ReviewRequestRepository
                factory: Sylius\Component\Resource\Factory\Factory
```

## Add eligibility checker

You can add your own eligibility checker by implementing the `Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface`.

```php
<?php
use Setono\SyliusReviewPlugin\EligibilityChecker\EligibilityCheck;
use Setono\SyliusReviewPlugin\EligibilityChecker\ReviewRequestEligibilityCheckerInterface;

final class MyEligibilityChecker implements ReviewRequestEligibilityCheckerInterface
{
    public function check(ReviewRequestInterface $reviewRequest): EligibilityCheck
    {
        if($this->getCustomer()->hasGreenEyes()) {
            return EligibilityCheck::ineligible('The review request is not eligible because we don't trust people with green eyes...');
        }
        
        return EligibilityCheck::eligible();
    }
}
```

When you implement the interface, your service will automatically be added to the list of eligibility checkers.
However, if you don't use autoconfiguration, you need to tag your service with `setono_sylius_review.review_request_eligibility_checker`.

## Mail tester plugin

If you use the [mail tester plugin](https://github.com/synolia/SyliusMailTesterPlugin/) you can test the review request email
directly from the admin interface. Just go to `https://your-store.com/admin/mail/tester`
and select the `setono_sylius_review__review_request` in the `Subjects` dropdown.

[ico-version]: https://poser.pugx.org/setono/sylius-review-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-review-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-review-plugin/actions/workflows/build.yaml/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-review-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-review-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-review-plugin
[link-github-actions]: https://github.com/Setono/sylius-review-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-review-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-review-plugin/master
