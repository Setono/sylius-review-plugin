# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that sends review requests to customers after completing orders. Review requests go through an eligibility check before being sent, and the plugin uses Symfony Workflow for state management.

## Code Standards

Follow clean code principles and SOLID design patterns when working with this codebase:
- Write clean, readable, and maintainable code
- Apply SOLID principles (Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion)
- Use meaningful variable and method names
- Keep methods and classes focused on a single responsibility
- Favor composition over inheritance
- Write code that is easy to test and extend

### Testing Requirements
- Write unit tests for all new functionality (if it makes sense)
- Follow the BDD-style naming convention for test methods (e.g., `it_should_do_something_when_condition_is_met`)
- **MUST use Prophecy for mocking** - Use the `ProphecyTrait` and `$this->prophesize()` for all mocks, NOT PHPUnit's `$this->createMock()`
- **Form testing** - Use Symfony's best practices for form testing as documented at https://symfony.com/doc/current/form/unit_testing.html
  - Extend `Symfony\Component\Form\Test\TypeTestCase` for form type tests
  - Use `$this->factory->create()` to create form instances
  - Test form submission, validation, and data transformation
- Ensure tests are isolated and don't depend on external state
- Test both happy path and edge cases

## Development Commands

### Code Quality & Testing
- `composer analyse` - Run PHPStan static analysis (level max)
- `composer check-style` - Check code style with ECS (Easy Coding Standard)
- `composer fix-style` - Fix code style issues automatically with ECS
- `composer phpunit` - Run PHPUnit tests

```bash
# Run tests
vendor/bin/phpunit                           # Full test suite
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
- **Sylius Backend Credentials**: Username: `sylius`, Password: `sylius`

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

1. **Order Completion**: `CreateReviewRequestSubscriber` listens to `sylius.order.pre_complete` and creates a `ReviewRequest` entity
2. **Processing**: `ProcessCommand` runs `ReviewRequestProcessor` which:
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

### Key Services

- `ReviewRequestProcessor`: Main processing logic with batch iteration via DoctrineBatchUtils
- `ReviewRequestEmailManager`: Handles sending review request emails via Sylius Mailer
- `ReviewRequestFactory`: Creates ReviewRequest entities from orders

### Configuration Parameters

- `setono_sylius_review.eligibility.initial_delay`: Time before first eligibility check (default: '+1 week')
- `setono_sylius_review.eligibility.maximum_checks`: Max eligibility checks before auto-cancel (default: 5)
- `setono_sylius_review.pruning.threshold`: Age threshold for pruning old requests (default: '-1 month')

### Translations

The plugin provides multilingual support through translation files in `src/Resources/translations/`:

- **Translation Domains**:
  - `messages.*` - General UI translations
  - `flashes.*` - Flash message translations (success/error messages)

Key translation keys:
- `setono_sylius_review.ui.*` - UI labels
- `setono_sylius_review.form.*` - Form field labels
