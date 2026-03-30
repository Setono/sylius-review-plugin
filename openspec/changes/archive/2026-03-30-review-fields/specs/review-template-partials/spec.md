## MODIFIED Requirements

### Requirement: Store review partial contains store review form fields

The `_store_review.html.twig` partial SHALL contain the store review section heading, description, and form fields (rating, comment). The `title` field SHALL NOT be rendered. The rating field SHALL be rendered as a Semantic UI star rating widget with the native radio group hidden. The star widget div SHALL have a `data-rating-target` attribute pointing to the ID of the hidden radio group wrapper.

#### Scenario: Store review section renders
- **WHEN** the store review partial is rendered
- **THEN** it SHALL display the section heading, description text, and comment form field
- **AND** the rating field SHALL display a `<div class="ui huge star rating">` widget with `data-max-rating="5"`
- **AND** the native radio button group SHALL be wrapped in a div with `style="display: none"`
- **AND** the star widget SHALL have a `data-rating-target` attribute matching the ID of the hidden radio group wrapper
- **AND** there SHALL be no `title` form field rendered

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

### Requirement: Product review display omits title

The `SyliusShopBundle/ProductReview/_single.html.twig` template SHALL NOT render the review title. It SHALL display the reviewer's display name, rating, date, and comment.

#### Scenario: Product review renders without title
- **WHEN** a product review is displayed
- **THEN** the template SHALL show the display name, star rating, date, and comment
- **AND** there SHALL be no `review.title` output in the rendered HTML
