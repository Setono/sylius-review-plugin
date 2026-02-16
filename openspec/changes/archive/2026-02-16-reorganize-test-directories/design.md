## Context

The test suite has 4 test files in a flat directory structure under `tests/`. One is a functional test (`ReviewControllerTest` extending `WebTestCase`, requiring kernel + database), and three are unit tests (extending `TestCase`, `TypeTestCase`, and `AbstractExtensionTestCase`). There is no way to run unit tests independently of functional tests.

Supporting infrastructure (`tests/Application/` and `tests/PHPStan/`) must remain untouched.

## Goals / Non-Goals

**Goals:**
- Separate tests into `tests/Unit/` and `tests/Functional/` directories
- Update namespaces to match new directory paths
- Configure PHPUnit with separate test suites for independent execution

**Non-Goals:**
- Changing test logic or adding new tests
- Modifying the test application or PHPStan configuration
- Adding a third category (e.g., `Integration/`)

## Decisions

### 1. Classification of DependencyInjection test as Unit

The `SetonoSyliusReviewExtensionTest` extends `AbstractExtensionTestCase` which creates a partial container but does not boot the kernel or require a database. It is classified as a unit test.

**Alternative**: Create an `Integration/` tier. Rejected — adds complexity for a single test, and the DI test behaves like a unit test in practice (fast, no external dependencies).

### 2. Preserve subdirectory structure within Unit/Functional

Tests keep their domain-based subdirectories (e.g., `Unit/Factory/`, `Unit/Form/Type/`). This mirrors the `src/` structure and scales as new tests are added.

**Alternative**: Flatten into `Unit/ReviewRequestFactoryTest.php`. Rejected — loses organizational clarity as test count grows.

### 3. Two PHPUnit test suites

Define `Unit` and `Functional` suites in `phpunit.xml.dist`. The existing `<directory>tests</directory>` single-suite is replaced.

The DAMA DoctrineTestBundle extension remains globally configured — it's a no-op for unit tests and simplifies configuration.

### 4. Explicit directory exclusions in test suites

Each suite points at its specific directory (`tests/Unit` or `tests/Functional`), so `tests/Application/` and `tests/PHPStan/` are naturally excluded without needing `<exclude>` directives.

## Risks / Trade-offs

- **[Low] Old test paths in developer muscle memory** → One-time adjustment, mitigated by clear commit message
- **[Low] Composer autoload cache** → Run `composer dump-autoload` after move to regenerate; PSR-4 mapping doesn't change
