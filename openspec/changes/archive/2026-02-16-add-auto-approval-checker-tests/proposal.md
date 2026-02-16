## Why

The AutoApproval checker classes (`CompositeAutoApprovalChecker`, `MinimumRatingAutoApprovalChecker`) have no unit tests. Adding tests ensures the composite pattern short-circuits correctly and the minimum rating logic works at boundary values.

## What Changes

- Add unit tests for `CompositeAutoApprovalChecker` covering: no checkers registered, all approve, first rejects (short-circuit), mixed results
- Add unit tests for `MinimumRatingAutoApprovalChecker` covering: ratings at, above, and below the threshold, default and custom thresholds, null/zero ratings

## Capabilities

### New Capabilities
- `auto-approval-checker-tests`: Unit tests for the AutoApproval checker classes

### Modified Capabilities

## Impact

- New test files in `tests/Unit/Checker/AutoApproval/`
- No changes to production code
