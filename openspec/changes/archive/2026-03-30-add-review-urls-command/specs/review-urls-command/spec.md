## ADDED Requirements

### Requirement: Command outputs review URLs grouped by channel
The command `setono:sylius-review:urls` SHALL iterate over all enabled channels and output review page URLs for fulfilled orders, grouped by channel name.

#### Scenario: Multiple channels with fulfilled orders
- **WHEN** the command is run and there are fulfilled orders across multiple channels
- **THEN** the output SHALL display each channel name as a header followed by review URLs for that channel's orders

#### Scenario: Channel with no fulfilled orders
- **WHEN** a channel has no fulfilled orders with tokens
- **THEN** the command SHALL display a message indicating no orders were found for that channel

### Requirement: Command limits URLs per channel
The command SHALL accept a `--max` option (default: 5) that limits how many URLs are displayed per channel.

#### Scenario: Default limit
- **WHEN** the command is run without `--max` and a channel has 10 fulfilled orders
- **THEN** only 5 URLs SHALL be displayed for that channel

#### Scenario: Custom limit
- **WHEN** the command is run with `--max=3`
- **THEN** at most 3 URLs SHALL be displayed per channel

### Requirement: Command generates absolute URLs
The command SHALL generate absolute URLs using the review route and order token.

#### Scenario: URL format
- **WHEN** a review URL is generated for an order with token `abc123`
- **THEN** the URL SHALL point to the `setono_sylius_review__review` route with `token=abc123` as a query parameter

### Requirement: Command uses ORMTrait with injected order class
The command SHALL use the `ORMTrait` and accept a configurable order class string (via `%sylius.model.order.class%`) instead of injecting `EntityManagerInterface` directly. This follows the established plugin pattern for Doctrine entity access.

#### Scenario: Order entity manager resolution
- **WHEN** the command queries for orders
- **THEN** it SHALL resolve the entity manager via `ORMTrait::getManager()` using the injected order class string
