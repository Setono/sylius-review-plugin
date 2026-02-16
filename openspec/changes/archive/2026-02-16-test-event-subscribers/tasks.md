## 1. Doctrine Event Subscriber Tests

- [x] 1.1 Create `tests/Unit/EventSubscriber/Doctrine/ReviewAutoApprovalSubscriberTest.php` with tests: store review auto-approved, store review not auto-approved, product review auto-approved, product review not auto-approved, unrelated entity ignored

## 2. ReviewRequest Event Subscriber Tests

- [x] 2.1 Create `tests/Unit/EventSubscriber/ReviewRequest/CheckEligibilityChecksSubscriberTest.php` with tests: cancels when checks exceed max, no-op when checks equal max, no-op when checks below max, correct subscribed events mapping
- [x] 2.2 Create `tests/Unit/EventSubscriber/ReviewRequest/IncrementEligibilityChecksSubscriberTest.php` with tests: increments counter, correct subscribed events mapping
- [x] 2.3 Create `tests/Unit/EventSubscriber/ReviewRequest/ResetSubscriberTest.php` with tests: clears ineligibility reason and processing error, correct subscribed events mapping
- [x] 2.4 Create `tests/Unit/EventSubscriber/ReviewRequest/UpdateNextEligibilityCheckSubscriberTest.php` with tests: exponential backoff for checks 1 and 2 with default delay, custom initial delay, correct subscribed events mapping

## 3. Verify

- [x] 3.1 Run full unit test suite and confirm all new tests pass
- [x] 3.2 Run PHPStan to confirm no static analysis errors
