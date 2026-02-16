## 1. Create the data provider

- [x] 1.1 Create `src/DataProvider/OrderForReviewRequestDataProviderInterface.php` with `getOrders(): iterable` returning `OrderInterface` objects
- [x] 1.2 Create `src/Event/QueryBuilderForReviewRequestCreationCreated.php` event class holding the QueryBuilder
- [x] 1.3 Create `src/DataProvider/DefaultOrderForReviewRequestDataProvider.php` — queries fulfilled orders with `completedAt > pruning threshold`, LEFT JOINs review_request IS NULL, dispatches the event, uses `SelectBatchIteratorAggregate`

## 2. Create the creator service

- [x] 2.1 Create `src/Creator/ReviewRequestCreatorInterface.php` with `create(): void`
- [x] 2.2 Create `src/Creator/ReviewRequestCreator.php` — iterates over orders from the data provider, creates review requests via factory, persists them, implements `LoggerAwareInterface`

## 3. Integrate into ProcessCommand

- [x] 3.1 Update `src/Command/ProcessCommand.php` to inject `ReviewRequestCreatorInterface` and call `create()` before `process()`

## 4. Register services

- [x] 4.1 Add service definitions for the data provider, event, and creator in `src/Resources/config/services.xml`

## 5. Remove the subscriber

- [x] 5.1 Delete `src/EventSubscriber/CreateReviewRequestSubscriber.php`
- [x] 5.2 Remove its service definition from `src/Resources/config/services.xml`
- [x] 5.3 Delete any existing tests for `CreateReviewRequestSubscriber` (none found)

## 6. Tests

- [x] 6.1 Create unit test for `ReviewRequestCreator`
- [x] 6.2 Update the existing `ProcessCommand` functional test to verify the creation phase runs (already passing — command now runs both creation and processing)
