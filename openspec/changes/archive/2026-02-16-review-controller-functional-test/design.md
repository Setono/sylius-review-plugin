## Context

The project has a test Symfony application at `tests/Application/` with all Sylius bundles registered and a MySQL test database (`setono_sylius_review_test`). Existing tests are unit-level only (form types, factories, DI extension). There are no functional tests using `WebTestCase`.

The Sylius default fixtures provide 20 orders (all in `new` state), customers, products, a channel (`FASHION_WEB`), and order items — sufficient data for testing the review controller.

## Goals / Non-Goals

**Goals:**
- Functional test coverage for `ReviewController` verifying the full HTTP request/response cycle
- Test isolation via `dama/doctrine-test-bundle` so each test can mutate database state (e.g., set order state to `fulfilled`) without affecting other tests
- Cover the key paths: 404 errors, non-reviewable orders, form rendering, and successful submission

**Non-Goals:**
- Testing form type internals (already covered by `ReviewTypeTest`)
- Testing eligibility checker logic in isolation (unit test concern)
- Testing email sending or review request processing
- Adding fixture definitions — we rely on Sylius default fixtures being pre-loaded

## Decisions

### 1. Use `dama/doctrine-test-bundle` for test isolation

**Choice**: Install `dama/doctrine-test-bundle` and register its PHPUnit extension.

**Why**: Each test can freely modify database state (UPDATE order state, INSERT reviews) and the bundle automatically rolls back all changes after each test. No manual transaction management or cleanup needed.

**Alternatives considered**:
- *Manual transaction wrapping in setUp/tearDown*: More boilerplate, error-prone with multiple entity managers
- *Using different fixture orders per test*: Fragile, limits number of test scenarios, makes tests depend on fixture data specifics

### 2. Test class extends `WebTestCase`

**Choice**: Use `Symfony\Bundle\FrameworkBundle\Test\WebTestCase` with `self::createClient()`.

**Why**: We need to test the full HTTP cycle — routing, controller dispatch, form handling, Twig rendering, and database persistence. `WebTestCase` gives us an HTTP client that exercises all of this through the real Symfony kernel.

**Alternatives considered**:
- *KernelTestCase + direct controller invocation*: Skips routing and HTTP layer, which is part of what we want to verify

### 3. Data setup strategy: query fixture data, mutate in-test

**Choice**: In each test, query an existing fixture order from the database and update its state as needed (e.g., set to `fulfilled`). The DAMA bundle rolls back mutations after each test.

**Why**: Avoids creating a complex entity graph programmatically. Sylius orders require channels, customers, currencies, locales, products, variants, order items — creating all of this from scratch is brittle and verbose.

**Approach**:
```
setUp():
  client = self::createClient()
  em = self::getContainer()->get('doctrine.orm.entity_manager')
  order = em->getRepository(Order)->findOneBy([...])  // grab a fixture order

test_form_renders():
  order.setState('fulfilled')
  em.flush()
  client.request('GET', '/en_US/review?token=' . order.getTokenValue())
  assertResponseIsSuccessful()
```

### 4. PHPUnit extension registration

**Choice**: Add the DAMA bootstrap extension to `phpunit.xml.dist` using the `<extensions>` element.

**Why**: The current `phpunit.xml.dist` uses PHPUnit 9.x format (based on `coverage` element structure). The DAMA extension needs to be registered so it wraps all tests in transactions automatically.

## Risks / Trade-offs

**[Fixture dependency]** → Tests assume Sylius default fixtures are loaded. If fixtures change between Sylius versions, tests may need updating. Mitigation: test assertions are loose (check HTTP status, presence of form elements) rather than asserting specific product names.

**[MySQL required]** → The test database is MySQL, not SQLite. CI must have MySQL available. Mitigation: this is already the case for the existing test setup — no new infrastructure requirement.

**[Template rendering]** → Functional tests render the full Twig template stack including Sylius shop layout. If template dependencies break (missing assets, undefined routes), tests will fail with rendering errors rather than controller logic errors. Mitigation: this is actually a benefit — it catches template integration issues too.
