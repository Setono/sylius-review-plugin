## Context

Currently the model hierarchy is:
- `ReviewInterface` (has `displayName`) → extended by `StoreReviewInterface`, `ProductReviewInterface`
- `StoreReplyInterface` (has `storeReply`, `storeRepliedAt`) → extended by `StoreReviewInterface`, `ProductReviewInterface`

Both `StoreReviewInterface` and `ProductReviewInterface` extend `StoreReplyInterface`, meaning store reply is effectively a universal review capability. The separate interface adds no value — it should live in `ReviewInterface`.

## Goals / Non-Goals

**Goals:**
- Consolidate `StoreReplyInterface` methods into `ReviewInterface`
- Consolidate `StoreReplyTrait` implementation into `ReviewTrait`
- Remove the now-empty `StoreReplyInterface` and `StoreReplyTrait` files
- Keep all existing behavior identical

**Non-Goals:**
- Renaming methods or changing signatures
- Changing Doctrine mapping strategy (attribute-based mapping in trait stays)
- Modifying form types, templates, or services

## Decisions

### 1. Merge into ReviewInterface/ReviewTrait vs creating a new abstraction

**Decision**: Merge directly into `ReviewInterface` and `ReviewTrait`.

**Rationale**: Since both review types already extend `StoreReplyInterface`, the store reply capability is universal. No need for a separate abstraction.

### 2. Doctrine mapping for StoreReview

**Decision**: Remove the `storeReply` and `storeRepliedAt` fields from `StoreReview.orm.xml`. The attribute mapping in `ReviewTrait` (via `#[ORM\Column]`) will handle both StoreReview and ProductReview.

**Rationale**: `StoreReplyTrait` already uses attribute-based mapping. Moving to `ReviewTrait` keeps the same approach. The XML mapping in `StoreReview.orm.xml` would conflict with attribute mapping if both exist.

### 3. Test migration

**Decision**: Rename `StoreReplyTraitTest` to test the same behavior through `ReviewTrait`. The test assertions remain the same since the behavior is identical.

## Risks / Trade-offs

- **[Breaking change for downstream]** → Any code type-hinting against `StoreReplyInterface` will break. This is acceptable as a major version change. The fix is simple: replace `StoreReplyInterface` with `ReviewInterface`.
- **[Doctrine mapping conflict]** → Must ensure XML mapping fields are removed before adding attribute mapping, or Doctrine will see duplicate column definitions.
