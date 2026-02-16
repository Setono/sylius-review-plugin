## Why

The `AverageRatingCalculator` and `CachedAverageRatingCalculator` classes in `src/Calculator/` have no unit tests. These are core components in the decoration chain that replace Sylius's default rating calculator. Tests are needed to verify fallback behavior, cache logic, and the database query path.

## What Changes

- Add a **functional test** for `AverageRatingCalculator` using a real database:
  - Tests the AVG query against real Doctrine entities loaded via Sylius fixtures
  - Verifies correct average rating computation with accepted reviews
  - Uses `WebTestCase` pattern with DAMA transaction rollback
- Add **unit tests** for `CachedAverageRatingCalculator` covering:
  - Fallback to decorated calculator when reviewable is not a `ResourceInterface`
  - Fallback when reviewable has no ID
  - Cache hit and cache miss scenarios
  - Cache key generation

## Capabilities

### New Capabilities

- `calculator-tests`: Functional test for `AverageRatingCalculator` and unit tests for `CachedAverageRatingCalculator`

### Modified Capabilities

_(none)_

## Impact

- New functional test file in `tests/Functional/Calculator/`
- New unit test file in `tests/Unit/Calculator/`
- No changes to production code
