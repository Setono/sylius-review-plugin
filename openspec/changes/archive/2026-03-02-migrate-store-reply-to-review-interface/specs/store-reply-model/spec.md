## MODIFIED Requirements

### Requirement: StoreReplyInterface defines reply fields
The system SHALL define store reply methods (`getStoreReply`, `setStoreReply`, `getStoreRepliedAt`, `setStoreRepliedAt`) directly on `ReviewInterface`. `StoreReplyInterface` SHALL be removed.

#### Scenario: ReviewInterface declares reply methods
- **WHEN** inspecting `ReviewInterface`
- **THEN** it SHALL declare `getStoreReply(): ?string`, `setStoreReply(?string): void`, `getStoreRepliedAt(): ?DateTimeInterface`, `setStoreRepliedAt(?DateTimeInterface): void`, `getDisplayName(): ?string`, and `setDisplayName(?string): void`

### Requirement: StoreReplyTrait provides reply field implementation
The system SHALL implement store reply fields in `ReviewTrait` with ORM attribute mapping. `StoreReplyTrait` SHALL be removed.

#### Scenario: ReviewTrait provides reply properties and methods
- **WHEN** a class uses `ReviewTrait`
- **THEN** it SHALL have `storeReply` (nullable text), `storeRepliedAt` (nullable datetime), and `displayName` (nullable string) properties with getters and setters

#### Scenario: Setting a non-null reply auto-sets storeRepliedAt
- **WHEN** `setStoreReply()` is called with a non-null string
- **THEN** `storeRepliedAt` SHALL be automatically set to the current datetime

#### Scenario: Setting reply to null clears storeRepliedAt
- **WHEN** `setStoreReply(null)` is called
- **THEN** `storeRepliedAt` SHALL also be set to null

### Requirement: StoreReview includes reply fields
The `StoreReview` entity SHALL use `ReviewTrait` (which now includes reply fields). `StoreReviewInterface` SHALL extend `ReviewInterface` only (not `StoreReplyInterface`).

#### Scenario: StoreReview has reply fields
- **WHEN** a `StoreReview` entity is created
- **THEN** `getStoreReply()` SHALL return null and `getStoreRepliedAt()` SHALL return null

#### Scenario: StoreReview Doctrine mapping includes reply columns
- **WHEN** inspecting the StoreReview ORM mapping
- **THEN** it SHALL include `store_reply` (text, nullable) and `store_replied_at` (datetime, nullable) columns via attribute mapping in `ReviewTrait`

### Requirement: ProductReviewInterface extends StoreReplyInterface
`ProductReviewInterface` SHALL extend `ReviewInterface` only (not `StoreReplyInterface`). The reply field contract is inherited from `ReviewInterface`.

#### Scenario: Interface is available for user extension
- **WHEN** a user creates a custom ProductReview entity
- **THEN** they SHALL be able to implement `ProductReviewInterface` to gain the reply field contract via `ReviewInterface`

### Requirement: ProductReviewTrait provides reply fields for ProductReview
`ProductReviewTrait` SHALL use `ReviewTrait` only (not `StoreReplyTrait`). Reply fields are included via `ReviewTrait`.

#### Scenario: Trait carries ORM mapping via attributes
- **WHEN** a user's ProductReview entity uses `ProductReviewTrait`
- **THEN** the `storeReply` and `storeRepliedAt` fields SHALL be mapped via ORM attributes from `ReviewTrait`
