## ADDED Requirements

### Requirement: Admin store review index page is accessible and lists reviews
The functional test SHALL verify that an authenticated admin can access the store review index page and that store reviews are displayed.

#### Scenario: Admin views store review index with reviews
- **WHEN** an authenticated admin requests GET `/admin/store-reviews/`
- **AND** a store review exists in the database
- **THEN** the response status is 200
- **AND** the page contains the store review's comment (not title)

#### Scenario: Admin views empty store review index
- **WHEN** an authenticated admin requests GET `/admin/store-reviews/`
- **AND** no store reviews exist in the database
- **THEN** the response status is 200

### Requirement: Admin store review update form renders and submits
The functional test SHALL verify that an authenticated admin can view and submit the store review update form. The form SHALL NOT contain a title field.

#### Scenario: Admin views the update form
- **WHEN** an authenticated admin requests GET `/admin/store-reviews/{id}/edit`
- **THEN** the response status is 200
- **AND** the page contains a form

#### Scenario: Admin updates a store review
- **WHEN** an authenticated admin submits the update form with modified comment
- **THEN** the system redirects to the update page
- **AND** the store review is persisted with the new values

#### Scenario: Admin adds a store reply via the update form
- **WHEN** an authenticated admin submits the update form with a store reply value
- **THEN** the store review is persisted with the store reply text

### Requirement: Admin can accept and reject store reviews
The functional test SHALL verify that the accept and reject workflow transitions work via PUT requests.

#### Scenario: Admin accepts a store review
- **WHEN** an authenticated admin sends PUT to `/admin/store-review/{id}/accept` with a valid CSRF token
- **THEN** the system redirects
- **AND** the store review status becomes "accepted"

#### Scenario: Admin rejects a store review
- **WHEN** an authenticated admin sends PUT to `/admin/store-review/{id}/reject` with a valid CSRF token
- **THEN** the system redirects
- **AND** the store review status becomes "rejected"

### Requirement: Admin can delete a store review
The functional test SHALL verify that single delete removes the store review.

#### Scenario: Admin deletes a store review
- **WHEN** an authenticated admin sends DELETE to `/admin/store-reviews/{id}` with a valid CSRF token
- **THEN** the system redirects
- **AND** the store review no longer exists in the database

### Requirement: Admin can bulk delete store reviews
The functional test SHALL verify that bulk delete removes multiple store reviews.

#### Scenario: Admin bulk deletes store reviews
- **WHEN** an authenticated admin sends DELETE to `/admin/store-reviews/bulk-delete` with multiple review IDs and a valid CSRF token
- **THEN** the system redirects
- **AND** the selected store reviews no longer exist in the database

### Requirement: Unauthenticated access is denied
The functional test SHALL verify that unauthenticated requests to admin store review routes are redirected to the login page.

#### Scenario: Unauthenticated user is redirected from index
- **WHEN** an unauthenticated user requests GET `/admin/store-reviews/`
- **THEN** the response is a redirect to the admin login page
