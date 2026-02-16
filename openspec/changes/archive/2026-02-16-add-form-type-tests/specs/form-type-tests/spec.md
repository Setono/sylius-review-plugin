## ADDED Requirements

### Requirement: StoreReviewType has correct form fields
`StoreReviewType` SHALL build a form with three fields: `rating` (ChoiceType, expanded, choices 1-5), `title` (TextType), and `comment` (TextareaType). All fields SHALL be optional.

#### Scenario: Form contains expected fields
- **WHEN** a `StoreReviewType` form is created with a valid `order` option
- **THEN** the form SHALL have children named `rating`, `title`, and `comment`

#### Scenario: Rating field uses expanded choices
- **WHEN** the `rating` field is inspected
- **THEN** it SHALL be a ChoiceType with `expanded` true, `multiple` false, and choices mapping labels `'1'`-`'5'` to integers `1`-`5`

### Requirement: StoreReviewType POST_SUBMIT listener sets order, subject, and author
The `POST_SUBMIT` event listener SHALL set the order, review subject (channel), and author (customer) on the `StoreReviewInterface` entity.

#### Scenario: Listener sets order on store review
- **WHEN** the form is submitted with a valid `order` option
- **THEN** `StoreReviewInterface::setOrder()` SHALL be called with the order from the options

#### Scenario: Listener sets review subject from order channel
- **WHEN** the form is submitted and the order's channel implements `ChannelInterface`
- **THEN** `StoreReviewInterface::setReviewSubject()` SHALL be called with that channel

#### Scenario: Listener sets author from order customer
- **WHEN** the form is submitted and the order's customer implements `ReviewerInterface`
- **THEN** `StoreReviewInterface::setAuthor()` SHALL be called with that customer

#### Scenario: Listener does not set subject when channel is not ChannelInterface
- **WHEN** the form is submitted and the order's channel does not implement `Setono\SyliusReviewPlugin\Model\ChannelInterface`
- **THEN** the review subject SHALL remain unset

### Requirement: StoreReviewType requires order option
`StoreReviewType` SHALL require an `order` option of type `OrderInterface`.

#### Scenario: Missing order option raises error
- **WHEN** a `StoreReviewType` form is created without the `order` option
- **THEN** an exception SHALL be thrown

### Requirement: StoreReviewType has correct block prefix
The form type SHALL return `setono_sylius_review_store_review` as its block prefix.

#### Scenario: Block prefix value
- **WHEN** a `StoreReviewType` form is created
- **THEN** `getBlockPrefix()` SHALL return `'setono_sylius_review_store_review'`

### Requirement: ProductReviewType has correct form fields
`ProductReviewType` SHALL build a form with three fields: `rating` (ChoiceType, expanded, choices 1-5), `title` (TextType), and `comment` (TextareaType). All fields SHALL be optional.

#### Scenario: Form contains expected fields
- **WHEN** a `ProductReviewType` form is created
- **THEN** the form SHALL have children named `rating`, `title`, and `comment`

#### Scenario: Rating field uses expanded choices
- **WHEN** the `rating` field is inspected
- **THEN** it SHALL be a ChoiceType with `expanded` true, `multiple` false, and choices mapping labels `'1'`-`'5'` to integers `1`-`5`

### Requirement: ProductReviewType maps submitted data to entity
Submitting data through the form SHALL map field values onto the underlying data object.

#### Scenario: Form submission populates entity fields
- **WHEN** the form is submitted with `rating`, `title`, and `comment` values
- **THEN** the underlying data object SHALL have those values set

### Requirement: ProductReviewType has correct block prefix
The form type SHALL return `setono_sylius_review_product_review` as its block prefix.

#### Scenario: Block prefix value
- **WHEN** a `ProductReviewType` form is created
- **THEN** `getBlockPrefix()` SHALL return `'setono_sylius_review_product_review'`
