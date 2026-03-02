## Why

The admin store review CRUD (index, update, delete, accept/reject transitions, bulk delete) was added recently but has no functional test coverage. Tests are needed to verify these routes work correctly and catch regressions.

## What Changes

- Add a functional test class `AdminStoreReviewControllerTest` covering:
  - Index page lists store reviews
  - Empty index page renders correctly
  - Update form renders and submits successfully
  - Store reply can be added via the update form
  - Accept and reject workflow transitions work
  - Single delete works
  - Bulk delete works
  - Unauthenticated access is denied (redirects to login)

## Capabilities

### New Capabilities
- `admin-store-review-crud-tests`: Functional test coverage for all admin store review CRUD routes and workflow transitions

### Modified Capabilities
<!-- None — this is a test-only change, no requirement changes to existing capabilities -->

## Impact

- New file: `tests/Functional/Controller/Admin/StoreReviewControllerTest.php`
- No changes to production code
- No dependency changes
