## MODIFIED Requirements

### Requirement: Store review partial contains store review form fields

The `_store_review.html.twig` partial SHALL contain the store review section heading, description, and form fields (rating, title, comment). The rating field SHALL be rendered as a Semantic UI star rating widget with the native radio group hidden. The star widget div SHALL have a `data-rating-target` attribute pointing to the ID of the hidden radio group wrapper.

#### Scenario: Store review section renders
- **WHEN** the store review partial is rendered
- **THEN** it SHALL display the section heading, description text, and title/comment form fields
- **AND** the rating field SHALL display a `<div class="ui huge star rating">` widget with `data-max-rating="5"`
- **AND** the native radio button group SHALL be wrapped in a div with `style="display: none"`
- **AND** the star widget SHALL have a `data-rating-target` attribute matching the ID of the hidden radio group wrapper

### Requirement: Product review item partial contains single product review

The `_product_review_item.html.twig` partial SHALL render a single product's image, name, variant name, and review form fields (rating, title, comment). The rating field SHALL be rendered as a Semantic UI star rating widget with the native radio group hidden. The star widget div SHALL have a `data-rating-target` attribute pointing to the ID of the hidden radio group wrapper.

#### Scenario: Product with image and variant
- **WHEN** a product has an image and the item has a variant name
- **THEN** the partial SHALL display the product image, product name, variant name, and review form fields
- **AND** the rating field SHALL display a `<div class="ui huge star rating">` widget with `data-max-rating="5"`
- **AND** the native radio button group SHALL be wrapped in a div with `style="display: none"`

#### Scenario: Product without image
- **WHEN** a product has no images
- **THEN** the partial SHALL omit the image but still display the product name and review form fields
- **AND** the rating field SHALL display a star rating widget

### Requirement: Rendered HTML output is unchanged

The review page SHALL render the same functional HTML as before, except that rating fields now display as star widgets instead of visible radio buttons. The form submission behavior SHALL remain identical.

#### Scenario: Before and after comparison
- **WHEN** the review page is rendered after the change
- **THEN** the HTML structure, classes, and content SHALL match the previous template output
- **AND** the only differences SHALL be: star widget divs added, radio groups hidden, inline script added in javascripts block
