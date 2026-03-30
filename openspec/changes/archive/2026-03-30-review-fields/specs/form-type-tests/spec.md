## MODIFIED Requirements

### Requirement: StoreReviewType has correct form fields
`StoreReviewType` SHALL build a form with two fields: `rating` (ChoiceType, expanded, choices 1-5) and `comment` (TextareaType, required). The `title` field SHALL NOT be present.

#### Scenario: Form contains expected fields
- **WHEN** a `StoreReviewType` form is created with a valid `order` option
- **THEN** the form SHALL have children named `rating` and `comment`
- **AND** the form SHALL NOT have a child named `title`

#### Scenario: Comment field is required
- **WHEN** the `comment` field is inspected
- **THEN** it SHALL have `required` set to `true`

#### Scenario: Rating field uses expanded choices
- **WHEN** the `rating` field is inspected
- **THEN** it SHALL be a ChoiceType with `expanded` true, `multiple` false, and choices mapping labels `'1'`-`'5'` to integers `1`-`5`

### Requirement: ProductReviewType has correct form fields
`ProductReviewType` SHALL build a form with two fields: `rating` (ChoiceType, expanded, choices 1-5) and `comment` (TextareaType, optional). The `title` field SHALL NOT be present.

#### Scenario: Form contains expected fields
- **WHEN** a `ProductReviewType` form is created
- **THEN** the form SHALL have children named `rating` and `comment`
- **AND** the form SHALL NOT have a child named `title`

#### Scenario: Rating field uses expanded choices
- **WHEN** the `rating` field is inspected
- **THEN** it SHALL be a ChoiceType with `expanded` true, `multiple` false, and choices mapping labels `'1'`-`'5'` to integers `1`-`5`

### Requirement: ProductReviewType maps submitted data to entity
Submitting data through the form SHALL map field values onto the underlying data object.

#### Scenario: Form submission populates entity fields
- **WHEN** the form is submitted with `rating` and `comment` values
- **THEN** the underlying data object SHALL have those values set
