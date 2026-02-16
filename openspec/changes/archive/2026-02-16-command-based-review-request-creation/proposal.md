## Why

The `CreateReviewRequestSubscriber` runs inside the Sylius order completion transaction (`sylius.order.pre_complete`), causing database lock contention. Moving review request creation to the existing `process` command eliminates this problem and decouples review request creation from the checkout flow.

## What Changes

- **BREAKING**: Remove `CreateReviewRequestSubscriber` — review requests are no longer created synchronously during order completion
- Add a new `ReviewRequestCreator` service that creates review requests for fulfilled orders that don't have one yet
- Add a data provider interface (following the Setono data provider pattern) that supplies orders eligible for review request creation, with an event to allow query customization
- Merge the creation phase into the existing `setono:sylius-review:process` command — it runs creation first, then processing
- Reuse the existing `pruning.threshold` config as the cutoff for how far back to look for orders

## Capabilities

### New Capabilities
- `review-request-creation`: The data provider and creator service that find eligible orders and create review requests for them

### Modified Capabilities

## Impact

- **Breaking**: Users relying on automatic review request creation at checkout will need the `process` command running via cron (which they should already have)
- Removed: `CreateReviewRequestSubscriber`, its service registration, and any related tests
- Modified: `ProcessCommand` to call the creator before the processor
- New services: data provider interface + default implementation, creator service, query builder event
- Config: reuses existing `pruning.threshold` parameter for order cutoff
