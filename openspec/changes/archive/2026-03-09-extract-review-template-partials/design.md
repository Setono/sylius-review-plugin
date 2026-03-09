## Context

The shop review form template (`src/Resources/views/shop/review/index.html.twig`) is a single 120-line file containing all sections inline: header, error display, display name, store review, product reviews loop, disclaimer, submit button, and inline CSS. Plugin users must override the entire file to change any part. Sylius plugins commonly use `{% include %}` partials so users can override individual sections via `templates/bundles/<BundleName>/...`.

## Goals / Non-Goals

**Goals:**
- Break the monolithic template into independently overridable `{% include %}` partials
- Move inline CSS to a static asset file in `src/Resources/public/`
- Maintain identical rendered HTML output (no visual or behavioral changes)

**Non-Goals:**
- Adding `sylius_template_event` hooks (decided against — too heavy for this plugin's needs)
- Changing the form structure or adding new form fields
- Restyling or redesigning the review form UI
- Adding Twig blocks within the main template (Symfony bundle overrides replace templates, they don't extend them)

## Decisions

### 1. Composition via `{% include %}` over Twig blocks

**Decision**: Use `{% include %}` with partials named `_*.html.twig` in the same directory.

**Rationale**: Symfony bundle template overrides replace an entire template file. Twig blocks only help if the user `{% extends %}` the original, but the `bundles/` override mechanism doesn't support that — it replaces. With `{% include %}`, each partial is a separate file that can be independently overridden via `templates/bundles/SetonoSyliusReviewPlugin/shop/review/_store_review.html.twig`.

**Alternatives considered**: Twig blocks (doesn't work with bundle overrides), `sylius_template_event` (too heavy, requires YAML config for each event).

### 2. Partial decomposition boundaries

**Decision**: 8 partials matching logical sections:

| Partial | Content |
|---------|---------|
| `_header.html.twig` | Page title, order number, intro text |
| `_not_reviewable.html.twig` | Error message when order isn't reviewable |
| `_errors.html.twig` | Form-level validation errors |
| `_display_name.html.twig` | Display name selection field |
| `_store_review.html.twig` | Store review: rating, title, comment |
| `_product_reviews.html.twig` | Section header + loop calling `_product_review_item` |
| `_product_review_item.html.twig` | Single product: image, variant name, review fields |
| `_footer.html.twig` | Disclaimer + submit button |

**Rationale**: Each partial maps to a section a user would plausibly want to customize independently. The product review item is extracted from the loop because it's the most likely customization target (product image rendering, layout, extra product info).

### 3. Default Twig context passing

**Decision**: Use `{% include '...' %}` without explicit `with { }` or `only` — Twig passes the full parent context by default.

**Rationale**: All partials need access to `form`, `order`, and/or `reviewableCheck` which are already in scope. Restricting with `only` would require manually listing variables for every include, adding maintenance burden with no real benefit.

### 4. CSS extraction to static asset

**Decision**: Move inline `<style>` to `src/Resources/public/css/review.css`, referenced via `{% block stylesheets %}{{ parent() }}<link ...>{% endblock %}`.

**Rationale**: Inline styles in templates can't be cached by browsers and are harder to override. A static CSS file follows Symfony asset conventions, gets installed to `public/bundles/setonosyliusreviewplugin/css/review.css` via `assets:install`, and can be overridden by the host app's own stylesheets.

## Risks / Trade-offs

- **[More files to navigate]** → 9 files instead of 1. Mitigated by clear naming and the orchestrator pattern making the structure obvious at a glance.
- **[Upgrade path for existing overrides]** → Users who already override `index.html.twig` are unaffected — their full override still takes precedence. No breaking change.
- **[`assets:install` required]** → The new CSS file requires `bin/console assets:install` after upgrading the plugin. This is standard practice for Sylius plugins with public assets and should be documented.
