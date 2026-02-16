## 1. Install and configure doctrine-test-bundle

- [x] 1.1 Run `composer require --dev dama/doctrine-test-bundle`
- [x] 1.2 Register `DAMA\DoctrineTestBundle\DAMADoctrineTestBundle` in `tests/Application/config/bundles.php` for `test` environment only
- [x] 1.3 Add the DAMA PHPUnit extension to `phpunit.xml.dist` inside an `<extensions>` element

## 2. Write ReviewControllerTest

- [x] 2.1 Create `tests/Controller/ReviewControllerTest.php` extending `WebTestCase`
- [x] 2.2 Implement `setUp()` that creates the HTTP client and fetches a fixture order from the database
- [x] 2.3 Implement test: missing token returns 404 (`it_returns_404_when_token_is_missing`)
- [x] 2.4 Implement test: invalid token returns 404 (`it_returns_404_when_order_is_not_found`)
- [x] 2.5 Implement test: non-reviewable order shows error page (`it_shows_error_when_order_is_not_reviewable`)
- [x] 2.6 Implement test: fulfilled order renders review form (`it_renders_review_form_for_fulfilled_order`)
- [x] 2.7 Implement test: valid submission persists and redirects (`it_submits_review_successfully`)

## 3. Verify

- [x] 3.1 Run the full test suite and confirm all tests pass (existing + new)
- [x] 3.2 Run PHPStan and fix any type errors in the new test file
