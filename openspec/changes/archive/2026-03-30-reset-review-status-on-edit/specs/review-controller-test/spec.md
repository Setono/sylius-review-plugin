## ADDED Requirements

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
