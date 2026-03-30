## Context

The plugin has two existing commands (`setono:sylius-review:process` and `setono:sylius-review:prune`). The review page is accessed via `/review?token=<order_token>`. Developers currently need to manually query the database for order tokens to test the review flow.

## Goals / Non-Goals

**Goals:**
- Provide a quick way to get review URLs for testing and development
- Group URLs by channel so multi-channel setups are easy to navigate
- Follow existing command patterns in the codebase

**Non-Goals:**
- Filtering by order state beyond "fulfilled" (the review page already handles non-reviewable orders)
- Generating URLs for orders that don't have tokens
- Any write operations or side effects

## Decisions

- **Command name**: `setono:sylius-review:urls` — follows the existing `setono:sylius-review:*` naming pattern
- **URL generation**: Use `Symfony\Component\Routing\Generator\UrlGeneratorInterface` with the channel's hostname to generate absolute URLs. The route `setono_sylius_review__review` with `?token=` query parameter is the target
- **Channel iteration**: Use `Sylius\Component\Channel\Repository\ChannelRepositoryInterface` to get all enabled channels
- **Order query**: Query fulfilled orders that have a token, limited per channel. Use the standard Sylius order repository
- **Max option**: `--max` option (default 5) controls how many URLs per channel are shown
- **Output format**: Plain text with channel name headers and URLs listed below, suitable for terminal copy-paste

## Risks / Trade-offs

- [No fulfilled orders] → Command outputs a helpful message per channel when no orders are found
- [Channel without hostname] → Skip the channel or use a fallback base URL; log a warning
