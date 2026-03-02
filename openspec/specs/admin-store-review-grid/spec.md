## ADDED Requirements

### Requirement: Store review grid lists all store reviews
The admin grid SHALL display store reviews with columns: date (formatted `d-m-Y H:i:s`, sorted descending by default), title, rating, status, review subject (channel), and author (customer). The grid SHALL use the Doctrine ORM driver with the store review model class.

#### Scenario: Admin views store review list
- **WHEN** the admin navigates to the store review index page
- **THEN** the system displays a paginated grid of all store reviews sorted by date descending
- **AND** each row shows the date, title, rating, status label, channel name, and customer name

### Requirement: Store review grid supports filtering
The grid SHALL provide filters for title (string filter) and status (select filter with options: new, accepted, rejected).

#### Scenario: Admin filters by status
- **WHEN** the admin selects "accepted" in the status filter
- **THEN** the grid displays only store reviews with status "accepted"

#### Scenario: Admin filters by title
- **WHEN** the admin enters a search term in the title filter
- **THEN** the grid displays only store reviews whose title matches the search term

### Requirement: Store review grid supports accept and reject actions
The grid SHALL provide item actions to accept and reject store reviews. These actions SHALL apply workflow transitions (`accept`, `reject`) on the `setono_sylius_review__store_review` graph via the Sylius resource controller's `applyStateMachineTransitionAction`. The accept action SHALL use a green style with a checkmark icon. The reject action SHALL use a yellow style with a remove icon.

#### Scenario: Admin accepts a store review from the grid
- **WHEN** the admin clicks the accept action on a store review with status "new"
- **THEN** the system applies the "accept" transition and the review status becomes "accepted"
- **AND** a success flash message is displayed

#### Scenario: Admin rejects a store review from the grid
- **WHEN** the admin clicks the reject action on a store review with status "new"
- **THEN** the system applies the "reject" transition and the review status becomes "rejected"
- **AND** a success flash message is displayed

### Requirement: Store review grid supports update and delete actions
The grid SHALL provide an update item action and both item-level and bulk delete actions.

#### Scenario: Admin deletes a store review
- **WHEN** the admin clicks the delete action on a store review
- **THEN** the system removes the store review from the database

#### Scenario: Admin bulk deletes store reviews
- **WHEN** the admin selects multiple store reviews and triggers bulk delete
- **THEN** the system removes all selected store reviews

### Requirement: Store review grid uses Sylius status label templates
The grid status column SHALL render using Sylius's `@SyliusAdmin/ProductReview/Label/Status` templates for the `new`, `accepted`, and `rejected` states.

#### Scenario: Status labels display correctly
- **WHEN** a store review has status "new"
- **THEN** the grid displays a blue label with an inbox icon
- **WHEN** a store review has status "accepted"
- **THEN** the grid displays a green label with a check icon

### Requirement: Admin menu includes store reviews under Marketing
An event subscriber SHALL listen to the `sylius.menu.admin.main` event and add a "Store reviews" menu item under the `marketing` section. The menu item SHALL link to the store review index route and highlight on the update route as well.

#### Scenario: Store reviews menu item appears in sidebar
- **WHEN** an admin views any page in the admin panel
- **THEN** the Marketing section in the sidebar contains a "Store reviews" item below "Product reviews"

### Requirement: Admin routing provides CRUD and workflow transition routes
The admin routing SHALL register resource routes for store reviews (index, update, delete — excluding show and create) with admin section and permission protection. Separate routes SHALL exist for accept and reject transitions as PUT requests.

#### Scenario: Admin navigates to store review update page
- **WHEN** the admin clicks the update action on a store review
- **THEN** the system displays the store review update form

#### Scenario: Accept route applies workflow transition
- **WHEN** a PUT request is made to the accept route with a valid CSRF token
- **THEN** the system applies the "accept" transition on the `setono_sylius_review__store_review` workflow and redirects to the index

### Requirement: Admin update form exposes review fields and store reply
The update form SHALL display editable fields for title, comment, rating, and store reply. The form SHALL NOT include a status field — status is managed exclusively through accept/reject grid actions.

#### Scenario: Admin edits a store review
- **WHEN** the admin modifies the title and adds a store reply on the update form and submits
- **THEN** the system persists the updated title and store reply
- **AND** the `storeRepliedAt` timestamp is set automatically

#### Scenario: Status field is not present in the form
- **WHEN** the admin views the update form for a store review
- **THEN** no status field is rendered in the form

### Requirement: Update form displays channel and author in sidebar
The update form template SHALL use a 12-wide + 4-wide grid layout. The sidebar (4-wide) SHALL display the channel name and customer information panels.

#### Scenario: Sidebar shows channel and customer info
- **WHEN** the admin views the update form for a store review
- **THEN** the sidebar displays the channel name as a header panel
- **AND** the sidebar displays the customer information below the channel panel
