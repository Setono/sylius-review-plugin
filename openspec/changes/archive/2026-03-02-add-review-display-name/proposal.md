## Why

When a customer submits a review, their identity is tied to their Customer account (firstName/lastName). There is no option for the customer to choose how their name appears on the review. Customers should be able to pick a display name from a set of candidates derived from their account info (e.g., "John", "John D.") so they have control over their public-facing identity on reviews.

## What Changes

- Add a `displayName` nullable string field to both store reviews and product reviews via a new `ReviewInterface` + `ReviewTrait` (extending the existing trait+interface pattern)
- Add a display name candidate provider system (composite, tagged) that generates name candidates from `ReviewerInterface` (Customer)
- Ship two built-in candidate providers: first name only, and first name + last initial
- Add a `displayName` ChoiceType field to `ReviewType` (one name per submission, copied to all review entities)
- Add a `DisplayNameResolverInterface` service that resolves a review's display name with fallback logic (displayName → author.firstName → "Anonymous")
- Add a Twig function `review_display_name(review)` for use in templates
- Update shop review templates to use the new Twig function instead of hardcoded `review.author.firstName`

## Capabilities

### New Capabilities
- `review-display-name`: Display name field on reviews, candidate provider system, resolver service, and Twig function

### Modified Capabilities

## Impact

- **Entities**: `StoreReview` and `ProductReview` gain a `displayName` column (DB migration needed by end users)
- **Interfaces**: New `ReviewInterface` inserted into hierarchy; `StoreReviewInterface` and `ProductReviewInterface` updated to extend it
- **Form**: `ReviewType` gains a new `displayName` field; `ReviewCommand` gains a `displayName` property
- **Templates**: Shop review templates updated to use `review_display_name()` instead of `review.author.firstName`
- **Services**: New candidate provider, resolver, and Twig extension services registered in DI
