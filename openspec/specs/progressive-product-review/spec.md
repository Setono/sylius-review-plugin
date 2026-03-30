### Requirement: Product review comment fields are hidden by default

Each product review item on the review page SHALL render its comment textarea hidden (`display: none`) by default. The comment field SHALL only become visible after the customer selects a star rating for that product.

#### Scenario: Page loads with no existing reviews
- **WHEN** the review page loads for an order with 3 products and no existing reviews
- **THEN** all 3 product review comment textareas SHALL be hidden
- **AND** all 3 star rating widgets SHALL be visible with 0 stars selected

#### Scenario: Page loads with an existing product review
- **WHEN** the review page loads and a product already has a saved review with rating 4
- **THEN** that product's comment textarea SHALL be visible
- **AND** that product's star widget SHALL display 4 filled stars

### Requirement: Selecting a star rating reveals the comment field

When a customer clicks a star rating for a product review, the comment textarea for that product SHALL become visible.

#### Scenario: Customer rates a product
- **WHEN** the customer clicks the 3rd star on Product A's rating widget
- **THEN** Product A's comment textarea SHALL become visible
- **AND** Product B's and Product C's comment textareas SHALL remain hidden (unchanged)

#### Scenario: Customer changes an existing rating
- **WHEN** Product A already has a rating of 3 and the customer clicks the 5th star
- **THEN** Product A's comment textarea SHALL remain visible

### Requirement: Clearing a star rating hides and clears the comment field

When a customer clears a star rating (clicks the currently selected star to deselect), the comment textarea SHALL be hidden and its value SHALL be cleared.

#### Scenario: Customer clears a rating with no comment
- **WHEN** the customer has rated Product A with 3 stars and no comment
- **AND** the customer clicks the 3rd star again to clear the rating
- **THEN** Product A's comment textarea SHALL be hidden
- **AND** Product A's rating radio buttons SHALL all be unchecked

#### Scenario: Customer clears a rating that has a comment
- **WHEN** the customer has rated Product A with 4 stars and typed a comment
- **AND** the customer clicks the 4th star again to clear the rating
- **THEN** Product A's comment textarea SHALL be hidden
- **AND** Product A's comment textarea value SHALL be cleared (empty string)
- **AND** Product A's rating radio buttons SHALL all be unchecked

### Requirement: Comment field wrapper uses a predictable ID

Each product review's comment field SHALL be wrapped in a container element with an ID that can be targeted by JavaScript. The star rating widget SHALL have a `data-comment-target` attribute pointing to this wrapper ID.

#### Scenario: DOM structure for a product review
- **WHEN** the review page renders a product review at index 2
- **THEN** the comment wrapper SHALL have an `id` attribute
- **AND** the corresponding star widget SHALL have a `data-comment-target` attribute matching that ID

### Requirement: Product reviews description signals optionality

The `product_reviews_description` translation key SHALL communicate that reviewing individual products is optional.

#### Scenario: English translation
- **WHEN** the review page renders in English
- **THEN** the product reviews section description SHALL indicate that product reviews are optional (e.g., "Rate the products you'd like to review")
