# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that sends review requests to customers after completing orders. Review requests go through an eligibility check before being sent, and the plugin uses Symfony Workflow for state management.

### Bundle Ordering

The plugin must be registered **before** `SyliusGridBundle` in `bundles.php`, otherwise you'll get a missing parameter exception for `setono_sylius_review.model.review_request.class`.

### Channel Entity Extension (Store Reviews)

Store reviews require the Channel entity to implement `ReviewableInterface`. The plugin provides `ChannelInterface` and `ChannelTrait` for this. See README.md for the full entity setup.

### CLI Commands

- `setono:sylius-review:process` — Create review requests for fulfilled orders, then process pending ones (send emails). Run daily.
- `setono:sylius-review:prune` — Remove old review requests. Run weekly/monthly.

## Code Standards

Follow clean code principles and SOLID design patterns when working with this codebase:
- Write clean, readable, and maintainable code
- Apply SOLID principles (Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion)
- Use meaningful variable and method names
- Keep methods and classes focused on a single responsibility
- Favor composition over inheritance
- Write code that is easy to test and extend

### Testing Requirements
- Tests are organized into `tests/Unit/` (no kernel/DB) and `tests/Functional/` (requires kernel + DB). Place new tests in the appropriate directory.
- Write unit tests for all new functionality (if it makes sense)
- Follow the BDD-style naming convention for test methods (e.g., `it_should_do_something_when_condition_is_met`)
- **MUST use Prophecy for mocking** - Use the `ProphecyTrait` and `$this->prophesize()` for all mocks, NOT PHPUnit's `$this->createMock()`
- **Form testing** - Use Symfony's best practices for form testing as documented at https://symfony.com/doc/6.4/form/unit_testing.html
  - Extend `Symfony\Component\Form\Test\TypeTestCase` for form type tests
  - Use `$this->factory->create()` to create form instances
  - Use `PreloadedExtension` in `getExtensions()` to register form types with mocked dependencies
  - Test form submission, validation, and data transformation
- **Functional testing** - Use Symfony's `WebTestCase` for HTTP-level controller tests
  - `dama/doctrine-test-bundle` provides automatic transaction rollback between tests (no manual cleanup needed)
  - Tests rely on Sylius default fixtures being loaded in the test database
  - Query fixture data in `setUp()` and mutate state as needed (e.g., set order state to `fulfilled`) — DAMA rolls it back after each test
  - Use `self::createClient()` for HTTP requests and Symfony's assert methods (`assertResponseIsSuccessful()`, `assertSelectorExists()`, etc.)
  - See `tests/Functional/Controller/ReviewControllerTest.php` for the reference pattern
- Ensure tests are isolated and don't depend on external state
- Test both happy path and edge cases

### Test Database Setup
Functional tests require a MySQL test database with fixtures loaded:
```bash
tests/Application/bin/console doctrine:database:create --env=test --if-not-exists
tests/Application/bin/console doctrine:schema:create --env=test
tests/Application/bin/console sylius:fixtures:load default --env=test --no-interaction
```

## Git Hooks
The repository includes git hooks in `.githooks/`. To enable them, run:
```bash
git config core.hooksPath .githooks
```
- **pre-commit**: Runs `composer fix-style` to auto-fix code style before each commit

## Development Commands

### Code Quality & Testing
- `composer analyse` - Run PHPStan static analysis (level max)
- `composer check-style` - Check code style with ECS (Easy Coding Standard)
- `composer fix-style` - Fix code style issues automatically with ECS
- `composer phpunit` - Run PHPUnit tests

```bash
# Run tests
vendor/bin/phpunit                           # Full test suite (Unit + Functional)
vendor/bin/phpunit --testsuite=Unit          # Unit tests only (fast, no DB)
vendor/bin/phpunit --testsuite=Functional    # Functional tests only (needs DB)
vendor/bin/phpunit tests/path/to/Test.php    # Single test file
vendor/bin/phpunit --filter testMethodName   # Single test method

# Rector
vendor/bin/rector process --dry-run    # Check for Rector changes
vendor/bin/rector process              # Apply Rector changes

# Dependency analysis
vendor/bin/composer-dependency-analyser

# Test application commands (from tests/Application/)
bin/console lint:container
bin/console lint:yaml ../../src/Resources
bin/console lint:twig ../../src/Resources
```

### Static Analysis

#### PHPStan Configuration
PHPStan is configured in `phpstan.neon` with:
- **Analysis Level**: max (strictest)
- **Extensions**: Auto-loaded via `phpstan/extension-installer`
  - `phpstan/phpstan-symfony` - Symfony framework integration
  - `phpstan/phpstan-doctrine` - Doctrine ORM integration
  - `phpstan/phpstan-phpunit` - PHPUnit test integration
  - `jangregor/phpstan-prophecy` - Prophecy mocking integration
- **Symfony Integration**: Uses console application loader (`tests/PHPStan/console_application.php`)
- **Doctrine Integration**: Uses object manager loader (`tests/PHPStan/object_manager.php`)
- **Exclusions**: Test application directory and Configuration.php
- **Baseline**: Generate with `composer analyse -- --generate-baseline` to track improvements

### Test Application
The plugin includes a test Symfony application in `tests/Application/` for development and testing:
- Navigate to `tests/Application/` directory
- Run `yarn install && yarn build` to build assets
- Use standard Symfony commands for the test app
- **Start Development Server**: `symfony serve --daemon --dir=tests/Application` (run from repository root)
- **Development Server**: https://127.0.0.1:8000
- **Check Server Status**: `symfony server:status --dir=tests/Application`
- **Admin Interface**: https://127.0.0.1:8000/admin
- **Admin Credentials**: `sylius:sylius`

## Bash Tools Recommendations

Use the right tool for the right job when executing bash commands:

- **Finding FILES?** → Use `fd` (fast file finder)
- **Finding TEXT/strings?** → Use `rg` (ripgrep for text search)
- **Finding CODE STRUCTURE?** → Use `ast-grep` (syntax-aware code search)
- **SELECTING from multiple results?** → Pipe to `fzf` (interactive fuzzy finder)
- **Interacting with JSON?** → Use `jq` (JSON processor)
- **Interacting with YAML or XML?** → Use `yq` (YAML/XML processor)

Examples:
- `fd "*.php" | fzf` - Find PHP files and interactively select one
- `rg "function.*validate" | fzf` - Search for validation functions and select
- `ast-grep --lang php -p 'class $name extends $parent'` - Find class inheritance patterns

## Architecture

### Core Flow

1. **Creation**: `ProcessCommand` first runs `ReviewRequestCreator` which:
   - Uses `OrderForReviewRequestDataProvider` to find fulfilled orders without review requests (within the `pruning.threshold` lookback window)
   - The data provider dispatches `QueryBuilderForReviewRequestCreationCreated` to allow query customization
   - Creates a `ReviewRequest` entity for each eligible order via `ReviewRequestFactory`
2. **Processing**: `ProcessCommand` then runs `ReviewRequestProcessor` which:
   - Fetches pending review requests ready for eligibility check
   - Runs eligibility checkers via composite pattern
   - Sends email via `ReviewRequestEmailManager` if eligible
   - Applies workflow transition to mark as completed/cancelled
3. **Pruning**: `PruneCommand` removes old review requests based on configured threshold

### State Machine

Review requests use Symfony Workflow (`setono_sylius_review__review_request`) with states:
- `pending` (initial) → `completed` or `cancelled`

Transitions defined in `src/Workflow/ReviewRequestWorkflow.php`.

### Eligibility Checkers

Implement `ReviewRequestEligibilityCheckerInterface` to create custom eligibility logic. Services implementing this interface are automatically tagged and added to the composite checker.

- `CompositeReviewRequestEligibilityChecker`: Aggregates all checkers (first ineligible result stops processing)
- `OrderFulfilledReviewRequestEligibilityChecker`: Built-in checker for order fulfillment status
- `CheckEligibilityChecksSubscriber`: Cancels request after max checks exceeded

### Review Form System

The customer-facing review form allows customers to submit store and product reviews after an order.

#### Data Flow

1. `ReviewController` validates the order token, checks reviewability, creates a `ReviewCommand` DTO, and builds the `ReviewType` form
2. `ReviewType::PRE_SET_DATA` populates the command with an existing store review (from repository) and product reviews (deduplicated by product, reusing existing reviews from repository)
3. `StoreReviewType::POST_SUBMIT` sets the order, review subject (channel), and author (customer) on new store reviews
4. On valid submission, the controller persists the store review and any product reviews that have a rating set

#### Form Types

- `ReviewType`: Composite form with `data_class` of `ReviewCommand`. Composes `StoreReviewType` + `CollectionType` of `ProductReviewType`. Uses validation group `setono_sylius_review`
- `StoreReviewType`: Extends `AbstractResourceType`. Fields: rating (expanded choices 1-5), title, comment. POST_SUBMIT listener sets order/author/reviewSubject
- `ProductReviewType`: Extends `AbstractResourceType`. Same field structure as StoreReviewType

#### Form Event Patterns (Important)

- **`StoreReviewType` uses POST_SUBMIT** (not PRE_SET_DATA) to set order, author, and reviewSubject on new entities. This is because `AbstractResourceType` uses `empty_data` to create entities during form submission — PRE_SET_DATA fires before the entity exists for new reviews
- **`ReviewType` uses PRE_SET_DATA** to populate the `ReviewCommand` with existing/new review data before the form renders. This works because `ReviewCommand` is created by the controller, not by `empty_data`

#### ReviewCommand DTO

`src/Controller/ReviewCommand.php` - Simple DTO holding a `StoreReviewInterface` and a collection of `ProductReviewInterface`. Used as the form's `data_class` to decouple form handling from entity persistence.

#### Route

- **Path**: `/{_locale}/review` (GET/POST)
- **Route name**: `setono_sylius_review__review`
- **Query parameter**: `token` (order token)

### Average Rating Calculator

The plugin replaces Sylius's default average rating calculator with a performant database-query-based implementation, decorated with an optional cache layer:

- **Decoration chain** (outermost → innermost):
  1. `CachedAverageRatingCalculator` (priority 32, **prod only**) — Symfony cache (`cache.app`), 900s TTL
  2. `AverageRatingCalculator` (priority 64) — Uses SQL `AVG()` instead of loading all reviews into memory
  3. Sylius default calculator — Fallback for non-ORM or unmapped entities

- `ConfigureAverageRatingCalculatorCachePass` removes the cache decorator when `kernel.debug` is `true`
- Both decorators fall through to the inner calculator when they can't handle the reviewable (e.g., no ORM mapping, no `ResourceInterface`, etc.)

### Auto-Approval Checkers

Product and store reviews can be auto-approved on creation via composite checker services:
- `setono_sylius_review.checker.auto_approval.store_review` — Composite checker for store reviews
- `setono_sylius_review.checker.auto_approval.product_review` — Composite checker for product reviews
- `MinimumRatingAutoApprovalChecker` — Built-in checker that auto-approves reviews above a minimum rating
- `ReviewAutoApprovalSubscriber` — Doctrine `prePersist` listener that runs the checkers

### Reviewable Order Checkers

Determine whether an order is eligible for review submission (used by `ReviewController`):
- `CompositeReviewableOrderChecker` — Aggregates all checkers
- `OrderFulfilledReviewableOrderChecker` — Checks the order is in a reviewable state
- `StoreReviewEditableReviewableOrderChecker` — Checks the store review editable period
- Tag: `setono_sylius_review.reviewable_order_checker`

### Review Request Creation

Review requests are created asynchronously via the `process` command (not during checkout):
- `OrderForReviewRequestDataProviderInterface` / `OrderForReviewRequestDataProvider`: Queries fulfilled orders without review requests using batch iteration and dispatches `QueryBuilderForReviewRequestCreationCreated` for query customization
- `ReviewRequestCreatorInterface` / `ReviewRequestCreator`: Orchestrates creation — iterates over orders from the data provider, creates review requests via factory, and persists them
- The `pruning.threshold` parameter is reused as the lookback cutoff for order eligibility

### Key Services

- `ReviewRequestCreator`: Creates review requests for fulfilled orders without one
- `ReviewRequestProcessor`: Main processing logic with batch iteration via DoctrineBatchUtils
- `ReviewRequestEmailManager`: Handles sending review request emails via Sylius Mailer
- `ReviewRequestFactory`: Creates ReviewRequest entities from orders
- `ReviewController`: Handles the customer-facing review form submission

### Mail Tester Compatibility

Compatible with [synolia/SyliusMailTesterPlugin](https://github.com/synolia/SyliusMailTesterPlugin/). The review request email subject is `setono_sylius_review__review_request`.

### Configuration Parameters

- `setono_sylius_review.eligibility.initial_delay`: Time before first eligibility check (default: '+1 week')
- `setono_sylius_review.eligibility.maximum_checks`: Max eligibility checks before auto-cancel (default: 5)
- `setono_sylius_review.pruning.threshold`: Age threshold for pruning old requests (default: '-1 month')

### Translations

The plugin provides multilingual support through translation files in `src/Resources/translations/`:

- **Translation Domains**:
  - `messages.*` - General UI translations
  - `flashes.*` - Flash message translations (success/error messages)
  - `validators.*` - Custom validation messages

Key translation keys:
- `setono_sylius_review.ui.*` - UI labels
- `setono_sylius_review.form.*` - Form field labels
- `setono_sylius_review.store_review.*` - Store review validation messages (in validators domain)
