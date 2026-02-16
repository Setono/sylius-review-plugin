## Context

The `src/Mailer/` directory contains three classes:
- `Emails` — A constants class with a single email subject constant and a private constructor
- `ReviewRequestEmailManagerInterface` — Interface defining `sendReviewRequest()`
- `ReviewRequestEmailManager` — Implementation that validates the review request has an order and customer email, then delegates to Sylius `SenderInterface`

These classes have no unit test coverage. The rest of the codebase follows a consistent testing pattern using PHPUnit with Prophecy for mocking.

## Goals / Non-Goals

**Goals:**
- Unit test `ReviewRequestEmailManager` to verify correct email sending behavior and assertion failures
- Unit test `Emails` to verify the constant value

**Non-Goals:**
- Integration testing of actual email delivery
- Testing `ReviewRequestEmailManagerInterface` (it's just an interface)
- Modifying any production code

## Decisions

1. **Use Prophecy for mocking** — Consistent with the rest of the test suite. Mock `SenderInterface`, `ReviewRequestInterface`, `OrderInterface`, and `CustomerInterface`.

2. **Test assertion failures via `expectException`** — `ReviewRequestEmailManager` uses `Webmozart\Assert` which throws `InvalidArgumentException`. Test both null order and null email scenarios.

3. **Place tests in `tests/Unit/Mailer/`** — Mirrors the `src/Mailer/` directory structure, consistent with existing test organization (e.g., `tests/Unit/Factory/`, `tests/Unit/EligibilityChecker/`).

## Risks / Trade-offs

- [Minimal risk] These are straightforward unit tests with no architectural impact.
