## Why

The test suite mixes functional tests (requiring a booted kernel and database) with unit tests (pure logic, no infrastructure) in a flat directory structure. Splitting them into `Functional/` and `Unit/` directories enables running each suite independently, giving faster feedback during development and clearer CI pipeline separation.

## What Changes

- Move `tests/Controller/ReviewControllerTest.php` into `tests/Functional/Controller/`
- Move `tests/DependencyInjection/SetonoSyliusReviewExtensionTest.php` into `tests/Unit/DependencyInjection/`
- Move `tests/Factory/ReviewRequestFactoryTest.php` into `tests/Unit/Factory/`
- Move `tests/Form/Type/ReviewTypeTest.php` into `tests/Unit/Form/Type/`
- Update namespaces to reflect new directory paths
- Configure PHPUnit with separate `Unit` and `Functional` test suites
- Remove empty old directories

## Capabilities

### New Capabilities

- `test-directory-structure`: Organizing tests into `Unit/` and `Functional/` directories with matching namespaces and separate PHPUnit suites

### Modified Capabilities

_(none — no existing spec-level behavior changes)_

## Impact

- **Test files**: 4 test files moved, namespaces updated
- **PHPUnit config**: `phpunit.xml.dist` updated with two test suites
- **CI/automation**: Any scripts referencing old test paths may need updating (none detected in repo)
- **No runtime code changes**: Only test infrastructure is affected
