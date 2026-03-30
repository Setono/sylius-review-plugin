## MODIFIED Requirements

### Requirement: Email template for reply notification
A Twig email template SHALL render the notification with the store/channel name (for store reviews) or product name (for product reviews), the customer's original review (rating, comment), and the store's reply text rendered as HTML from markdown. The template SHALL NOT display the review title.

#### Scenario: Template renders for store review
- **WHEN** the email is rendered for a store review reply
- **THEN** the intro text SHALL include the store/channel name (not hardcoded "The store")
- **AND** it SHALL include the review rating and comment
- **AND** it SHALL NOT include the review title
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter

#### Scenario: Template renders for product review
- **WHEN** the email is rendered for a product review reply
- **THEN** the intro text SHALL include the store/channel name and the product name
- **AND** it SHALL include the review rating and comment
- **AND** it SHALL NOT include the review title
- **AND** the store reply SHALL be converted from markdown to HTML using the `markdown_to_html` filter
