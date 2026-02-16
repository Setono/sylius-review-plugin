## Context

The plugin has two custom repository classes extending Sylius's `EntityRepository`:
- `ReviewRequestRepository` — 4 custom methods for query building, deletion, and existence checks
- `StoreReviewRepository` — 1 custom method for finding a store review by order

Existing functional tests use `KernelTestCase`, boot the kernel, retrieve services from the container, and rely on DAMA DoctrineTestBundle for automatic transaction rollback. Sylius default fixtures provide test data (orders, channels, customers).

## Goals / Non-Goals

**Goals:**
- Test all custom repository query methods against a real MySQL database
- Verify correct filtering, deletion, and lookup behavior
- Follow established functional test patterns (KernelTestCase, fixture data, DAMA rollback)

**Non-Goals:**
- Testing inherited `EntityRepository` methods (Sylius/Doctrine responsibility)
- Testing repository interface contracts (covered by type system)

## Decisions

**1. Use `KernelTestCase` (not `WebTestCase`)**
Rationale: Repository tests don't need HTTP — they need the kernel + entity manager. Matches `ReviewRequestCreatorTest` pattern.

**2. Retrieve repositories via Sylius service IDs**
- `setono_sylius_review.repository.review_request` for `ReviewRequestRepository`
- `setono_sylius_review.repository.store_review` for `StoreReviewRepository`
Rationale: These are Sylius resource-managed services. Using FQCN would bypass the Sylius resource registration.

**3. Create test entities via factory + entity manager**
For `ReviewRequestRepository` tests, create `ReviewRequest` entities manually (set order, state, dates), persist, and flush. For `StoreReviewRepository`, create `StoreReview` entities with the order association.
Rationale: Need precise control over entity state for each test scenario.

**4. File structure mirrors source**
- `tests/Functional/Repository/ReviewRequestRepositoryTest.php`
- `tests/Functional/Repository/StoreReviewRepositoryTest.php`

## Risks / Trade-offs

**[Risk] Fixture data interference** — Default fixtures might include orders/reviews that affect query results.
→ Mitigation: Use specific fixture orders and assert on known entities. DAMA rollback ensures clean state per test.

**[Risk] `createForProcessingQueryBuilder()` uses `new \DateTimeImmutable()` internally** — Time-dependent query.
→ Mitigation: Set `nextEligibilityCheckAt` to past dates (clearly eligible) or far-future dates (clearly ineligible) to avoid timing issues.
