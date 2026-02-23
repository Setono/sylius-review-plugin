## Why

Store owners have no way to publicly respond to customer reviews. Being able to reply to reviews (both product and store reviews) is a standard e-commerce feature that builds trust, shows engagement, and allows stores to address concerns publicly.

## What Changes

- Add `storeReply` (nullable text) and `storeRepliedAt` (nullable datetime) fields to both store reviews and product reviews
- Create a shared `StoreReplyInterface` + `StoreReplyTrait` for the field implementation
- Add fields directly to `StoreReview` (plugin-owned entity)
- Provide `ProductReviewInterface` + `ProductReviewTrait` for users to extend Sylius's `ProductReview` entity (same Channel-style extension pattern already used in this plugin)
- Update Doctrine mapping for `StoreReview`
- Add admin UI for store owners to write replies on review detail pages
- Display store replies in the shop-facing review templates
- Document the `ProductReview` entity extension in README

## Capabilities

### New Capabilities
- `store-reply-model`: Shared interface/trait and entity extensions for the store reply fields
- `store-reply-admin`: Admin UI for writing store replies on reviews
- `store-reply-shop`: Shop-facing display of store replies

### Modified Capabilities
<!-- None — existing specs cover test infrastructure, not review features -->

## Impact

- **Models**: `StoreReview`, `StoreReviewInterface` gain new fields; new `StoreReplyInterface`, `StoreReplyTrait`, `ProductReviewInterface`, `ProductReviewTrait`
- **Doctrine**: New columns on `setono_sylius_review__store_review` table; users must add columns to `sylius_product_review` if extending
- **Admin UI**: New form fields and/or template overrides for review editing
- **Shop UI**: Template changes to display store replies below customer comments
- **Documentation**: README update for ProductReview entity extension setup
- **Breaking**: None — new fields are nullable, no existing behavior changes
