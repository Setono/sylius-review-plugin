## 1. Update services.xml

- [x] 1.1 Rename all eligible service IDs from string-based to FQCN and drop `class` attributes (per the mapping in design.md)
- [x] 1.2 Add interface aliases (`ReviewRequestCreatorInterface`, `ReviewRequestProcessorInterface`, `ReviewRequestEmailManagerInterface`, `ReviewRequestEligibilityCheckerInterface`)
- [x] 1.3 Update all internal `<argument type="service" id="..."/>` references to use the new FQCN IDs

## 2. Update compiler passes and extension

- [x] 2.1 Update `SetonoSyliusReviewPlugin::build()` to use FQCN for the eligibility checker composite compiler pass
- [x] 2.2 Update `SetonoSyliusReviewExtension::registerEmailFormType()` to use FQCN for the service ID

## 3. Update tests

- [x] 3.1 Update `tests/Functional/Creator/ReviewRequestCreatorTest.php` to use the FQCN service ID

## 4. Verify

- [x] 4.1 Run `composer analyse` — passes
- [x] 4.2 Run `vendor/bin/phpunit` — all tests pass
