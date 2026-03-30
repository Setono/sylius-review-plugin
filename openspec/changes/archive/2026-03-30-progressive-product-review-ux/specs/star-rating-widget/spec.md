## MODIFIED Requirements

### Requirement: Star selection syncs to hidden radio buttons

When a user clicks a star, the corresponding hidden radio button SHALL be checked so the form submits the correct rating value. When a user clears a rating (clicks the active star to deselect), all radio buttons in the group SHALL be unchecked.

#### Scenario: User selects 4 stars
- **WHEN** the user clicks the 4th star on any rating widget
- **THEN** the radio button with value `4` in the corresponding hidden radio group SHALL be checked
- **AND** any previously checked radio button in that group SHALL be unchecked

#### Scenario: User changes rating from 3 to 5
- **WHEN** the user has selected 3 stars and then clicks the 5th star
- **THEN** the radio button with value `5` SHALL be checked
- **AND** the radio button with value `3` SHALL be unchecked

#### Scenario: User clears rating by clicking active star
- **WHEN** the user has selected 3 stars and clicks the 3rd star again
- **THEN** all radio buttons in the corresponding hidden radio group SHALL be unchecked

### Requirement: Star rating widgets support clearing

All star rating widgets on the review page SHALL be initialized with `clearable: true`, allowing users to click the currently selected star to reset the rating to 0.

#### Scenario: User clears a product review rating
- **WHEN** the user has selected 4 stars on a product review widget
- **AND** the user clicks the 4th star again
- **THEN** the widget SHALL display 0 filled stars
- **AND** the `onRate` callback SHALL fire with value `0`

#### Scenario: User clears the store review rating
- **WHEN** the user has selected 5 stars on the store review widget
- **AND** the user clicks the 5th star again
- **THEN** the widget SHALL display 0 filled stars
