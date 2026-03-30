## MODIFIED Requirements

### Requirement: Valid form submission persists reviews and redirects

The review endpoint SHALL persist submitted reviews and redirect back to the review page with a success flash message on valid POST submission. The form data SHALL NOT include a `title` field.

#### Scenario: Submit review for fulfilled order
- **WHEN** a fixture order's state is updated to `fulfilled` in the database
- **AND** a POST request is made to `/en_US/review?token={order_token}` with valid review form data (including a store review rating and comment, but no title)
- **THEN** the response SHALL be a redirect to the review page
- **AND** following the redirect, the response SHALL contain the success flash message
