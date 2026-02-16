## ADDED Requirements

### Requirement: Unit tests reside in tests/Unit directory
All test classes that do not require a booted Symfony kernel or database SHALL be located under `tests/Unit/`, preserving their domain subdirectory structure (e.g., `tests/Unit/Factory/`, `tests/Unit/Form/Type/`).

#### Scenario: Unit test files exist in correct location
- **WHEN** the test suite is examined
- **THEN** `tests/Unit/DependencyInjection/SetonoSyliusReviewExtensionTest.php` SHALL exist
- **AND** `tests/Unit/Factory/ReviewRequestFactoryTest.php` SHALL exist
- **AND** `tests/Unit/Form/Type/ReviewTypeTest.php` SHALL exist

#### Scenario: Unit test namespaces match directory
- **WHEN** a unit test file is in `tests/Unit/<Subdirectory>/`
- **THEN** its namespace SHALL be `Setono\SyliusReviewPlugin\Tests\Unit\<Subdirectory>`

### Requirement: Functional tests reside in tests/Functional directory
All test classes that require a booted Symfony kernel or database SHALL be located under `tests/Functional/`, preserving their domain subdirectory structure.

#### Scenario: Functional test files exist in correct location
- **WHEN** the test suite is examined
- **THEN** `tests/Functional/Controller/ReviewControllerTest.php` SHALL exist

#### Scenario: Functional test namespaces match directory
- **WHEN** a functional test file is in `tests/Functional/<Subdirectory>/`
- **THEN** its namespace SHALL be `Setono\SyliusReviewPlugin\Tests\Functional\<Subdirectory>`

### Requirement: Old test directories are removed
After migration, the original flat test directories SHALL NOT exist.

#### Scenario: Old directories cleaned up
- **WHEN** the migration is complete
- **THEN** `tests/Controller/`, `tests/DependencyInjection/`, `tests/Factory/`, and `tests/Form/` directories SHALL NOT exist

### Requirement: PHPUnit defines separate test suites
The PHPUnit configuration SHALL define a `Unit` suite and a `Functional` suite that can be run independently.

#### Scenario: Running only unit tests
- **WHEN** `vendor/bin/phpunit --testsuite=Unit` is executed
- **THEN** only tests under `tests/Unit/` SHALL be executed

#### Scenario: Running only functional tests
- **WHEN** `vendor/bin/phpunit --testsuite=Functional` is executed
- **THEN** only tests under `tests/Functional/` SHALL be executed

#### Scenario: Running all tests
- **WHEN** `vendor/bin/phpunit` is executed without a suite filter
- **THEN** both Unit and Functional tests SHALL be executed
