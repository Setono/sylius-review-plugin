## Why

The shop review template (`shop/review/index.html.twig`) is a single monolithic file (~120 lines). Plugin users who want to customize any part of the review form (e.g., hide the store review section, change the product image rendering, add a consent checkbox) must copy the entire template and maintain it through upgrades. Breaking it into `{% include %}` partials lets users override only the specific section they need.

## What Changes

- Extract the review form template into composable partials (`_header`, `_not_reviewable`, `_errors`, `_display_name`, `_store_review`, `_product_reviews`, `_product_review_item`, `_footer`)
- Move inline `<style>` CSS to a proper CSS file at `src/Resources/public/css/review.css`, referenced via the `{% block stylesheets %}` block
- The main `index.html.twig` becomes a thin orchestrator that includes the partials

## Capabilities

### New Capabilities

- `review-template-partials`: Decomposition of the shop review form template into independently overridable `{% include %}` partials and extraction of inline CSS to a static asset file

### Modified Capabilities

None — this is a pure refactor with no behavioral changes to existing specs.

## Impact

- **Templates**: `src/Resources/views/shop/review/index.html.twig` is refactored; 8 new partial templates created in the same directory
- **Assets**: New `src/Resources/public/css/review.css` file; requires `bin/console assets:install` after upgrade
- **No breaking changes**: The rendered HTML output remains identical. Plugin users who have already overridden `index.html.twig` in `templates/bundles/` are unaffected — their override still takes precedence
