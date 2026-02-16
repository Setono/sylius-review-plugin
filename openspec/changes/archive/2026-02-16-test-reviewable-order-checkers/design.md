## Context

The `src/Checker/ReviewableOrder/` directory contains four classes with no test coverage:
- `ReviewableOrderCheck` — value object with factory methods
- `CompositeReviewableOrderChecker` — aggregates checkers, short-circuits on first failure
- `OrderFulfilledReviewableOrderChecker` — checks order state against allowed states
- `StoreReviewEditableReviewableOrderChecker` — checks if existing review is within editable period

Existing test patterns in `tests/Unit/Checker/AutoApproval/` use Prophecy for mocking and BDD-style naming.

## Goals / Non-Goals

**Goals:**
- Unit test all four classes in `src/Checker/ReviewableOrder/`
- Cover happy paths, edge cases, and boundary conditions
- Follow existing test conventions (Prophecy, BDD naming)

**Non-Goals:**
- Functional/integration tests (these are pure unit tests)
- Testing `CompositeService` base class behavior from the library
- Modifying production code

## Decisions

- **Test location**: `tests/Unit/Checker/ReviewableOrder/` mirroring source structure
- **Mocking**: Use Prophecy via `ProphecyTrait` per project conventions
- **One test class per source class**: Four test files total
- **Time-sensitive tests**: `StoreReviewEditableReviewableOrderChecker` tests will use `DateTimeImmutable` objects set to specific times to test editable period boundaries without relying on real clock

## Risks / Trade-offs

- `StoreReviewEditableReviewableOrderChecker` uses `new \DateTimeImmutable()` internally, making exact boundary testing difficult. Tests will use dates far enough in the future/past to avoid flakiness.
