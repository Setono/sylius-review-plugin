## Context

Review requests are currently created synchronously inside the Sylius order completion transaction via `CreateReviewRequestSubscriber` (listens to `sylius.order.pre_complete`). This causes database lock contention. The creation needs to move to the existing `process` command, using a data provider pattern for fetching eligible orders.

## Goals / Non-Goals

**Goals:**
- Remove the synchronous subscriber from the checkout flow
- Add a creator service that finds fulfilled orders without review requests and creates them
- Use a data provider pattern with an event for query customization
- Merge creation into the existing `setono:sylius-review:process` command
- Reuse `pruning.threshold` as the order lookback cutoff

**Non-Goals:**
- Changing the processing/eligibility logic (stays as-is)
- Adding a separate command for creation
- Changing the `ReviewRequestFactory` or workflow

## Decisions

### Data Provider Pattern

Following the pattern from `setono/sylius-meilisearch-plugin`, introduce:

- `OrderForReviewRequestDataProviderInterface` with a single method `getOrders(): iterable` returning `OrderInterface` objects
- `DefaultOrderForReviewRequestDataProvider` implementation that:
  - Queries orders in fulfilled state with `completedAt > pruning threshold`
  - LEFT JOINs review_request and filters `IS NULL` to find orders without requests
  - Dispatches a `QueryBuilderForReviewRequestCreationCreated` event on the QueryBuilder before execution
  - Uses `SelectBatchIteratorAggregate` with batch size 100 to yield `OrderInterface` objects (the batch iterator handles entity manager clearing)

The event allows plugin users to customize the query (e.g., filter by channel, exclude specific orders).

### Creator Service

- `ReviewRequestCreatorInterface` with a single method `create(): void`
- `ReviewRequestCreator` implementation that:
  - Iterates over orders from the data provider
  - Uses the existing `ReviewRequestFactory::createFromOrder()` to create the review request
  - Persists via the entity manager
  - Implements `LoggerAwareInterface` for console output

### ProcessCommand Integration

The `ProcessCommand` will inject `ReviewRequestCreatorInterface` alongside the existing `ReviewRequestProcessorInterface`. The `execute` method runs creation first, then processing:

```
$this->reviewRequestCreator->create();
$this->reviewRequestProcessor->process();
```

### Removal

- Delete `CreateReviewRequestSubscriber` class
- Remove its service definition from `services.xml`
- The `hasExistingForOrder` method on `ReviewRequestRepository` can remain (it may be useful for other purposes), but is no longer called by the plugin itself

### Parameter Reuse

The data provider uses the existing `setono_sylius_review.pruning.threshold` parameter (default: `'-1 month'`) as the order lookback cutoff. This means orders older than 1 month are not considered for review request creation, which aligns with the pruning behavior.

## Risks / Trade-offs

- **Breaking change**: Users who don't run the `process` command via cron won't get review requests. This should already be a requirement, so impact is minimal.
- **Slight delay**: Review requests are no longer created instantly at checkout — they're created on the next cron run. This is acceptable since the `initialDelay` (default: +1 week) means processing wouldn't happen immediately anyway.
- **First run on existing shops**: The cutoff via `pruning.threshold` prevents creating review requests for all historical orders — only orders within the lookback window are considered.
