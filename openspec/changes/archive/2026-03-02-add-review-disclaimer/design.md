## Context

The review submission form currently allows customers to submit store and product reviews without any notice that their review will be publicly visible. The form is rendered in `shop/review/index.html.twig`.

## Goals / Non-Goals

**Goals:**
- Add a translatable disclaimer text above the submit button
- Inform customers that their review will be publicly visible

**Non-Goals:**
- Requiring explicit consent via a checkbox
- Storing any acceptance record
- Making the disclaimer configurable per channel

## Decisions

### 1. Template-only change

**Decision**: Add the disclaimer as a simple translated text element in the Twig template. No form type, DTO, or validation changes needed.

**Rationale**: The disclaimer is purely informational — by submitting the form, the customer implicitly accepts. This keeps the change minimal and avoids unnecessary complexity.

### 2. Translation key placement

**Decision**: Use a single translation key under `setono_sylius_review.ui.disclaimer` in the `messages` domain.

**Rationale**: Follows the existing convention for UI text in this plugin. Stores can override the text via standard Sylius translation overrides.

## Risks / Trade-offs

- **[No explicit consent]** → Acceptable. The disclaimer is informational. Stores requiring explicit consent can override the template to add a checkbox.
