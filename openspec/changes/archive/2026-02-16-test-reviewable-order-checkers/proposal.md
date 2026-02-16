## Why

The `src/Checker/ReviewableOrder/` classes have no unit test coverage. These checkers determine whether an order is eligible for review submission and are critical to the review flow. Adding tests ensures correctness and prevents regressions.

## What Changes

- Add unit tests for `CompositeReviewableOrderChecker` (composite/short-circuit behavior)
- Add unit tests for `OrderFulfilledReviewableOrderChecker` (state-based eligibility)
- Add unit tests for `StoreReviewEditableReviewableOrderChecker` (editable period logic)
- Add unit tests for `ReviewableOrderCheck` value object (factory methods)

## Capabilities

### New Capabilities
- `reviewable-order-checker-tests`: Unit tests for all ReviewableOrder checker classes and the ReviewableOrderCheck value object

### Modified Capabilities

## Impact

- New test files in `tests/Unit/Checker/ReviewableOrder/`
- No production code changes
- No API or dependency changes
