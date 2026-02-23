## ADDED Requirements

### Requirement: Admin can write a store reply on a review
The admin interface SHALL provide a textarea field for the store reply when editing a review.

#### Scenario: Store reply field appears on review edit form
- **WHEN** an admin edits a product review in the admin panel
- **THEN** a "Store reply" textarea field SHALL be displayed

#### Scenario: Admin submits a store reply
- **WHEN** an admin fills in the store reply field and saves the review
- **THEN** the `storeReply` field SHALL be persisted and `storeRepliedAt` SHALL be set automatically

#### Scenario: Admin clears a store reply
- **WHEN** an admin clears the store reply field and saves the review
- **THEN** `storeReply` SHALL be set to null and `storeRepliedAt` SHALL be set to null

### Requirement: Form extension adds reply field non-invasively
The store reply field SHALL be added via a Symfony form extension, not by overriding Sylius admin templates.

#### Scenario: Form extension targets review forms
- **WHEN** the Sylius admin renders a review edit form
- **THEN** the form extension SHALL add the `storeReply` field to the form
