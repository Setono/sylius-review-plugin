## Why

The `title` field on reviews adds friction to the review submission form without providing meaningful value. Most e-commerce review systems don't require titles — a rating and optional comment are sufficient. Additionally, the `comment` field should be required for store reviews (the store review is the primary feedback mechanism) but remain optional for product reviews (where a star rating alone is useful).

## What Changes

- **Remove `title` field** from all shop-facing review forms (store review and product review)
- **Remove `title` field** from the admin store review form
- **Remove `title` display** from all templates (shop product review listing, admin forms, email templates)
- **Remove `title` validation** constraints from `StoreReview.xml`
- **Make `comment` required** for store reviews in both shop and admin forms (HTML `required` attribute + existing server-side `NotBlank` validation)
- Database column remains nullable — no migration needed, existing data is preserved but not displayed

## Capabilities

### New Capabilities

_None_

### Modified Capabilities

- `form-type-tests`: Form type tests need updating to remove title field assertions and add comment-required assertions
- `review-template-partials`: Shop review templates lose the title field/display
- `review-controller-test`: Controller functional tests may need updated form data (no title submission)
- `admin-store-review-crud-tests`: Admin store review form tests need updating for removed title and required comment
- `store-reply-notification`: Email template loses the title display

## Impact

- **Form types**: `StoreReviewType`, `ProductReviewType`, `StoreReviewAdminType` — field removal and required change
- **Validation**: `StoreReview.xml` — title constraints removed
- **Templates**: 5 Twig files affected (shop forms, shop display, admin form, email)
- **Tests**: Form type tests, controller tests, and admin CRUD tests need updates
- **No breaking API changes**: The database column stays, only UI/form presentation changes
