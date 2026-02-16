## 1. ReviewableOrderCheck value object tests

- [x] 1.1 Create `tests/Unit/Checker/ReviewableOrder/ReviewableOrderCheckTest.php` with tests for `reviewable()` and `notReviewable()` factory methods

## 2. CompositeReviewableOrderChecker tests

- [x] 2.1 Create `tests/Unit/Checker/ReviewableOrder/CompositeReviewableOrderCheckerTest.php` with tests for: all checkers pass, first checker fails (short-circuit), second checker fails, no checkers registered

## 3. OrderFulfilledReviewableOrderChecker tests

- [x] 3.1 Create `tests/Unit/Checker/ReviewableOrder/OrderFulfilledReviewableOrderCheckerTest.php` with tests for: fulfilled order (default states), non-reviewable order state, custom reviewable states

## 4. StoreReviewEditableReviewableOrderChecker tests

- [x] 4.1 Create `tests/Unit/Checker/ReviewableOrder/StoreReviewEditableReviewableOrderCheckerTest.php` with tests for: no existing review, review within editable period, review past editable period, null editable period, null createdAt on existing review
