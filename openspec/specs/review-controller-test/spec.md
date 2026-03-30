### Requirement: Test isolation via doctrine-test-bundle

The test suite SHALL use `dama/doctrine-test-bundle` to automatically roll back database changes after each test. The bundle SHALL be registered in the test application's `bundles.php` for the `test` environment only. The PHPUnit extension SHALL be registered in `phpunit.xml.dist`.

#### Scenario: Database changes are rolled back between tests
- **WHEN** a test modifies database state (e.g., updates an order's state to `fulfilled`)
- **THEN** the modification is rolled back after the test completes, leaving the database unchanged for the next test

### Requirement: Missing token returns 404

The review endpoint SHALL return a 404 response when no `token` query parameter is provided.

#### Scenario: No token parameter in request
- **WHEN** a GET request is made to `/en_US/review` without a `token` query parameter
- **THEN** the response status code SHALL be 404

### Requirement: Invalid token returns 404

The review endpoint SHALL return a 404 response when the `token` query parameter does not match any existing order.

#### Scenario: Non-existent order token
- **WHEN** a GET request is made to `/en_US/review?token=nonexistent_token_value`
- **THEN** the response status code SHALL be 404

### Requirement: Non-reviewable order shows error page

The review endpoint SHALL render an error page (200 status) when the order exists but is not in a reviewable state, and the page SHALL NOT contain a review form.

#### Scenario: Order in non-fulfilled state
- **WHEN** a GET request is made to `/en_US/review?token={valid_token}` for an order in `new` state
- **THEN** the response status code SHALL be 200
- **AND** the response body SHALL NOT contain a review form

### Requirement: Fulfilled order renders review form

The review endpoint SHALL render the review form when the order is in `fulfilled` state.

#### Scenario: GET request for fulfilled order
- **WHEN** a fixture order's state is updated to `fulfilled` in the database
- **AND** a GET request is made to `/en_US/review?token={order_token}`
- **THEN** the response status code SHALL be 200
- **AND** the response body SHALL contain a review form

### Requirement: Valid form submission persists reviews and redirects

The review endpoint SHALL persist submitted reviews and redirect back to the review page with a success flash message on valid POST submission.

#### Scenario: Submit review for fulfilled order
- **WHEN** a fixture order's state is updated to `fulfilled` in the database
- **AND** a POST request is made to `/en_US/review?token={order_token}` with valid review form data (including a store review rating and comment, but no title)
- **THEN** the response SHALL be a redirect to the review page
- **AND** following the redirect, the response SHALL contain the success flash message

### Requirement: Editing an accepted review resets status to new

The review endpoint SHALL reset the review status to `new` when a customer submits an edit to an existing review. Auto-approval SHALL re-run after the reset.

#### Scenario: Edit accepted store review resets status
- **WHEN** a store review exists for an order with status `accepted`
- **AND** the customer submits the review form with modified data
- **THEN** the store review status SHALL be reset to `new`
- **AND** auto-approval SHALL re-run (status may become `accepted` again if the review passes auto-approval)

#### Scenario: Edit accepted product review resets status
- **WHEN** a product review exists for an order with status `accepted`
- **AND** the customer submits the review form with a modified rating
- **THEN** the product review status SHALL be reset to `new`
- **AND** auto-approval SHALL re-run

#### Scenario: New review submission is unaffected
- **WHEN** no store review exists for an order
- **AND** the customer submits the review form
- **THEN** the review SHALL follow the normal creation flow (status `new`, then auto-approval if configured)
