### Requirement: Review form displays disclaimer text
The review submission form SHALL display a disclaimer text above the submit button informing the customer that their review will be publicly visible.

#### Scenario: Disclaimer text is visible on the review form
- **WHEN** a customer navigates to the review submission page for a fulfilled order
- **THEN** the form SHALL display a disclaimer text above the submit button

### Requirement: Disclaimer text is translatable
The disclaimer text SHALL use a translation key so it can be customized per locale.

#### Scenario: Disclaimer text uses translation key
- **WHEN** the review form is rendered
- **THEN** the disclaimer text SHALL use a translation key from the `messages` domain
