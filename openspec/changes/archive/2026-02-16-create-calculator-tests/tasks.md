## 1. Functional Test for AverageRatingCalculator

- [x] 1.1 Create `tests/Functional/Calculator/AverageRatingCalculatorTest.php` extending `KernelTestCase`
- [x] 1.2 Implement test: product with multiple accepted reviews returns correct average
- [x] 1.3 Implement test: product with no accepted reviews returns 0.0
- [x] 1.4 Implement test: only accepted reviews are included in the average (mix of accepted, new, rejected)

## 2. Unit Tests for CachedAverageRatingCalculator

- [x] 2.1 Create `tests/Unit/Calculator/CachedAverageRatingCalculatorTest.php` with ProphecyTrait
- [x] 2.2 Implement test: falls back to decorated calculator when reviewable is not ResourceInterface
- [x] 2.3 Implement test: falls back to decorated calculator when reviewable has null ID
- [x] 2.4 Implement test: cache miss computes and stores result with correct key format
- [x] 2.5 Implement test: custom cache lifetime is respected

## 3. Verify

- [x] 3.1 Run full test suite and confirm all new tests pass
