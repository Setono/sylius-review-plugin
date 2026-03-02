## Why

`StoreReplyInterface` and `StoreReplyTrait` exist as separate abstractions, but store reply is a core part of the review model — both `StoreReviewInterface` and `ProductReviewInterface` extend it. Consolidating into `ReviewInterface`/`ReviewTrait` simplifies the model hierarchy and removes unnecessary indirection.

## What Changes

- **BREAKING**: Move `getStoreReply()`, `setStoreReply()`, `getStoreRepliedAt()`, `setStoreRepliedAt()` from `StoreReplyInterface` into `ReviewInterface`
- **BREAKING**: Move the property definitions and method implementations from `StoreReplyTrait` into `ReviewTrait`
- **BREAKING**: Remove `StoreReplyInterface` and `StoreReplyTrait`
- Remove `StoreReplyInterface` from the `extends` clause of `StoreReviewInterface` and `ProductReviewInterface`
- Remove `use StoreReplyTrait` from `StoreReview` and `ProductReviewTrait`
- Update the Doctrine XML mapping for StoreReview to remove store reply fields (now handled by attribute mapping in `ReviewTrait`)

## Capabilities

### New Capabilities
_(none)_

### Modified Capabilities

- `store-reply-model`: Store reply methods move from a separate interface/trait into ReviewInterface/ReviewTrait

## Impact

- **Model layer**: `ReviewInterface`, `ReviewTrait`, `StoreReviewInterface`, `ProductReviewInterface`, `StoreReview`, `ProductReviewTrait` all change
- **Deleted files**: `StoreReplyInterface.php`, `StoreReplyTrait.php`
- **Doctrine mapping**: `StoreReview.orm.xml` loses `storeReply`/`storeRepliedAt` fields (covered by attribute mapping in trait)
- **Tests**: `StoreReplyTraitTest` needs to be migrated or removed
- **Form types/templates**: No changes needed — they reference `storeReply`/`storeRepliedAt` on the entity which remains the same
- **Plugin class**: `SetonoSyliusReviewPlugin` may reference `StoreReplyInterface` in model mapping
