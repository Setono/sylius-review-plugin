## Why

The repository classes `ReviewRequestRepository` and `StoreReviewRepository` contain custom query logic (filtering, deletion, counting) with zero functional test coverage. These are database-dependent methods that must be tested against a real database to verify correct query construction and behavior.

## What Changes

- Add functional tests for `ReviewRequestRepository`:
  - `createForProcessingQueryBuilder()` — filters pending requests with nextEligibilityCheckAt <= now
  - `removeBefore()` — deletes requests created before a threshold date
  - `removeCancelled()` — deletes cancelled requests
  - `hasExistingForOrder()` — checks if a review request exists for a given order
- Add functional tests for `StoreReviewRepository`:
  - `findOneByOrder()` — finds a store review by its associated order

## Capabilities

### New Capabilities
- `repository-tests`: Functional tests covering ReviewRequestRepository and StoreReviewRepository query methods

### Modified Capabilities

_(none)_

## Impact

- New test files in `tests/Functional/Repository/`
- No production code changes
- Requires test database with Sylius fixtures loaded (already a prerequisite for existing functional tests)
