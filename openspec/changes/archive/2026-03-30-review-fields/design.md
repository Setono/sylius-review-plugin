## Context

The plugin currently includes a `title` field on both store and product review forms, mirroring Sylius core's review model. The field adds unnecessary friction — most modern review UIs use only rating + comment. The `title` column is already nullable in both Sylius core's ORM mapping and the plugin's `StoreReview` ORM mapping, so no database migration is needed.

The `comment` field is currently optional everywhere. For store reviews (the primary feedback channel), comment should be required. For product reviews (where a star rating alone is useful), comment stays optional.

## Goals / Non-Goals

**Goals:**
- Remove `title` from all form types (shop and admin)
- Remove `title` from all Twig templates (shop display, shop forms, admin forms, email)
- Remove `title` validation constraints from `StoreReview.xml`
- Make `comment` required for store reviews (both form `required` attribute and existing `NotBlank` validation)
- Update all affected tests

**Non-Goals:**
- Database migration (column stays, nullable)
- Overriding Sylius core's product review admin form
- Backwards compatibility for removed `title` field display
- Changing Sylius core's `Review` model or validation

## Decisions

### 1. Remove `title` field from form types entirely (not just templates)

**Decision:** Remove the `->add('title', ...)` call from `StoreReviewType`, `ProductReviewType`, and `StoreReviewAdminType`.

**Rationale:** An invisible form field that accepts data nobody submits is dead code. Removing from the form type is cleaner than hiding it in templates. The getter/setter on the model stays — existing data is accessible programmatically.

**Alternative considered:** Hide the field in templates only — rejected because it leaves unnecessary form processing overhead and a confusing API surface.

### 2. Keep `NotBlank` validation on `comment` for store reviews, no validation changes for product reviews

**Decision:** Keep the existing `NotBlank` constraint on `comment` in `StoreReview.xml`. Set `required: true` on the `comment` field in both `StoreReviewType` and `StoreReviewAdminType`. No validation changes needed for product reviews.

**Rationale:** The server-side validation already enforces `NotBlank` on store review comments. Adding `required: true` to the form gives HTML5 client-side enforcement too. Product reviews have no plugin-side validation (Sylius core's constraints are in the `sylius` group, which doesn't fire), so they're already optional.

### 3. Handle existing `title` in product review display template

**Decision:** Remove the `review.title` display from `_single.html.twig`. Old reviews with titles will lose that heading in the UI.

**Rationale:** Consistent with the no-backwards-compat decision. The data remains in the database if needed via custom templates.

## Risks / Trade-offs

- **Lost display data for existing reviews** — Old reviews with titles won't show them anymore. Acceptable per the no-backwards-compat decision. Users who need this can override the `_single.html.twig` template.
- **Sylius core admin still shows title for product reviews** — The Sylius admin product review form is out of scope. Admins will still see the `title` field when editing product reviews through Sylius's built-in admin. This is a Sylius core concern, not a plugin concern.
