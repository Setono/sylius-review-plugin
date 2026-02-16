## Context

The project has an existing `ReviewTypeTest` that exercises `StoreReviewType` and `ProductReviewType` indirectly through the composite `ReviewType`. However, neither form type has its own dedicated test class. Both extend `AbstractResourceType` (Sylius), which uses `empty_data` to create entity instances during submission. The user has requested tests using Symfony's `TypeTestCase`.

## Goals / Non-Goals

**Goals:**
- Test `StoreReviewType` in isolation: field presence, POST_SUBMIT listener behavior, required `order` option, and block prefix
- Test `ProductReviewType` in isolation: field presence, data mapping on submission, and block prefix
- Follow existing test conventions: Prophecy for mocking, BDD-style method names, `TypeTestCase` base class

**Non-Goals:**
- Testing `ReviewType` (already covered by `ReviewTypeTest`)
- Testing validation constraints (these live on the model, not the form type)
- Integration/functional testing of the form rendering

## Decisions

### 1. Use `TypeTestCase` with `PreloadedExtension`

Both form types extend `AbstractResourceType`, which requires constructor arguments (`dataClass`, `validationGroups`). We register each type via `PreloadedExtension` in `getExtensions()`, matching the pattern in the existing `ReviewTypeTest`.

**Alternative considered**: Using `KernelTestCase` with the service container. Rejected — heavier, slower, and unnecessary for unit-level form testing.

### 2. Use concrete `StoreReview` entity instead of mocking for `StoreReviewType` tests

The `POST_SUBMIT` listener calls `setOrder()`, `setReviewSubject()`, and `setAuthor()` on the form data. Using a real `StoreReview` instance lets us assert the actual property values after submission. Mocking the entity would be fragile and test the mock rather than the behavior.

`ProductReviewType` has no event listeners, so we'll use a mock for the data class (as `AbstractResourceType`'s `empty_data` creates it).

**Alternative considered**: Mocking `StoreReviewInterface` and verifying method calls with Prophecy. Rejected — the `StoreReview` entity is a simple model with no dependencies; using the real object produces clearer, more meaningful assertions.

### 3. Test POST_SUBMIT listener via form submission

Rather than extracting and unit-testing the listener closure directly, we test it through submitting the form. This tests the actual integration point and matches Symfony's recommended form testing approach.

### 4. Test file organization

Place tests in `tests/Unit/Form/Type/` alongside the existing `ReviewTypeTest.php`:
- `StoreReviewTypeTest.php`
- `ProductReviewTypeTest.php`

## Risks / Trade-offs

- **`AbstractResourceType` `empty_data` coupling**: The `empty_data` callback in `AbstractResourceType` creates entities via `new $dataClass()`. For `StoreReviewType`, the concrete `StoreReview` class works fine. For `ProductReviewType`, Sylius's `ProductReview` class may have constructor requirements — if so, we'll provide a custom `empty_data` in test options or use `PreloadedExtension` configuration.
  → Mitigation: The existing `ReviewTypeTest` already handles this pattern successfully.
