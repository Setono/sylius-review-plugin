## Why

The `ReviewController` is the customer-facing entry point for submitting store and product reviews, yet it has no functional test coverage. It handles token validation, order lookup, reviewability checks, form rendering, and persistence — all of which should be verified through HTTP-level tests to catch regressions in routing, form handling, and the full request/response cycle.

## What Changes

- Install `dama/doctrine-test-bundle` as a dev dependency for automatic transaction rollback between tests
- Register the bundle in the test application (`tests/Application/config/bundles.php`)
- Add the DAMA PHPUnit extension to `phpunit.xml.dist`
- Add a functional test class `tests/Controller/ReviewControllerTest.php` covering:
  - Missing token returns 404
  - Invalid/unknown token returns 404
  - Non-reviewable order (e.g., state `new`) shows error page
  - Fulfilled order renders the review form (GET)
  - Valid form submission persists reviews and redirects (POST)

## Capabilities

### New Capabilities

- `review-controller-test`: Functional test coverage for the customer-facing review controller, including test infrastructure setup (doctrine-test-bundle)

### Modified Capabilities

(none)

## Impact

- **New dev dependency**: `dama/doctrine-test-bundle`
- **PHPUnit config**: `phpunit.xml.dist` gains the DAMA extension
- **Test app config**: New bundle registration in `bundles.php`
- **Test suite**: New test file at `tests/Controller/ReviewControllerTest.php`
- **CI assumption**: Test database must have fixtures loaded before running the test suite
