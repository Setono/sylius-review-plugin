## Context

The plugin manages two review entity types:
- **StoreReview** — Fully owned by the plugin (`src/Model/StoreReview.php`), mapped via `src/Resources/config/doctrine/model/StoreReview.orm.xml`
- **ProductReview** — Owned by Sylius core, extendable via the resource bundle's model class override pattern

The plugin already uses an Interface + Trait pattern for extending Sylius's Channel entity (see `ChannelInterface` + `ChannelTrait`). Users create a concrete entity in their app that extends the Sylius base and uses the trait. This is documented in the README.

Currently, neither review type has any store response field. The admin can only accept/reject reviews.

## Goals / Non-Goals

**Goals:**
- Add `storeReply` and `storeRepliedAt` fields to both review types
- Provide a shared trait so the field logic is defined once
- Follow the existing Channel extension pattern for ProductReview
- Add admin UI for entering replies
- Display replies in shop templates

**Non-Goals:**
- Threaded conversations (multiple replies)
- Individual admin user attribution on replies
- Approval workflow for store replies
- Notification to customer when store replies
- API/GraphQL endpoints for store replies

## Decisions

### 1. Shared trait for the reply fields

**Decision**: Create `StoreReplyInterface` + `StoreReplyTrait` containing `storeReply` and `storeRepliedAt`.

**Rationale**: Both StoreReview and ProductReview need identical fields. A shared trait avoids duplication and ensures consistent behavior. This follows the existing `ChannelTrait` pattern.

**Alternative considered**: Adding the fields separately to each entity. Rejected because it duplicates logic and diverges over time.

### 2. StoreReview gets fields directly, ProductReview via trait

**Decision**:
- `StoreReview` uses `StoreReplyTrait` directly and `StoreReviewInterface` extends `StoreReplyInterface`
- `ProductReviewTrait` uses `StoreReplyTrait` and `ProductReviewInterface` extends `StoreReplyInterface`
- Users extend Sylius's ProductReview entity (same as Channel pattern)

**Rationale**: The plugin owns StoreReview and can modify it directly. ProductReview is owned by Sylius, so it must follow the established extension pattern.

### 3. Doctrine mapping strategy

**Decision**:
- StoreReview: Add fields to existing `StoreReview.orm.xml`
- ProductReview: Use ORM attributes in `ProductReviewTrait` (same as `ChannelTrait` uses `#[ORM\Column]`)

**Rationale**: The plugin controls StoreReview's XML mapping. For ProductReview, the trait must carry its own mapping since the plugin doesn't own the entity's mapping file. This is consistent with how `ChannelTrait` already uses ORM attributes.

### 4. `storeRepliedAt` auto-set behavior

**Decision**: `storeRepliedAt` SHALL be automatically set when `storeReply` is set to a non-null value (within the setter). If `storeReply` is set to null, `storeRepliedAt` is also set to null.

**Rationale**: Prevents inconsistent state where a reply exists without a timestamp or vice versa. Keeps the model self-consistent without requiring external coordination.

### 5. Admin UI approach

**Decision**: Add a Symfony form extension that extends the existing Sylius review admin forms with a `storeReply` textarea field. The `storeRepliedAt` field is managed automatically by the setter and not shown in the form.

**Rationale**: Form extensions are non-invasive and don't require overriding Sylius templates. They integrate with whatever admin review form Sylius provides.

## Risks / Trade-offs

- [Risk] Users who don't extend ProductReview won't get the feature for product reviews → Mitigation: Clear README documentation, same pattern they already follow for Channel
- [Risk] Doctrine schema migration required for existing installations → Mitigation: Fields are nullable, so migration is non-destructive (`ALTER TABLE ADD COLUMN ... NULL`)
- [Trade-off] Auto-setting `storeRepliedAt` in the setter couples the two fields → Acceptable because they are semantically coupled; a reply always has a timestamp

## Migration Plan

1. Users run `doctrine:migrations:diff` + `doctrine:migrations:migrate` to add the new nullable columns
2. Users who want product review replies must extend ProductReview (documented in README, same as Channel)
3. No data migration needed — new fields default to null

## Open Questions

None — all decisions resolved during exploration.
