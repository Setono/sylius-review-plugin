### Requirement: Star rating widget replaces raw radio buttons

Each rating field on the review page SHALL be rendered as a Semantic UI star rating widget (`<div class="ui huge star rating">`) instead of raw radio buttons. The actual radio button form inputs SHALL be hidden with `display: none` but remain in the DOM for form submission.

#### Scenario: Store review rating renders as stars
- **WHEN** the review page is rendered
- **THEN** the store review rating field SHALL display a Semantic UI star rating widget with 5 stars
- **AND** the native radio button group SHALL be hidden

#### Scenario: Product review rating renders as stars
- **WHEN** the review page is rendered with products
- **THEN** each product review rating field SHALL display a Semantic UI star rating widget with 5 stars
- **AND** each native radio button group SHALL be hidden

### Requirement: Star selection syncs to hidden radio buttons

When a user clicks a star, the corresponding hidden radio button SHALL be checked so the form submits the correct rating value.

#### Scenario: User selects 4 stars
- **WHEN** the user clicks the 4th star on any rating widget
- **THEN** the radio button with value `4` in the corresponding hidden radio group SHALL be checked
- **AND** any previously checked radio button in that group SHALL be unchecked

#### Scenario: User changes rating from 3 to 5
- **WHEN** the user has selected 3 stars and then clicks the 5th star
- **THEN** the radio button with value `5` SHALL be checked
- **AND** the radio button with value `3` SHALL be unchecked

### Requirement: Pre-selected ratings display correctly

When a rating field has a pre-existing value (e.g., editing an existing review), the star widget SHALL display the correct number of filled stars on page load.

#### Scenario: Existing review with rating 4
- **WHEN** the review page loads with an existing review that has a rating of 4
- **THEN** the star widget SHALL display 4 filled stars and 1 empty star
- **AND** the corresponding radio button SHALL remain checked

### Requirement: Star-to-radio pairing uses data attributes

Each star rating widget SHALL use a `data-rating-target` attribute to identify which radio button group it controls. This pairing SHALL NOT rely on DOM proximity or hardcoded form field names.

#### Scenario: Multiple rating widgets on page
- **WHEN** the page has a store review and 3 product reviews
- **THEN** each star widget SHALL have a unique `data-rating-target` value
- **AND** each value SHALL correspond to the ID of its paired radio group wrapper

### Requirement: JavaScript initializes via inline script

The star rating initialization JavaScript SHALL be included as an inline `<script>` block in the review page template (inside the `javascripts` block). It SHALL use jQuery and Semantic UI's `.rating()` method, both of which are already available from the Sylius shop bundle.

#### Scenario: Script initializes all star widgets
- **WHEN** the review page DOM is ready
- **THEN** the script SHALL call `.rating()` on every `.star.rating` element
- **AND** each widget SHALL use `fireOnInit: true` to sync pre-existing ratings
