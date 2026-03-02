## ADDED Requirements

### Requirement: Review entities store a display name
Both store reviews and product reviews SHALL have an optional `displayName` string field. The plugin SHALL provide a `ReviewInterface` (extending Sylius's `ReviewInterface`) with `getDisplayName(): ?string` and `setDisplayName(?string): void`, and a corresponding `ReviewTrait` with the ORM column mapping. `StoreReviewInterface` and `ProductReviewInterface` SHALL extend `ReviewInterface`. `StoreReview` SHALL use `ReviewTrait` directly. `ProductReviewTrait` SHALL use `ReviewTrait`.

#### Scenario: StoreReview has display name
- **WHEN** a store review entity is created
- **THEN** it SHALL have a nullable `displayName` field that defaults to null

#### Scenario: ProductReview has display name via trait
- **WHEN** the end user's ProductReview entity uses `ProductReviewTrait`
- **THEN** it SHALL have a nullable `displayName` field mapped to the database

### Requirement: Display name candidate provider generates name options
The system SHALL provide a `DisplayNameCandidateProviderInterface` with method `candidates(ReviewerInterface $reviewer): iterable<string>`. A composite implementation SHALL aggregate all tagged providers, deduplicate results, and skip empty strings.

#### Scenario: First name candidate
- **WHEN** a customer has firstName "John"
- **THEN** the `FirstNameCandidateProvider` SHALL return "John"

#### Scenario: First name + last initial candidate
- **WHEN** a customer has firstName "John" and lastName "Doe"
- **THEN** the `FirstNameLastInitialCandidateProvider` SHALL return "John D."

#### Scenario: Empty first name is skipped
- **WHEN** a customer has a null or empty firstName
- **THEN** the `FirstNameCandidateProvider` SHALL return no candidates

#### Scenario: Empty last name skips last initial provider
- **WHEN** a customer has firstName "John" but null or empty lastName
- **THEN** the `FirstNameLastInitialCandidateProvider` SHALL return no candidates

#### Scenario: Duplicate candidates are removed
- **WHEN** multiple providers return the same string value
- **THEN** the composite provider SHALL return each unique value only once

### Requirement: Review form includes display name selection
The `ReviewType` form SHALL include a `displayName` field of type `ChoiceType` whose choices are populated from the candidate provider. The `ReviewCommand` DTO SHALL have a `displayName` property. The chosen display name SHALL be applied to the store review and all product reviews upon form submission.

#### Scenario: Customer selects a display name
- **WHEN** a customer submits the review form with displayName "John"
- **THEN** the store review and all product reviews persisted from that submission SHALL have `displayName` set to "John"

#### Scenario: No candidates available
- **WHEN** the candidate provider returns no candidates for the customer
- **THEN** the `displayName` field SHALL not be rendered and `displayName` on the reviews SHALL remain null

#### Scenario: Re-editing a review preserves display name
- **WHEN** a customer returns to edit a previously submitted review
- **THEN** the form SHALL pre-select the previously chosen display name

### Requirement: Display name resolver provides the rendered name
The system SHALL provide a `DisplayNameResolverInterface` with method `resolve(BaseReviewInterface $review): string`. The default implementation SHALL follow this fallback chain:
1. If the review implements `ReviewInterface` and `getDisplayName()` returns a non-null, non-empty string, return it
2. If the review has an author and `getFirstName()` returns a non-null, non-empty string, return it
3. Return a translated "Anonymous" fallback string

#### Scenario: Review with display name set
- **WHEN** a review has `displayName` "John D."
- **THEN** the resolver SHALL return "John D."

#### Scenario: Legacy review without display name
- **WHEN** a review does not implement `ReviewInterface` (e.g., a plain Sylius review)
- **THEN** the resolver SHALL return `author.firstName` or "Anonymous"

#### Scenario: Review with null display name
- **WHEN** a review implements `ReviewInterface` but `displayName` is null
- **THEN** the resolver SHALL fall back to `author.firstName` or "Anonymous"

### Requirement: Twig function for display name rendering
The system SHALL provide a Twig function `review_display_name(review)` that delegates to the `DisplayNameResolverInterface`. All shop review templates SHALL use this function instead of directly accessing `review.author.firstName`.

#### Scenario: Template renders display name
- **WHEN** a template calls `{{ review_display_name(review) }}`
- **THEN** it SHALL output the resolved display name string

### Requirement: Display name validation
The `displayName` field on the review form SHALL be validated. The submitted value MUST be one of the candidates returned by the provider for that customer (enforced by Symfony's ChoiceType validation).

#### Scenario: Valid display name submitted
- **WHEN** a customer submits a display name that is in the candidate list
- **THEN** the form SHALL accept it

#### Scenario: Tampered display name rejected
- **WHEN** a submitted display name is not in the candidate list
- **THEN** Symfony's ChoiceType validation SHALL reject the submission
