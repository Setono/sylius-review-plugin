## 1. Create new directory structure

- [x] 1.1 Create `tests/Unit/DependencyInjection/`, `tests/Unit/Factory/`, `tests/Unit/Form/Type/` directories
- [x] 1.2 Create `tests/Functional/Controller/` directory

## 2. Move test files and update namespaces

- [x] 2.1 Move `tests/DependencyInjection/SetonoSyliusReviewExtensionTest.php` to `tests/Unit/DependencyInjection/` and update namespace to `Setono\SyliusReviewPlugin\Tests\Unit\DependencyInjection`
- [x] 2.2 Move `tests/Factory/ReviewRequestFactoryTest.php` to `tests/Unit/Factory/` and update namespace to `Setono\SyliusReviewPlugin\Tests\Unit\Factory`
- [x] 2.3 Move `tests/Form/Type/ReviewTypeTest.php` to `tests/Unit/Form/Type/` and update namespace to `Setono\SyliusReviewPlugin\Tests\Unit\Form\Type`
- [x] 2.4 Move `tests/Controller/ReviewControllerTest.php` to `tests/Functional/Controller/` and update namespace to `Setono\SyliusReviewPlugin\Tests\Functional\Controller`

## 3. Remove old directories

- [x] 3.1 Remove empty directories: `tests/Controller/`, `tests/DependencyInjection/`, `tests/Factory/`, `tests/Form/`

## 4. Update PHPUnit configuration

- [x] 4.1 Replace the single test suite in `phpunit.xml.dist` with separate `Unit` and `Functional` suites

## 5. Verify

- [x] 5.1 Run `vendor/bin/phpunit --testsuite=Unit` and confirm only unit tests execute
- [x] 5.2 Run `vendor/bin/phpunit --testsuite=Functional` and confirm only functional tests execute
- [x] 5.3 Run `vendor/bin/phpunit` and confirm all tests pass
- [x] 5.4 Run `composer analyse` to confirm PHPStan passes with updated namespaces
