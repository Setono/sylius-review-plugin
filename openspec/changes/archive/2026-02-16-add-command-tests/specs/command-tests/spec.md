## ADDED Requirements

### Requirement: ProcessCommand is testable via CommandTester
A functional test SHALL verify that the `setono:sylius-review:process` command is registered in the application and executes successfully.

#### Scenario: Command executes successfully
- **WHEN** the `setono:sylius-review:process` command is executed via CommandTester
- **THEN** the command SHALL return exit code 0

### Requirement: PruneCommand is testable via CommandTester
A functional test SHALL verify that the `setono:sylius-review:prune` command is registered in the application and executes successfully.

#### Scenario: Command executes successfully
- **WHEN** the `setono:sylius-review:prune` command is executed via CommandTester
- **THEN** the command SHALL return exit code 0
