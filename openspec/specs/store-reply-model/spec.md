### Requirement: StoreReplyInterface defines reply fields
The system SHALL provide a `StoreReplyInterface` with methods for getting and setting the store reply text and timestamp.

#### Scenario: Interface defines getters and setters
- **WHEN** inspecting `StoreReplyInterface`
- **THEN** it SHALL declare `getStoreReply(): ?string`, `setStoreReply(?string): void`, `getStoreRepliedAt(): ?DateTimeInterface`, and `setStoreRepliedAt(?DateTimeInterface): void`

### Requirement: StoreReplyTrait provides reply field implementation
The system SHALL provide a `StoreReplyTrait` implementing the reply fields with ORM attribute mapping.

#### Scenario: Trait provides properties and methods
- **WHEN** a class uses `StoreReplyTrait`
- **THEN** it SHALL have `storeReply` (nullable text) and `storeRepliedAt` (nullable datetime) properties with getters and setters

#### Scenario: Setting a non-null reply auto-sets storeRepliedAt
- **WHEN** `setStoreReply()` is called with a non-null string
- **THEN** `storeRepliedAt` SHALL be automatically set to the current datetime

#### Scenario: Setting reply to null clears storeRepliedAt
- **WHEN** `setStoreReply(null)` is called
- **THEN** `storeRepliedAt` SHALL also be set to null

### Requirement: StoreReview includes reply fields
The `StoreReview` entity SHALL use `StoreReplyTrait` and `StoreReviewInterface` SHALL extend `StoreReplyInterface`.

#### Scenario: StoreReview has reply fields
- **WHEN** a `StoreReview` entity is created
- **THEN** `getStoreReply()` SHALL return null and `getStoreRepliedAt()` SHALL return null

#### Scenario: StoreReview Doctrine mapping includes reply columns
- **WHEN** inspecting the StoreReview ORM mapping
- **THEN** it SHALL include `store_reply` (text, nullable) and `store_replied_at` (datetime, nullable) columns

### Requirement: ProductReviewInterface extends StoreReplyInterface
The system SHALL provide a `ProductReviewInterface` that extends `StoreReplyInterface` for users to implement when extending Sylius's ProductReview.

#### Scenario: Interface is available for user extension
- **WHEN** a user creates a custom ProductReview entity
- **THEN** they SHALL be able to implement `ProductReviewInterface` to gain the reply field contract

### Requirement: ProductReviewTrait provides reply fields for ProductReview
The system SHALL provide a `ProductReviewTrait` that uses `StoreReplyTrait` for users to add to their custom ProductReview entity.

#### Scenario: Trait carries ORM mapping via attributes
- **WHEN** a user's ProductReview entity uses `ProductReviewTrait`
- **THEN** the `storeReply` and `storeRepliedAt` fields SHALL be mapped via ORM attributes on the trait
