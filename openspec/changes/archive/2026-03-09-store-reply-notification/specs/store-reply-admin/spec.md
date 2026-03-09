## MODIFIED Requirements

### Requirement: Admin can write a store reply on a review
The admin interface SHALL provide a textarea field for the store reply and a "Notify reviewer" checkbox when editing a review.

#### Scenario: Store reply field appears on review edit form
- **WHEN** an admin edits a product review in the admin panel
- **THEN** a "Store reply" textarea field SHALL be displayed

#### Scenario: Notify reviewer checkbox appears on review edit form
- **WHEN** an admin edits a review (store or product) in the admin panel
- **THEN** a "Notify reviewer" checkbox SHALL be displayed alongside the store reply field

#### Scenario: Admin submits a store reply
- **WHEN** an admin fills in the store reply field and saves the review
- **THEN** the `storeReply` field SHALL be persisted and `storeRepliedAt` SHALL be set automatically

#### Scenario: Admin clears a store reply
- **WHEN** an admin clears the store reply field and saves the review
- **THEN** `storeReply` SHALL be set to null and `storeRepliedAt` SHALL be set to null

### Requirement: Form extension adds reply field and notify checkbox non-invasively
The store reply field and notify reviewer checkbox SHALL be added via Symfony form extensions, not by overriding Sylius admin templates.

#### Scenario: Form extension targets review forms
- **WHEN** the Sylius admin renders a review edit form
- **THEN** the form extension SHALL add the `storeReply` field and `notifyReviewer` checkbox to the form

#### Scenario: Notify checkbox on store review admin form
- **WHEN** the store review admin form is rendered
- **THEN** the `notifyReviewer` checkbox SHALL be included in the form
