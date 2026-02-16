## 1. ReviewRequestRepository Tests

- [x] 1.1 Create `tests/Functional/Repository/ReviewRequestRepositoryTest.php` with tests: createForProcessingQueryBuilder (pending+past returned, pending+future excluded, completed excluded, cancelled excluded), removeBefore (before removed, after kept), removeCancelled (cancelled removed, pending kept), hasExistingForOrder (true when exists, false when not)

## 2. StoreReviewRepository Tests

- [x] 2.1 Create `tests/Functional/Repository/StoreReviewRepositoryTest.php` with tests: findOneByOrder (returns review when exists, returns null when not)

## 3. Verify

- [x] 3.1 Run functional test suite and confirm all new tests pass
- [x] 3.2 Run PHPStan to confirm no static analysis errors
