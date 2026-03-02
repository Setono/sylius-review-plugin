## Context

Reviews are currently tied to the Customer entity for author identity. The displayed name is hardcoded as `review.author.firstName` in templates. There is no way for customers to choose how their name appears publicly.

The plugin already uses a trait+interface pattern for extending entities (e.g., `StoreReplyTrait`/`StoreReplyInterface` for store replies, `ChannelTrait`/`ChannelInterface` for reviewable channels). The same pattern applies here.

`StoreReview` is owned by the plugin. `ProductReview` is owned by Sylius and extended by end users via the existing `ProductReviewTrait`/`ProductReviewInterface`.

## Goals / Non-Goals

**Goals:**
- Allow customers to pick a display name from candidates when submitting a review
- One display name per submission (applied to store review + all product reviews)
- Extensible candidate provider system (tagged services)
- Centralized display name resolution with fallback logic
- Twig function for consistent rendering across all templates

**Non-Goals:**
- Free-text / custom display name input
- Admin editing of display names
- Anonymous/pseudonym support beyond what the candidate providers generate
- Handling the edge case of customers with no first/last name (fallback to null)

## Decisions

### 1. New `ReviewInterface` + `ReviewTrait` in the plugin's Model namespace

**Decision**: Create `Setono\SyliusReviewPlugin\Model\ReviewInterface` extending `Sylius\Component\Review\Model\ReviewInterface`, adding `getDisplayName()`/`setDisplayName()`. A corresponding `ReviewTrait` holds the ORM column.

**Rationale**: Follows the established trait+interface pattern (`StoreReplyTrait`, `ChannelTrait`). Creates a single plugin-level base review contract. Both `StoreReviewInterface` and `ProductReviewInterface` extend it.

**Alternatives considered**:
- A feature-specific `HasDisplayNameInterface` + `DisplayNameTrait` — works but doesn't create a unified plugin review contract. Harder to extend later if more shared fields are needed.

### 2. Composite candidate provider with service tagging

**Decision**: `DisplayNameCandidateProviderInterface::candidates(ReviewerInterface): iterable<string>`. Composite aggregates tagged providers. Built-in: `FirstNameCandidateProvider` ("John") and `FirstNameLastInitialCandidateProvider` ("John D.").

**Rationale**: Extensible — store owners can add custom providers (e.g., username-based) by tagging a service. The composite deduplicates and filters empty strings.

**Alternatives considered**:
- Single provider class with all logic — simpler but not extensible.
- Candidates as value objects with labels — over-engineered, the string IS the label.

### 3. Display name field on `ReviewType` (not sub-types)

**Decision**: The `displayName` ChoiceType field lives on `ReviewType` (the parent form), not on `StoreReviewType`/`ProductReviewType`. The `ReviewCommand` DTO gets a `displayName` property. The controller copies it to each review entity on submission.

**Rationale**: It's one name per submission. Placing it on the parent form reflects this semantically and avoids duplicate fields.

### 4. DisplayNameResolver + Twig function for rendering

**Decision**: `DisplayNameResolverInterface::resolve(BaseReviewInterface): string` with fallback chain: `displayName` (if review implements `ReviewInterface`) → `author.firstName` → translated "Anonymous". Exposed as `review_display_name(review)` Twig function.

**Rationale**: Centralizes rendering logic. Works for legacy reviews without the `displayName` column, Sylius core reviews, and plugin reviews. Templates become consistent and decoupled from the entity structure.

### 5. If no candidates exist, display name is null

**Decision**: When the candidate provider returns no candidates (e.g., customer has no name), the form field is not rendered and `displayName` stays null. The resolver falls back to `author.firstName` or "Anonymous".

**Rationale**: In practice, Sylius checkout requires first/last name, so this edge case is negligible. No need to manufacture artificial candidates.

## Risks / Trade-offs

- **DB migration required by end users**: Adding the `displayName` column to `StoreReview` and `ProductReview` requires a doctrine migration. → Standard for any entity change; documented in upgrade notes.
- **Existing reviews have null displayName**: The resolver handles this gracefully via fallback chain. → No data migration needed.
- **ChoiceType with no choices**: If somehow no candidates exist, Symfony renders an empty select. → Accept this; practically never happens (see Decision 5).
