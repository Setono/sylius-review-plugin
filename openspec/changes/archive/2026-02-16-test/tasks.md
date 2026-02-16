## 1. Emails Test

- [x] 1.1 ~~Create `tests/Unit/Mailer/EmailsTest.php`~~ Skipped — PHPStan flags constant comparison as always-true; `Emails` is a trivial constants class with no testable logic

## 2. ReviewRequestEmailManager Tests

- [x] 2.1 Create `tests/Unit/Mailer/ReviewRequestEmailManagerTest.php` with Prophecy mocks for `SenderInterface`, `ReviewRequestInterface`, `OrderInterface`, and `CustomerInterface`
- [x] 2.2 Add test: successful email sending verifies `SenderInterface::send()` is called with correct email code, recipient array, and data array (reviewRequest, channel, localeCode)
- [x] 2.3 Add test: throws `InvalidArgumentException` when review request returns null order
- [x] 2.4 Add test: throws `InvalidArgumentException` when order has null customer (no email)
- [x] 2.5 Add test: throws `InvalidArgumentException` when customer exists but email is null

## 3. Verification

- [x] 3.1 Run unit tests to confirm all pass
- [x] 3.2 Run PHPStan to confirm no static analysis errors
