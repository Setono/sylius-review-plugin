### Requirement: Review form template uses include-based partials

The main review form template (`shop/review/index.html.twig`) SHALL compose its content from `{% include %}` partials rather than containing all markup inline. Each partial SHALL be a separate Twig file in the same directory, prefixed with `_`.

#### Scenario: Main template includes all partials
- **WHEN** the review page is rendered
- **THEN** `index.html.twig` SHALL include the following partials via `{% include %}`:
  - `_header.html.twig`
  - `_not_reviewable.html.twig` (conditional: only when order is not reviewable)
  - `_errors.html.twig`
  - `_display_name.html.twig`
  - `_store_review.html.twig`
  - `_product_reviews.html.twig`
  - `_footer.html.twig`

#### Scenario: Product reviews partial includes product review item partial
- **WHEN** the product reviews section is rendered
- **THEN** `_product_reviews.html.twig` SHALL include `_product_review_item.html.twig` for each order item with a product

### Requirement: Each partial is independently overridable

Plugin users SHALL be able to override any individual partial by placing a file at `templates/bundles/SetonoSyliusReviewPlugin/shop/review/<partial_name>.html.twig` in their Symfony application.

#### Scenario: User overrides only the store review partial
- **WHEN** a user creates `templates/bundles/SetonoSyliusReviewPlugin/shop/review/_store_review.html.twig`
- **THEN** only the store review section uses the user's template
- **AND** all other sections render from the plugin's default partials

#### Scenario: User overrides the product review item partial
- **WHEN** a user creates `templates/bundles/SetonoSyliusReviewPlugin/shop/review/_product_review_item.html.twig`
- **THEN** every product in the review form uses the user's item template
- **AND** the product reviews section header and loop logic remain unchanged

### Requirement: Partials receive full template context

All partials SHALL receive the full parent template context (including `form`, `order`, and `reviewableCheck`) via Twig's default context passing. Partials SHALL NOT use the `only` keyword.

#### Scenario: Partial accesses form and order variables
- **WHEN** any partial is rendered
- **THEN** the partial SHALL have access to `form`, `order`, and `reviewableCheck` variables without explicit `with` declarations

### Requirement: Header partial contains page title and introduction

The `_header.html.twig` partial SHALL contain the page heading (with order number) and introduction text.

#### Scenario: Header renders order information
- **WHEN** the header partial is rendered
- **THEN** it SHALL display the translated review page title and the order number

### Requirement: Not-reviewable partial contains error message

The `_not_reviewable.html.twig` partial SHALL display the reason an order cannot be reviewed.

#### Scenario: Order is not reviewable
- **WHEN** `reviewableCheck.reviewable` is false
- **THEN** the not-reviewable partial SHALL display `reviewableCheck.reason` as a translated error message

### Requirement: Errors partial contains form validation errors

The `_errors.html.twig` partial SHALL display form-level and store review validation errors.

#### Scenario: Form has validation errors
- **WHEN** `form.vars.errors` or `form.storeReview.vars.errors` contain errors
- **THEN** the errors partial SHALL display all error messages in a list

#### Scenario: No validation errors
- **WHEN** no form-level errors exist
- **THEN** the errors partial SHALL render nothing

### Requirement: Display name partial contains display name field

The `_display_name.html.twig` partial SHALL render the display name form field when it exists.

#### Scenario: Display name field is defined
- **WHEN** `form.displayName` is defined
- **THEN** the partial SHALL render the display name form row

#### Scenario: Display name field is not defined
- **WHEN** `form.displayName` is not defined
- **THEN** the partial SHALL render nothing

### Requirement: Store review partial contains store review form fields

The `_store_review.html.twig` partial SHALL contain the store review section heading, description, and form fields (rating, comment). The `title` field SHALL NOT be rendered. The rating field SHALL be rendered as a Semantic UI star rating widget with the native radio group hidden. The star widget div SHALL have a `data-rating-target` attribute pointing to the ID of the hidden radio group wrapper.

#### Scenario: Store review section renders
- **WHEN** the store review partial is rendered
- **THEN** it SHALL display the section heading, description text, and comment form field
- **AND** the rating field SHALL display a `<div class="ui huge star rating">` widget with `data-max-rating="5"`
- **AND** the native radio button group SHALL be wrapped in a div with `style="display: none"`
- **AND** the star widget SHALL have a `data-rating-target` attribute matching the ID of the hidden radio group wrapper
- **AND** there SHALL be no `title` form field rendered

### Requirement: Product reviews partial contains loop and section header

The `_product_reviews.html.twig` partial SHALL contain the product reviews section heading, description, and the loop over order items. For each item with a product, it SHALL include `_product_review_item.html.twig`.

#### Scenario: Multiple products in order
- **WHEN** the order has multiple items with products
- **THEN** the partial SHALL include `_product_review_item.html.twig` for each product item

### Requirement: Product review item partial contains single product review

The `_product_review_item.html.twig` partial SHALL render a single product's image, name, variant name, and review form fields (rating, comment). The `title` field SHALL NOT be rendered. The rating field SHALL be rendered as a Semantic UI star rating widget with the native radio group hidden. The star widget div SHALL have a `data-rating-target` attribute pointing to the ID of the hidden radio group wrapper.

#### Scenario: Product with image and variant
- **WHEN** a product has an image and the item has a variant name
- **THEN** the partial SHALL display the product image, product name, variant name, and review form fields (rating, comment)
- **AND** the rating field SHALL display a `<div class="ui huge star rating">` widget with `data-max-rating="5"`
- **AND** the native radio button group SHALL be wrapped in a div with `style="display: none"`
- **AND** there SHALL be no `title` form field rendered

#### Scenario: Product without image
- **WHEN** a product has no images
- **THEN** the partial SHALL omit the image but still display the product name and review form fields (rating, comment)
- **AND** the rating field SHALL display a star rating widget

### Requirement: Footer partial contains disclaimer and submit button

The `_footer.html.twig` partial SHALL contain the review disclaimer text and the submit button.

#### Scenario: Footer renders
- **WHEN** the footer partial is rendered
- **THEN** it SHALL display the translated disclaimer text and a submit button

### Requirement: Rendered HTML output is unchanged

The review page SHALL render the same functional HTML as before, except that rating fields now display as star widgets instead of visible radio buttons. The form submission behavior SHALL remain identical.

#### Scenario: Before and after comparison
- **WHEN** the review page is rendered after the change
- **THEN** the HTML structure, classes, and content SHALL match the previous template output
- **AND** the only differences SHALL be: star widget divs added, radio groups hidden, inline script added in javascripts block
