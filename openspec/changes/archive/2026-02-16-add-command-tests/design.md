## Context

`ProcessCommand` calls `ReviewRequestProcessorInterface::process()` and optionally sets a console logger. `PruneCommand` calls `ReviewRequestRepositoryInterface::removeCancelled()` and `removeBefore()`. Both are registered as services in the container. Neither command produces console output on success.

## Goals / Non-Goals

**Goals:**
- Verify commands are registered and findable via the console Application
- Verify commands execute without errors and return exit code 0

**Non-Goals:**
- Testing the business logic of the processor or repository (covered by separate tests)
- Testing verbose output / logger integration

## Decisions

### 1. Use KernelTestCase + CommandTester (not WebTestCase)

Commands don't need an HTTP client. `KernelTestCase` boots the kernel and gives access to the container. `CommandTester` wraps the command for testing without a real console. This follows Symfony's recommended pattern.

### 2. Place tests in tests/Functional/Command/

These are functional tests — they boot the kernel and use the real service container. They belong in `tests/Functional/` per the project's test directory convention.

### 3. Tests require database with fixtures

Both commands interact with the database through their dependencies. The test database with fixtures must be available, matching the existing functional test setup.

## Risks / Trade-offs

- **[Low] Empty result set** → Commands run against fixture data which may have no pending review requests. This is fine — the test verifies execution completes without error, not business outcomes.
