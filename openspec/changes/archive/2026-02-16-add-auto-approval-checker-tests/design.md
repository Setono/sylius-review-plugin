## Context

The `src/Checker/AutoApproval/` directory contains two concrete classes without test coverage:
- `CompositeAutoApprovalChecker` — aggregates multiple checkers via `CompositeService`, returns `true` only if all inner checkers approve (short-circuit on first `false`)
- `MinimumRatingAutoApprovalChecker` — approves reviews with rating >= a configurable threshold (default 4)

Existing tests for the analogous `EligibilityChecker` composite in `tests/Unit/EligibilityChecker/` provide the reference pattern: Prophecy mocks, BDD-style method names, `self::assert*` assertions.

## Goals / Non-Goals

**Goals:**
- Achieve full branch coverage for `CompositeAutoApprovalChecker` and `MinimumRatingAutoApprovalChecker`
- Follow existing test conventions (Prophecy, BDD naming, `tests/Unit/` directory structure)

**Non-Goals:**
- Testing the `AutoApprovalCheckerInterface`, `ProductAutoApprovalCheckerInterface`, or `StoreAutoApprovalCheckerInterface` (marker interfaces with no logic)
- Integration/functional tests for the Doctrine `prePersist` subscriber that invokes the checkers

## Decisions

- **Test location**: `tests/Unit/Checker/AutoApproval/` mirroring the `src/` directory structure, consistent with `tests/Unit/EligibilityChecker/`
- **Mocking**: Prophecy (`ProphecyTrait`) for `AutoApprovalCheckerInterface` and `ReviewInterface` stubs, matching project convention
- **MinimumRatingAutoApprovalChecker threshold testing**: Test both the default threshold (4) and a custom threshold to verify constructor injection works

## Risks / Trade-offs

- [Minimal risk] `CompositeService::$services` is `protected` — tests add checkers via `add()` method, not direct property access. This is the intended public API.
