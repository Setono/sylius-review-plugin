## ADDED Requirements

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
- **AND** a POST request is made to `/en_US/review?token={order_token}` with valid review form data (including a store review rating)
- **THEN** the response SHALL be a redirect to the review page
- **AND** following the redirect, the response SHALL contain the success flash message
