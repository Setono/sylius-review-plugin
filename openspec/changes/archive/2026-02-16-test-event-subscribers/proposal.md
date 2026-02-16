## Why

The 5 event subscribers in `src/EventSubscriber/` (Doctrine and ReviewRequest namespaces) have zero unit test coverage. Adding tests ensures correct behavior for auto-approval logic, eligibility check lifecycle, and exponential backoff scheduling, and prevents regressions.

## What Changes

- Add unit tests for `ReviewAutoApprovalSubscriber` (Doctrine prePersist auto-approval for store and product reviews)
- Add unit tests for `CheckEligibilityChecksSubscriber` (cancels review request when max checks exceeded)
- Add unit tests for `IncrementEligibilityChecksSubscriber` (increments eligibility check counter)
- Add unit tests for `ResetSubscriber` (clears ineligibility reason and processing error)
- Add unit tests for `UpdateNextEligibilityCheckSubscriber` (exponential backoff for next eligibility check)

## Capabilities

### New Capabilities
- `event-subscriber-tests`: Unit tests covering all 5 event subscriber classes in `src/EventSubscriber/`

### Modified Capabilities

_(none)_

## Impact

- New test files in `tests/Unit/EventSubscriber/`
- No production code changes
- No dependency changes
