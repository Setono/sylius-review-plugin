## 1. Create CSS asset file

- [x] 1.1 Create `src/Resources/public/css/review.css` with the rating-choices styles extracted from the inline `<style>` block

## 2. Extract partials from index.html.twig

- [x] 2.1 Create `_header.html.twig` — page title, order number, introduction text
- [x] 2.2 Create `_not_reviewable.html.twig` — error message for non-reviewable orders
- [x] 2.3 Create `_errors.html.twig` — form-level and store review validation errors
- [x] 2.4 Create `_display_name.html.twig` — display name field (conditional on `form.displayName`)
- [x] 2.5 Create `_store_review.html.twig` — store review section heading, description, rating/title/comment fields
- [x] 2.6 Create `_product_review_item.html.twig` — single product image, name, variant, and review form fields
- [x] 2.7 Create `_product_reviews.html.twig` — section heading, description, loop over order items including `_product_review_item`
- [x] 2.8 Create `_footer.html.twig` — disclaimer text and submit button

## 3. Refactor main template

- [x] 3.1 Rewrite `index.html.twig` as an orchestrator that includes all partials and references the CSS asset via `{% block stylesheets %}`

## 4. Verify

- [x] 4.1 Install assets in the test application (`bin/console assets:install`)
- [x] 4.2 Visually verify the review page renders identically in the browser
