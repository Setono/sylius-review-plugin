## 1. Create command tests

- [x] 1.1 Create `tests/Functional/Command/ProcessCommandTest.php` using `KernelTestCase` + `CommandTester` to verify the command executes successfully
- [x] 1.2 Create `tests/Functional/Command/PruneCommandTest.php` using `KernelTestCase` + `CommandTester` to verify the command executes successfully

## 2. Verify

- [x] 2.1 Run `vendor/bin/phpunit --testsuite=Functional` and confirm all tests pass
- [x] 2.2 Run `composer analyse` to confirm PHPStan passes
