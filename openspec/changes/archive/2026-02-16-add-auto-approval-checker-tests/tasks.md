## 1. CompositeAutoApprovalChecker Tests

- [x] 1.1 Create `tests/Unit/Checker/AutoApproval/CompositeAutoApprovalCheckerTest.php` with test: it approves when no checkers are registered
- [x] 1.2 Add test: it approves when all checkers approve
- [x] 1.3 Add test: it rejects on first failing checker and does not call subsequent checkers
- [x] 1.4 Add test: it rejects from second checker when first approves

## 2. MinimumRatingAutoApprovalChecker Tests

- [x] 2.1 Create `tests/Unit/Checker/AutoApproval/MinimumRatingAutoApprovalCheckerTest.php` with test: it approves when rating equals default threshold
- [x] 2.2 Add test: it approves when rating exceeds default threshold
- [x] 2.3 Add test: it rejects when rating is below default threshold
- [x] 2.4 Add test: it approves at boundary with custom threshold
- [x] 2.5 Add test: it rejects when rating is null (treated as zero)

## 3. Verification

- [x] 3.1 Run unit tests and confirm all pass
- [x] 3.2 Run PHPStan and confirm no static analysis errors
