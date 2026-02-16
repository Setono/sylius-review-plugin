## 1. Remove data provider abstraction

- [x] 1.1 Delete `src/DataProvider/OrderForReviewRequestDataProviderInterface.php`
- [x] 1.2 Delete `src/DataProvider/OrderForReviewRequestDataProvider.php`
- [x] 1.3 Remove the `setono_sylius_review.data_provider.order_for_review_request` service definition from `src/Resources/config/services.xml`

## 2. Rewrite ReviewRequestCreator

- [x] 2.1 Update constructor: remove `OrderForReviewRequestDataProviderInterface`, add `EventDispatcherInterface`, `string $orderClass`, `string $reviewRequestClass`, `string $threshold`
- [x] 2.2 Implement `create()`: build QueryBuilder with the same filters (fulfilled state, no existing review request, within threshold), dispatch `QueryBuilderForReviewRequestCreationCreated`, wrap in `SimpleBatchIteratorAggregate::fromQuery()`, iterate and persist (no manual flush)
- [x] 2.3 Update the creator service definition in `services.xml`: replace data provider argument with `event_dispatcher`, `%sylius.model.order.class%`, `%setono_sylius_review.model.review_request.class%`, `%setono_sylius_review.pruning.threshold%`

## 3. Replace unit test with functional test

- [x] 3.1 Delete `tests/Unit/Creator/ReviewRequestCreatorTest.php`
- [x] 3.2 Create `tests/Functional/Creator/ReviewRequestCreatorTest.php` extending `KernelTestCase` with scenarios: creates review request for fulfilled order, skips orders that already have a review request, skips non-fulfilled orders

## 4. Verify

- [x] 4.1 Run `composer analyse` (PHPStan) — passes
- [x] 4.2 Run `vendor/bin/phpunit` — all tests pass
