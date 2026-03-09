## Why

The review page currently renders rating fields as vertically stacked radio buttons with Semantic UI toggle styling, which looks unpolished compared to the interactive star rating widget used on Sylius's native product review page. The Semantic UI rating component is already loaded in the shop JS bundle, so we can reuse it with minimal effort.

## What Changes

- Add a `<div class="ui huge star rating">` visual widget above each hidden rating radio group (store review + each product review)
- Add a small JS file (`src/Resources/public/js/review.js`) that generically initializes all `.star.rating` elements on the review page, syncing star clicks to the corresponding hidden radio buttons
- Update templates to hide the native radio buttons and show the star widget instead
- Include the JS file in the review page's `javascripts` block

## Capabilities

### New Capabilities
- `star-rating-widget`: Interactive star rating UI component using Semantic UI's rating module, with JS glue that syncs to hidden form radio buttons

### Modified Capabilities
- `review-template-partials`: The `_store_review.html.twig` and `_product_review_item.html.twig` partials need to render the star widget div and hide the native radio group

## Impact

- **Templates**: `_store_review.html.twig`, `_product_review_item.html.twig`, `index.html.twig` (JS include)
- **New files**: `src/Resources/public/js/review.js`
- **Dependencies**: None new — Semantic UI rating component is already bundled in Sylius's shop JS
