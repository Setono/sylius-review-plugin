## Context

The plugin has 5 event subscriber classes with no unit tests:
- `ReviewAutoApprovalSubscriber` — Doctrine `prePersist` listener that auto-approves store/product reviews
- `CheckEligibilityChecksSubscriber` — Cancels review requests exceeding max eligibility checks
- `IncrementEligibilityChecksSubscriber` — Increments the eligibility check counter
- `ResetSubscriber` — Clears processing state before each processing cycle
- `UpdateNextEligibilityCheckSubscriber` — Calculates exponential backoff for next eligibility check

Existing unit tests in the project use Prophecy for mocking, BDD-style naming (`it_*`), and mirror the source directory structure under `tests/Unit/`.

## Goals / Non-Goals

**Goals:**
- Achieve full unit test coverage for all 5 event subscribers
- Test happy paths, edge cases, and boundary conditions
- Follow existing test conventions (Prophecy, BDD naming, directory mirroring)

**Non-Goals:**
- Integration/functional tests (these are pure unit tests)
- Modifying any production code
- Testing the event dispatcher wiring (that's Symfony's responsibility)

## Decisions

**1. Use Prophecy for all mocks (not PHPUnit mocks)**
Rationale: Project convention per CLAUDE.md. All existing tests use `ProphecyTrait`.

**2. Mirror source directory structure**
- `tests/Unit/EventSubscriber/Doctrine/ReviewAutoApprovalSubscriberTest.php`
- `tests/Unit/EventSubscriber/ReviewRequest/CheckEligibilityChecksSubscriberTest.php`
- `tests/Unit/EventSubscriber/ReviewRequest/IncrementEligibilityChecksSubscriberTest.php`
- `tests/Unit/EventSubscriber/ReviewRequest/ResetSubscriberTest.php`
- `tests/Unit/EventSubscriber/ReviewRequest/UpdateNextEligibilityCheckSubscriberTest.php`

Rationale: Matches existing `tests/Unit/` layout (e.g., `Checker/`, `Factory/`, `Form/`).

**3. Prophesize `ReviewRequestInterface` for ReviewRequest subscribers**
The four ReviewRequest subscribers all operate on `ReviewRequestProcessingStarted` which holds a `ReviewRequestInterface`. Use Prophecy to mock the interface and verify method calls.

**4. Prophesize `PrePersistEventArgs` for Doctrine subscriber**
`ReviewAutoApprovalSubscriber.prePersist()` takes Doctrine's `PrePersistEventArgs`. Mock `getObject()` to return different entity types for each scenario.

**5. Test `getSubscribedEvents()` static method**
Verify the event-to-method mapping and priority for each `EventSubscriberInterface` implementation.

## Risks / Trade-offs

**[Risk] Prophecy mocking of `PrePersistEventArgs`** — Doctrine event args may have constructor requirements.
→ Mitigation: Prophecy handles this since it doesn't call constructors. Verified pattern works with Prophecy.

**[Risk] Time-dependent test for `UpdateNextEligibilityCheckSubscriber`** — Uses `new \DateTimeImmutable()` internally.
→ Mitigation: Assert the calculated datetime is within an acceptable range rather than exact equality, or freeze time via `ClockMock` if available. Since the calculation is deterministic (based on eligibility checks count), we can compute the expected value independently.
