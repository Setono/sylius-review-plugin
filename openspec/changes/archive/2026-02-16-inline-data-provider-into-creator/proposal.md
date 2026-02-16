## Why

The `ReviewRequestCreator` and `OrderForReviewRequestDataProvider` have muddled persistence responsibilities. The creator flushes after every persist, while the batch iterator in the data provider also flushes and clears every 100 items — resulting in double flushing, hidden coupling to implementation details behind an interface, and a design that works by accident rather than by contract.

## What Changes

- **BREAKING**: Remove `OrderForReviewRequestDataProviderInterface` and `OrderForReviewRequestDataProvider` — the data provider abstraction is eliminated
- Inline the query building, event dispatch, and batch iteration directly into `ReviewRequestCreator`
- Remove per-item `flush()` from the creator — let `SimpleBatchIteratorAggregate` handle flush/clear in batches (consistent with how `ReviewRequestProcessor` already works)
- Remove the data provider service definition from `services.xml` and update the creator's service arguments
- Replace the existing unit test with a functional test (the creator is now an integration piece that builds a real Doctrine query)

## Capabilities

### New Capabilities

None.

### Modified Capabilities

- `review-request-creation`: The "data provider" concept is removed. The creator directly builds the query, dispatches the event, and handles batch iteration and persistence.

## Impact

- **Code removed**: `src/DataProvider/OrderForReviewRequestDataProviderInterface.php`, `src/DataProvider/OrderForReviewRequestDataProvider.php`
- **Code modified**: `src/Creator/ReviewRequestCreator.php`, `src/Resources/config/services.xml`
- **Tests**: `tests/Unit/Creator/ReviewRequestCreatorTest.php` removed, replaced by a functional test
- **Breaking for plugin users**: Anyone depending on `OrderForReviewRequestDataProviderInterface` (e.g., decorating it or replacing the service) will need to use the `QueryBuilderForReviewRequestCreationCreated` event instead
