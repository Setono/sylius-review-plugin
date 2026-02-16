## Context

The `AverageRatingCalculator` and `CachedAverageRatingCalculator` form a decoration chain for computing average review ratings. They currently have no tests despite containing non-trivial logic (ORM query building, cache key generation, multiple fallback paths).

The project uses PHPUnit with Prophecy for mocking, follows BDD-style naming, and organizes tests under `tests/Unit/` and `tests/Functional/`.

## Goals / Non-Goals

**Goals:**
- Test `AverageRatingCalculator` with a real database (functional test) to verify the actual AVG query
- Test `CachedAverageRatingCalculator` with unit tests covering all branches
- Follow existing project test conventions (Prophecy for unit tests, WebTestCase for functional tests, BDD naming)

**Non-Goals:**
- Testing the Sylius default calculator (third-party code)
- Testing `ConfigureAverageRatingCalculatorCachePass` (compiler pass, separate concern)

## Decisions

**1. Functional test for AverageRatingCalculator using real database**
Rationale: The core value of this class is its SQL AVG query. Mocking the entire Doctrine chain would be verbose and wouldn't test the actual query. A functional test with Sylius fixtures validates the real behavior.

**2. Use KernelTestCase + container to get the calculator service and EntityManager**
Rationale: No HTTP layer needed — just service container access. DAMA handles transaction rollback. We can create review entities in setUp, flush, and then call `calculate()` directly.

**3. Use Prophecy for CachedAverageRatingCalculator unit tests**
Rationale: Project convention. The cache decorator logic is pure decoration — no database needed. Prophecy provides clean mock setup for `CacheInterface` and `ReviewableRatingCalculatorInterface`.

**4. Place functional test in `tests/Functional/Calculator/`, unit test in `tests/Unit/Calculator/`**
Rationale: Mirrors existing directory conventions for each test suite.

## Risks / Trade-offs

**[Functional test depends on Sylius fixtures being loaded]** → Same constraint as existing functional tests. Documented in CLAUDE.md.

**[Need a real reviewable entity with reviews for functional test]** → Use Sylius Product entity from fixtures and create/manage ProductReview entities directly via EntityManager.
