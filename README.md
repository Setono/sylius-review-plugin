# Sylius Review Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![codecov](https://codecov.io/github/Setono/sylius-review-plugin/graph/badge.svg?token=0H0n3XPAqR)](https://codecov.io/github/Setono/sylius-review-plugin)
[![Mutation testing][ico-infection]][link-infection]

Send review requests to your customers to receive reviews for your store.

The `process` command finds fulfilled orders without review requests and creates them automatically.
After an initial delay ([configurable](#configuration)), it sends a review request email to each customer.

Before sending, the plugin runs an eligibility check. You can [hook into this process](#add-eligibility-checker)
to decide whether a review request should be sent or not.

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

### Import routes

Create `config/routes/setono_sylius_review.yaml`:

```yaml
setono_sylius_review:
    resource: "@SetonoSyliusReviewPlugin/Resources/config/routes.yaml"
```

### Extend the Channel entity (for store reviews)

If you want to use store reviews, you need to extend the Channel entity to implement `ReviewableInterface`. The plugin provides a trait to make this easy.

Create `src/Entity/Channel.php`:

```php
<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ChannelInterface;
use Setono\SyliusReviewPlugin\Model\ChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_channel')]
class Channel extends BaseChannel implements ChannelInterface
{
    use ChannelTrait;

    public function __construct()
    {
        parent::__construct();

        $this->reviews = new ArrayCollection();
    }
}
```

Then configure Sylius to use your custom Channel entity in `config/packages/sylius_channel.yaml`:

```yaml
sylius_channel:
    resources:
        channel:
            classes:
                model: App\Entity\Channel\Channel
```

### Extend the ProductReview entity (for store replies on product reviews)

If you want store owners to reply to product reviews, you need to extend the ProductReview entity. The plugin provides a trait to make this easy.

Create `src/Entity/ProductReview.php`:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\ProductReviewTrait;
use Sylius\Component\Core\Model\ProductReview as BaseProductReview;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_product_review')]
class ProductReview extends BaseProductReview implements ProductReviewInterface
{
    use ProductReviewTrait;
}
```

Then configure Sylius to use your custom ProductReview entity in `config/packages/sylius_review.yaml`:

```yaml
sylius_review:
    resources:
        product:
            review:
                classes:
                    model: App\Entity\ProductReview
```

Store reviews support store replies out of the box — no entity extension needed.

### Extend the Order repository (for store reply notifications)

When the store replies to a product review, the plugin sends a notification email to the customer. To resolve the channel for the email template, the plugin needs to find the customer's latest order. You must extend the order repository to implement the plugin's `OrderRepositoryInterface`.

Create `src/Repository/OrderRepository.php`:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Setono\SyliusReviewPlugin\Repository\OrderRepositoryInterface;
use Setono\SyliusReviewPlugin\Repository\OrderRepositoryTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;

class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    use OrderRepositoryTrait;
}
```

Then configure Sylius to use your custom order repository in `config/packages/sylius_order.yaml`:

```yaml
sylius_order:
    resources:
        order:
            classes:
                repository: App\Repository\OrderRepository
```

### Override the product review admin form template (for store replies)

The plugin adds `storeReply` and `notifyReviewer` fields to the product review form via a form extension, but Sylius's default admin template
doesn't render them. To display these fields, override the template in your application.

Create `templates/bundles/SyliusAdminBundle/ProductReview/_form.html.twig`:

```twig
{% include '@SetonoSyliusReviewPlugin/admin/_markdown_toolbar_scripts.html.twig' %}

<div class="ui stackable grid">
    <div class="twelve wide column">
        <div class="ui segment">
            {{ form_errors(form) }}
            {{ form_row(form.title) }}
            {{ form_row(form.comment) }}
            {{ form_row(form.rating) }}
            {{ form_row(form.storeReply) }}
            {{ form_row(form.notifyReviewer) }}
        </div>
    </div>
    <div class="four wide column">
        {% include '@SyliusAdmin/ProductReview/_product.html.twig' %}
        {% include '@SyliusAdmin/ProductReview/_author.html.twig' %}
    </div>
</div>
```

### Update your database

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Run commands

```bash
php bin/console setono:sylius-review:process
php bin/console setono:sylius-review:prune
php bin/console setono:sylius-review:urls
```

The `process` command does two things:
1. Creates review requests for fulfilled orders that don't have one yet (looking back as far as the `pruning.threshold`)
2. Processes pending review requests (eligibility check + sending emails)

The `prune` command removes old review requests based on the configured threshold.

The `urls` command outputs review page URLs for fulfilled orders, grouped by channel. Useful for testing:

```bash
php bin/console setono:sylius-review:urls          # Default: 5 URLs per channel
php bin/console setono:sylius-review:urls --max=10  # 10 URLs per channel
```

You decide yourself how often you want to run the `process` and `prune` commands.
The process command makes sense to run daily, while the prune command can be run weekly or monthly.

## Configuration

```yaml
setono_sylius_review:
    auto_approval:
        store_review:
            # Whether to auto-approve store reviews
            enabled: true
            # The minimum rating for auto-approving store reviews
            minimum_rating: 4
        product_review:
            # Whether to auto-approve product reviews
            enabled: true
            # The minimum rating for auto-approving product reviews
            minimum_rating: 4
    eligibility:
        # The initial delay before the first eligibility check. The string must be parseable by strtotime(). See https://www.php.net/strtotime
        initial_delay: '+1 week'
        # The maximum number of eligibility checks before the review request is automatically cancelled
        maximum_checks: 5
    reviewable_order:
        # The order states that are considered reviewable
        reviewable_states:
            - fulfilled
        # The period during which a review can be edited after submission. Set to null to disable editing. The string must be parseable by strtotime(). See https://www.php.net/strtotime
        editable_period: '+24 hours'
    pruning:
        # Review requests older than this threshold will be pruned/removed. The string must be parseable by strtotime(). See https://www.php.net/strtotime
        threshold: '-1 month'
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
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-review-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-review-plugin
[link-github-actions]: https://github.com/Setono/sylius-review-plugin/actions
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-review-plugin/master
