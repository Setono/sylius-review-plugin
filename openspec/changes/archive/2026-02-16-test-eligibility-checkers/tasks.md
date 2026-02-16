## 1. EligibilityCheck Value Object Tests

- [x] 1.1 Create `tests/Unit/EligibilityChecker/EligibilityCheckTest.php` with test for `eligible()` factory method (returns eligible=true, reason=null)
- [x] 1.2 Add test for `ineligible()` factory method (returns eligible=false with provided reason)

## 2. CompositeReviewRequestEligibilityChecker Tests

- [x] 2.1 Create `tests/Unit/EligibilityChecker/CompositeReviewRequestEligibilityCheckerTest.php` with test for empty checkers (returns eligible)
- [x] 2.2 Add test for all checkers returning eligible
- [x] 2.3 Add test for first checker returning ineligible (short-circuits, subsequent checkers not called)
- [x] 2.4 Add test for second checker returning ineligible (first eligible, second ineligible)

## 3. OrderFulfilledReviewRequestEligibilityChecker Tests

- [x] 3.1 Create `tests/Unit/EligibilityChecker/OrderFulfilledReviewRequestEligibilityCheckerTest.php` with test for fulfilled order (returns eligible)
- [x] 3.2 Add test for non-fulfilled order state (returns ineligible with reason)
- [x] 3.3 Add test for null order (returns ineligible with reason)
