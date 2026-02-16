## Why

The two CLI commands (`ProcessCommand` and `PruneCommand`) have no test coverage. Adding functional tests using `KernelTestCase` + `CommandTester` ensures the commands are wired correctly in the container and execute without errors.

## What Changes

- Add `ProcessCommandTest` functional test using `KernelTestCase` and `CommandTester`
- Add `PruneCommandTest` functional test using `KernelTestCase` and `CommandTester`
- Both tests verify the commands are registered, execute successfully, and return exit code 0

## Capabilities

### New Capabilities

- `command-tests`: Functional tests for CLI commands using Symfony's CommandTester pattern

### Modified Capabilities

_(none)_

## Impact

- **New test files**: `tests/Functional/Command/ProcessCommandTest.php`, `tests/Functional/Command/PruneCommandTest.php`
- **No runtime code changes**
