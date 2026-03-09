## Context

The review page currently renders rating fields as vertically stacked Semantic UI toggle radio buttons. Sylius's native product review form uses Semantic UI's `.star.rating` widget for an interactive star-based experience. The Semantic UI rating JS component is already loaded in the shop bundle (`semantic-ui-css/components/rating`), so no new dependencies are needed.

Sylius's own implementation (in `ShopBundle/Resources/views/ProductReview/_form.html.twig` and `ShopBundle/Resources/private/js/app.js`) uses a hardcoded selector (`sylius_product_review[rating]`) that doesn't work for our form field names (`review[storeReview][rating]`, `review[productReviews][N][rating]`).

## Goals / Non-Goals

**Goals:**
- Replace raw radio button rating UI with interactive Semantic UI star rating widgets
- Work generically for both store review and all product review rating fields
- Sync star selection to the existing hidden radio button form inputs
- Support pre-selected ratings (e.g., when editing an existing review)

**Non-Goals:**
- Customizing star appearance beyond Semantic UI defaults
- Adding half-star ratings
- Changing the form type or backend logic — only the visual presentation changes

## Decisions

### 1. Generic JS initialization via data attributes

**Decision**: Each rating widget container will have a `data-rating-target` attribute pointing to the ID of the corresponding radio group wrapper. The JS will find each `.star.rating` element and wire its `onRate` callback to the matching radio group.

**Rationale**: This avoids hardcoding form field names (unlike Sylius's approach) and works for any number of rating fields on the page. It also means the JS doesn't need to understand the form structure — just the pairing between star widget and radio group.

**Alternative considered**: Using DOM proximity (e.g., finding the next sibling radio group). Rejected because it's fragile if template structure changes.

### 2. Hide radio group via `display: none` style attribute in Twig

**Decision**: The radio button widget div will get `style="display: none"` directly in the template, matching Sylius's approach.

**Rationale**: Simple, matches Sylius convention. The hidden radios still participate in form submission. No CSS file changes needed.

### 3. Inline `<script>` in templates instead of separate JS file

**Decision**: Use a small inline `<script>` in `index.html.twig` instead of a separate JS file.

**Rationale**: The JS is ~15 lines. A separate file would require asset installation (`assets:install`) and adds deployment complexity for minimal code. Inline keeps it self-contained and the review page is the only consumer. We already have Semantic UI and jQuery available from the shop bundle.

**Alternative considered**: Separate `src/Resources/public/js/review.js` file. Would be cleaner for larger amounts of JS, but overkill for this amount of code.

### 4. Use `fireOnInit: true` to support pre-selected ratings

**Decision**: Initialize the Semantic UI rating with `fireOnInit: true` so that if the `data-rating` attribute has a non-zero value (from an existing review), the corresponding radio button gets checked on page load.

**Rationale**: Matches Sylius's approach. Ensures round-trip editing works correctly.

## Risks / Trade-offs

- **[Risk] jQuery/Semantic UI not loaded** → The shop layout already includes these. If a store removes them, stars won't work but the hidden radios remain functional as a fallback.
- **[Risk] Form field ID structure changes** → Using `data-rating-target` attribute makes the pairing explicit rather than relying on DOM structure, reducing this risk.
- **[Trade-off] Inline script vs. separate file** → Less "clean" architecturally, but pragmatic for ~15 lines of JS.
