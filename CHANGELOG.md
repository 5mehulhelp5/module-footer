# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.14] - 2026-10-03

### Fixed
- Turning "Enable Custom Footer" off no longer leaves the store without a footer. The stock footer blocks are removed, and Font Awesome plus the footer fonts are loaded, only while the setting is on (new `panth_footer_enabled` layout handle added by a frontend observer), so with the setting off Luma shows its own footer links, newsletter and copyright again and Hyva shows the theme footer.
- The default footer links no longer point to pages that do not exist on a standard store (`/shipping`, `/faq`, `/cookies`, `/catalog/category/view/id/3` and sample-data category URLs). Column 2 ("Quick Links") now links to My Account, Shopping Cart, Search Terms and Advanced Search, column 3 to Contact Us, Customer Service, About Us and Orders and Returns, and the bottom row to the Privacy Policy, Enable Cookies and Contact Us pages. Stores that already saved their own links keep them.
- The JSON fields (column 2 and 3 links, newsletter benefits, bottom links) are validated on save: invalid JSON or a value that is not a list is rejected with an error naming the field instead of silently emptying the links on the storefront. Valid JSON is stored compact, so saving the section without changes no longer rewrites these values with the pretty-printed admin formatting.
- Copyright text and bottom links meet WCAG AA contrast (opacity raised from 0.6 to 0.85, about 5.4:1 on the default footer background) and the payment icons meet the 3:1 non-text contrast; bottom links are 14 px on Luma as on Hyva.
- On phones and tablets up to 1024 px the bottom bar stacks and centres copyright, payment icons and links, and the bottom links wrap as whole words instead of breaking into two lines ("Privacy / Policy"). On Luma the bottom bar no longer stays in one squeezed row on phones (an over-broad `:has()` selector for the payment icon row also matched the bottom bar).
- Social icons are 44 x 44 px and footer links up to 1024 px are at least 44 px wide as well as tall, so every footer tap target reaches 44 px; the 1024 px tablet width now gets the same 44 px link height as smaller screens.
- The newsletter email field has an accessible name and `autocomplete="email"`, and the success or error message is announced to screen readers (`role="status"`, `aria-live="polite"`). The newsletter input and button use the footer font on Luma instead of the Luma form font.
- The payment icons are announced as one image ("Accepted payment methods: Visa, Mastercard, PayPal, American Express"); decorative contact and back-to-top icons are hidden from screen readers.
- With "Show Contact Information" set to No, column 4 is no longer rendered as an orphan "Contact" heading, and the column grid is not output when every column is disabled.
