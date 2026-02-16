## Context

The `src/EligibilityChecker/` directory contains four files implementing the eligibility checking subsystem for review requests. Currently there are zero unit tests covering these classes. The existing test suite in `tests/Unit/` uses PHPUnit with Prophecy for mocking and follows BDD-style naming (`it_should_*`).

## Goals / Non-Goals

**Goals:**
- Achieve full unit test coverage for all classes in `src/EligibilityChecker/`
- Follow existing project testing conventions (Prophecy, BDD naming, `tests/Unit/` directory)
- Test edge cases and error paths, not just happy paths

**Non-Goals:**
- Integration/functional tests (these are pure unit tests)
- Modifying production code
- Testing the `ReviewRequestEligibilityCheckerInterface` (it's just an interface, nothing to test)

## Decisions

1. **Test file structure mirrors source**: Test files go in `tests/Unit/EligibilityChecker/` matching the `src/EligibilityChecker/` structure. This follows the existing convention seen in `tests/Unit/Factory/` and `tests/Unit/Form/`.

2. **Prophecy for mocking**: Use `ProphecyTrait` and `$this->prophesize()` for mocking `ReviewRequestInterface` and `ReviewRequestEligibilityCheckerInterface` as mandated by project standards.

3. **No test for the interface**: `ReviewRequestEligibilityCheckerInterface` defines a contract only — no behavior to test.

4. **EligibilityCheck tested without mocks**: It's a simple value object with static factory methods — test directly with assertions.

## Risks / Trade-offs

- [Minimal risk] These are straightforward unit tests with no external dependencies. No migration or deployment concerns.
