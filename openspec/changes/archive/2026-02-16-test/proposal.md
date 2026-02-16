## Why

The `src/Mailer/` classes (`Emails`, `ReviewRequestEmailManager`) lack unit test coverage. Adding tests ensures the email sending logic correctly validates order and customer data, passes the right arguments to the Sylius mailer, and protects against regressions.

## What Changes

- Add unit test for `ReviewRequestEmailManager` covering:
  - Successful email sending with correct arguments
  - Exception when order is null
  - Exception when customer email is null
- Add unit test for `Emails` verifying the constant value

## Capabilities

### New Capabilities
- `mailer-tests`: Unit tests for `Emails` and `ReviewRequestEmailManager` classes

### Modified Capabilities
<!-- None - this is a test-only change -->

## Impact

- New test files in `tests/Unit/Mailer/`
- No changes to production code
