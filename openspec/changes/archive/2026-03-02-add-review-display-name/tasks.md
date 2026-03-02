## 1. Model Layer

- [x] 1.1 Create `ReviewInterface` extending Sylius `ReviewInterface` with `getDisplayName()`/`setDisplayName()`
- [x] 1.2 Create `ReviewTrait` with `#[ORM\Column]` nullable string `displayName` field and getter/setter
- [x] 1.3 Update `StoreReviewInterface` to extend `ReviewInterface` (remove direct extend of Sylius `ReviewInterface`)
- [x] 1.4 Update `StoreReview` to use `ReviewTrait`
- [x] 1.5 Update `ProductReviewInterface` to extend `ReviewInterface`
- [x] 1.6 Update `ProductReviewTrait` to use `ReviewTrait`

## 2. Candidate Provider

- [x] 2.1 Create `DisplayNameCandidateProviderInterface` with `candidates(ReviewerInterface): iterable<string>`
- [x] 2.2 Create `FirstNameCandidateProvider` — returns first name if non-empty
- [x] 2.3 Create `FirstNameLastInitialCandidateProvider` — returns "FirstName L." if both names non-empty
- [x] 2.4 Create `CompositeDisplayNameCandidateProvider` — aggregates tagged providers, deduplicates
- [x] 2.5 Register services and autoconfiguration tag in DI

## 3. Display Name Resolver + Twig

- [x] 3.1 Create `DisplayNameResolverInterface` with `resolve(BaseReviewInterface): string`
- [x] 3.2 Create `DefaultDisplayNameResolver` implementing the fallback chain (displayName → author.firstName → translated "Anonymous")
- [x] 3.3 Create Twig extension with `review_display_name()` function delegating to the resolver
- [x] 3.4 Register resolver and Twig extension services in DI

## 4. Form + Controller

- [x] 4.1 Add `displayName` property to `ReviewCommand` DTO
- [x] 4.2 Add `displayName` ChoiceType field to `ReviewType`, populated from candidate provider
- [x] 4.3 Handle pre-selection of existing display name on re-edit (PRE_SET_DATA listener)
- [x] 4.4 Update `ReviewController` to copy `displayName` from `ReviewCommand` to each persisted review entity

## 5. Templates

- [x] 5.1 Update shop review form template to render the `displayName` field
- [x] 5.2 Update `ProductReview/_single.html.twig` to use `{{ review_display_name(review) }}`
- [x] 5.3 Add translations for the display name label and "Anonymous" fallback

## 6. Tests

- [x] 6.1 Unit tests for `FirstNameCandidateProvider`
- [x] 6.2 Unit tests for `FirstNameLastInitialCandidateProvider`
- [x] 6.3 Unit tests for `CompositeDisplayNameCandidateProvider`
- [x] 6.4 Unit tests for `DefaultDisplayNameResolver`
- [x] 6.5 Form type test for `ReviewType` with display name field
- [x] 6.6 Functional test for review submission with display name
