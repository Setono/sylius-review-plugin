## Why

The `StoreReviewType` and `ProductReviewType` form types lack dedicated unit tests. While `ReviewTypeTest` exists and exercises these types indirectly through integration, there are no isolated tests verifying each type's field configuration, options, block prefix, or event listener behavior. This leaves gaps in test coverage for form-level logic.

## What Changes

- Add `StoreReviewTypeTest` extending `TypeTestCase` to verify:
  - Form fields (rating, title, comment) are present with correct types and options
  - `POST_SUBMIT` listener sets order, review subject (channel), and author (customer) on the store review
  - Block prefix is `setono_sylius_review_store_review`
  - The `order` option is required and type-checked
- Add `ProductReviewTypeTest` extending `TypeTestCase` to verify:
  - Form fields (rating, title, comment) are present with correct types and options
  - Block prefix is `setono_sylius_review_product_review`
  - Form submission maps data correctly

## Capabilities

### New Capabilities

- `form-type-tests`: Unit tests for `StoreReviewType` and `ProductReviewType` form types using Symfony's `TypeTestCase`

### Modified Capabilities

(none)

## Impact

- New test files: `tests/Unit/Form/Type/StoreReviewTypeTest.php` and `tests/Unit/Form/Type/ProductReviewTypeTest.php`
- No changes to production code
- No dependency changes
