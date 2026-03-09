## MODIFIED Requirements

### Requirement: ReviewInterface defines reply fields
The system SHALL define store reply methods (`getStoreReply`, `setStoreReply`, `getStoreRepliedAt`, `setStoreRepliedAt`, `getNotifyReviewer`, `setNotifyReviewer`) directly on `ReviewInterface`.

#### Scenario: ReviewInterface declares reply methods
- **WHEN** inspecting `ReviewInterface`
- **THEN** it SHALL declare `getStoreReply(): ?string`, `setStoreReply(?string): void`, `getStoreRepliedAt(): ?DateTimeInterface`, `setStoreRepliedAt(?DateTimeInterface): void`, `getDisplayName(): ?string`, `setDisplayName(?string): void`, `getNotifyReviewer(): bool`, and `setNotifyReviewer(bool): void`

### Requirement: ReviewTrait provides reply field implementation
The system SHALL implement store reply fields in `ReviewTrait` with ORM attribute mapping, including `notifyReviewer` as a boolean defaulting to `true`.

#### Scenario: ReviewTrait provides reply properties and methods
- **WHEN** a class uses `ReviewTrait`
- **THEN** it SHALL have `storeReply` (nullable text), `storeRepliedAt` (nullable datetime), `displayName` (nullable string), and `notifyReviewer` (boolean, default true) properties with getters and setters

#### Scenario: Setting a non-null reply auto-sets storeRepliedAt
- **WHEN** `setStoreReply()` is called with a non-null string
- **THEN** `storeRepliedAt` SHALL be automatically set to the current datetime

#### Scenario: Setting reply to null clears storeRepliedAt
- **WHEN** `setStoreReply(null)` is called
- **THEN** `storeRepliedAt` SHALL also be set to null
