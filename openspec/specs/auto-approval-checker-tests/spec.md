### Requirement: CompositeAutoApprovalChecker approves when no checkers are registered
The composite checker SHALL return `true` when no inner checkers have been added.

#### Scenario: No checkers registered
- **WHEN** the composite checker has no inner checkers
- **THEN** `shouldAutoApprove()` SHALL return `true`

### Requirement: CompositeAutoApprovalChecker approves when all checkers approve
The composite checker SHALL return `true` when every registered checker returns `true`.

#### Scenario: All checkers approve
- **WHEN** two checkers are registered and both return `true`
- **THEN** `shouldAutoApprove()` SHALL return `true`

### Requirement: CompositeAutoApprovalChecker rejects on first failing checker
The composite checker SHALL return `false` as soon as any checker returns `false`, without calling subsequent checkers.

#### Scenario: First checker rejects
- **WHEN** the first checker returns `false`
- **THEN** `shouldAutoApprove()` SHALL return `false`
- **AND** the second checker SHALL NOT be called

#### Scenario: Second checker rejects after first approves
- **WHEN** the first checker returns `true` and the second returns `false`
- **THEN** `shouldAutoApprove()` SHALL return `false`

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
