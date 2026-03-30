## MODIFIED Requirements

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
