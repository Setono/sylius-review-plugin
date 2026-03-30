## MODIFIED Requirements

### Requirement: MinimumRatingAutoApprovalChecker approves reviews meeting the minimum rating
The checker SHALL approve reviews with a rating greater than or equal to the configured minimum.

#### Scenario: Rating equals default threshold
- **WHEN** a review has rating 4 and the default threshold (4) is used
- **THEN** `shouldAutoApprove()` SHALL return `true`

#### Scenario: Rating exceeds default threshold
- **WHEN** a review has rating 5 and the default threshold (4) is used
- **THEN** `shouldAutoApprove()` SHALL return `true`

#### Scenario: Rating below default threshold
- **WHEN** a review has rating 3 and the default threshold (4) is used
- **THEN** `shouldAutoApprove()` SHALL return `false`

#### Scenario: Custom threshold with rating at boundary
- **WHEN** a review has rating 3 and a custom threshold of 3 is configured
- **THEN** `shouldAutoApprove()` SHALL return `true`

#### Scenario: Null rating treated as zero
- **WHEN** a review has a null rating
- **THEN** `shouldAutoApprove()` SHALL return `false`

## REMOVED Requirements

### Requirement: ReviewAutoApprovalListener handles both store and product reviews
**Reason**: Replaced by generic `AutoApprovalListener` that is instantiated per review type. The new listener is covered by specs in `auto-approval-configuration`.
**Migration**: Existing listener tests should be rewritten for the new `AutoApprovalListener` class, testing it with a single review type per test case.
