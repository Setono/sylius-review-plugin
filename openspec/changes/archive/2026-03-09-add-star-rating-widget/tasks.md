## 1. Update Store Review Template

- [x] 1.1 In `_store_review.html.twig`, add a `<div class="ui huge star rating" data-rating="..." data-max-rating="5" data-rating-target="...">` with 5 `<i class="icon">` children before the rating radio group
- [x] 1.2 Wrap the existing `form_widget(form.storeReview.rating)` output in a div with `style="display: none"` and give it an ID that matches the `data-rating-target`
- [x] 1.3 Set the star widget's `data-rating` attribute to the current rating value from the form data

## 2. Update Product Review Item Template

- [x] 2.1 In `_product_review_item.html.twig`, add a `<div class="ui huge star rating" data-rating="..." data-max-rating="5" data-rating-target="...">` with 5 `<i class="icon">` children before the rating radio group
- [x] 2.2 Wrap the existing `form_widget(form.productReviews[index].rating)` output in a div with `style="display: none"` and give it an ID that matches the `data-rating-target`
- [x] 2.3 Set the star widget's `data-rating` attribute to the current rating value from the form data

## 3. Add Inline JavaScript

- [x] 3.1 In `index.html.twig`, add a `{% block javascripts %}` with an inline `<script>` that initializes all `.star.rating` elements
- [x] 3.2 The script shall use `$(document).ready()`, iterate each `.star.rating`, read its `data-rating-target`, and call `.rating({ fireOnInit: true, onRate(value) { ... } })`
- [x] 3.3 The `onRate` callback shall find the radio input with the matching value inside the target container and check it

## 4. Clean Up

- [x] 4.1 Remove the `review.css` rating-choices styles that are no longer needed (the `.rating-choices` class is replaced by the star widget)
- [x] 4.2 Remove the `<link>` to `review.css` in `index.html.twig` if the file becomes empty

## 5. Verify

- [x] 5.1 Test visually using Playwright: navigate to the review page, confirm star widgets render for store and product reviews
- [x] 5.2 Test interaction: click stars and verify the correct radio button value is selected
- [x] 5.3 Test pre-selected ratings: confirm existing reviews show the correct number of filled stars on page load
