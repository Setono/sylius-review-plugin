## Context

`ReviewRequestCreator` currently delegates order querying to `OrderForReviewRequestDataProvider` via an interface. The data provider builds a Doctrine query, dispatches an event for customization, and wraps results in `SimpleBatchIteratorAggregate` (which manages transactions, flush, and clear in batches of 100). Meanwhile, the creator also calls `flush()` after every single persist — resulting in double flushing, per-item flushes that defeat batch processing, and hidden coupling to the data provider's internal batch behavior.

The `ReviewRequestProcessor` already follows the correct pattern: it builds a query via a repository method, wraps it in `SimpleBatchIteratorAggregate`, and never calls `flush()` itself — trusting the batch iterator to handle persistence.

## Goals / Non-Goals

**Goals:**
- Single owner of the persistence lifecycle (the creator, via `SimpleBatchIteratorAggregate`)
- Consistent pattern with `ReviewRequestProcessor`
- Preserve the `QueryBuilderForReviewRequestCreationCreated` event for query customization
- Replace the unit test (which mocked the now-removed interface) with a functional test that exercises the real query and persistence

**Non-Goals:**
- Changing the query logic itself (same filters: fulfilled state, no existing review request, within threshold)
- Changing `ReviewRequestCreatorInterface` — the public contract remains `create(): void`
- Modifying `ReviewRequestProcessor` or any other class

## Decisions

### 1. Inline everything into `ReviewRequestCreator`

The creator absorbs the data provider's responsibilities: query building, event dispatch, and batch iteration wrapping.

**Constructor changes:**
- Remove: `OrderForReviewRequestDataProviderInterface $dataProvider`
- Add: `EventDispatcherInterface $eventDispatcher`, `string $orderClass`, `string $reviewRequestClass`, `string $threshold`

**Why not keep a simpler data provider?** The only value the data provider abstraction provided was swappability, but the `QueryBuilderForReviewRequestCreationCreated` event already serves that purpose better — it lets listeners modify the query without replacing an entire service. No one gains from swapping the data provider implementation.

### 2. Let `SimpleBatchIteratorAggregate` own flush/clear

The creator calls `persist()` on each new review request but never calls `flush()` or `clear()`. The batch iterator handles this every 100 items and at the end, exactly as the processor does.

This works because `SimpleBatchIteratorAggregate` wraps iteration in a transaction and calls `flush()` + `clear()` at batch boundaries. Newly persisted entities (review requests) are included in these flushes.

**Why not use `$query->toIterable()` without the batch iterator?** We need the flush/clear cycle to avoid memory exhaustion on large order sets. `SimpleBatchIteratorAggregate` handles this, and we already depend on it for the processor.

### 3. Functional test instead of unit test

The creator is now an integration piece: it builds a Doctrine query, dispatches an event, iterates with batch processing, and persists entities. Mocking all of this in a unit test would be brittle and test nothing meaningful.

The functional test will:
- Set up a fulfilled order (using Sylius fixtures + state mutation, same pattern as `ReviewControllerTest`)
- Call the creator service from the container
- Assert a `ReviewRequest` was created and persisted for the eligible order
- Assert no `ReviewRequest` is created for ineligible orders (already has one, wrong state, etc.)

Following the existing functional test pattern: extend `WebTestCase`, use `self::getContainer()`, mutate fixture data in `setUp()`, DAMA handles rollback.

Actually, since the creator doesn't need an HTTP client (it's a service, not a controller), the test should extend `KernelTestCase` instead of `WebTestCase`. This boots the kernel and gives container access without the overhead of a browser client.

## Risks / Trade-offs

**[Breaking change for plugin users who decorated the data provider]** → Mitigated by the `QueryBuilderForReviewRequestCreationCreated` event, which is the intended extension point and remains unchanged. Document the migration in the changelog.

**[`clear()` detaches the order entity after batch boundary]** → Not a problem. After `clear()`, the batch iterator re-fetches the next order from the cursor. The creator only uses each order within a single iteration (to create a review request), so detachment after flush is fine.

**[Functional test requires DB + fixtures]** → This is already the pattern for `tests/Functional/`. The test infrastructure (DAMA, fixture loading) is already in place.
