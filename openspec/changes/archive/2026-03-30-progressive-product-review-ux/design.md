## Context

The review page renders a flat list of product reviews, each with a star rating widget and a comment textarea fully visible. The backend already silently skips products with no rating on submission (ReviewController line 81), but the UI gives no visual signal that individual product reviews are optional. See proposal.md for full motivation.

The star rating initialization happens in an inline `<script>` block in `index.html.twig` using jQuery + Semantic UI's `$.rating()`. Each widget has a `data-rating-target` attribute linking it to its hidden radio group. The `onRate` callback syncs star clicks to radio buttons.

## Goals / Non-Goals

**Goals:**
- Make it visually clear that product reviews are optional by hiding comment fields until a rating is selected
- Support un-rating (deselecting stars) to hide the comment field again
- Preserve the existing behavior for pre-populated reviews (editing existing reviews)
- Update translation copy to reinforce optionality

**Non-Goals:**
- Changing the store review section (it remains always visible)
- Changing form types, validation, or controller logic
- Adding accordion/tab/checkbox UI — this is purely progressive disclosure via show/hide
- Supporting non-jQuery or non-Semantic-UI approaches

## Decisions

### 1. Hide comment via inline style, reveal via JS

The comment field wrapper in `_product_review_item.html.twig` will have `style="display: none"` by default. The `onRate` JS callback will toggle visibility.

**Why over CSS class toggle:** Inline `display: none` ensures the field is hidden even before JS initializes, preventing a flash of visible-then-hidden content. A CSS class would work too, but adds a stylesheet dependency for a single rule.

**Why over a separate CSS file:** This is a single `display` toggle. Adding a CSS asset for one rule isn't worth the complexity.

### 2. Wrap comment field in a targetable container

Each product's comment field will be wrapped in a `<div>` with a predictable ID derived from the form field ID (e.g., `id="{{ form.productReviews[index].comment.vars.id }}_wrapper"`). The JS uses this to find and toggle the comment.

**Why ID-based over DOM traversal:** The star widget already uses `data-rating-target` to find its radio group by ID. Following the same pattern for the comment wrapper is consistent and doesn't break if the template structure changes.

### 3. Extend existing `onRate` callback

The current `onRate` in `index.html.twig` syncs stars → radio buttons. We'll extend it to also toggle comment visibility. A new `data-comment-target` attribute on each `.star.rating` div will point to the comment wrapper ID.

**Why a data attribute over convention:** Explicit is better than implicit. Deriving the comment wrapper ID from the rating target ID would create a fragile naming convention.

### 4. Semantic UI `clearable: true` for un-rating

Semantic UI's `.rating()` supports `clearable: true`, which allows clicking the current rating to reset to 0. This enables customers to undo a rating, which hides the comment field and clears its textarea value.

**Why clear the textarea on unrate:** If a customer rates a product, types a comment, then unrates, the comment should not silently persist. Clearing it prevents confusion if they later re-rate and see stale text (debatable, but cleaner UX).

### 5. `fireOnInit: true` handles pre-existing ratings

The existing `fireOnInit: true` option already fires `onRate` on page load with the initial value. For pre-existing reviews (rating > 0), this will automatically show the comment field. For new reviews (rating 0), the callback will keep it hidden. No special initialization logic needed.

## Risks / Trade-offs

- **[Semantic UI clearable support]** → Verify that `clearable: true` works with the version of Semantic UI bundled with Sylius. If not, we can intercept the click manually, but this is unlikely to be an issue.
- **[Comment data loss on unrate]** → Clearing the textarea when unrating could lose typed text. Mitigation: this is an explicit user action (deliberately clicking to remove their rating), so losing the comment is expected behavior.
- **[No animation]** → The show/hide is instant (`display: none` toggle). A slide or fade would be smoother but adds complexity. Can be enhanced later if desired.
