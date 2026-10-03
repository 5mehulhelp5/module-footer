# Magento 2 Footer

Panth Footer replaces the default Magento storefront footer with a configurable one. It adds a newsletter section, a grid of two to four footer columns (logo and about text, two link columns, and a contact column), a bottom bar with copyright text, payment icons and bottom links, and a floating back-to-top button. All content is entered in the admin configuration; no template editing is needed for day-to-day changes.

The module ships two template sets. The base layout uses vanilla JavaScript templates that work on Luma; when the Hyva theme is active the `default_hyva` layout handle switches the newsletter and back-to-top blocks to Alpine.js templates. It is used by store owners and content teams who want to manage footer content from the admin.

Product page: [kishansavaliya.com/magento-2-footer.html](https://kishansavaliya.com/magento-2-footer.html)

## Features

- Footer grid with a "Footer Layout" of 2, 3 or 4 columns; each column can be enabled or disabled separately
- Column 1 shows an optional title, the store logo, an about text and social media icons
- Columns 2 and 3 are link lists entered as JSON arrays in the admin; each link can set a `target`
- Column 4 shows a phone number (rendered as a `tel:` link), an email address (rendered as a `mailto:` link), a physical address and working hours
- Social media links for Facebook, Twitter/X, Instagram, LinkedIn, YouTube and Pinterest, rendered as inline SVG icons; an empty URL hides that icon
- Newsletter section with configurable title, subtitle, benefits list, placeholder and button text; the form posts to Magento's own `newsletter/subscriber/new` action with `fetch()` and shows Magento's own result message without a page reload
- Back-to-top button at the bottom right or bottom left that appears after the page is scrolled more than 300 pixels and scrolls smoothly to the top. It is not rendered on the cart and checkout pages (`checkout_cart_index` and `checkout_index_index` remove it) so it never covers the order buttons or form fields, and it uses z-index 30. Floating elements share one bottom stack per side: back-to-top sits at the bottom, the WhatsApp button above it, the EU withdrawal tab (phones and tablets) above that. Each element adds `--panth-bottom-bar-offset` (bottom notification bars), the slot variables `--panth-float-slot-btt-<side>` and `--panth-float-slot-wa-<side>` of the elements below it, and `--panth-float-edge` (24px, 16px below 768px). The button hides while a drawer, modal, mini cart, open search or mobile menu is shown. Up to 1024px the footer adds bottom padding equal to the tallest float column plus 8px, so the last footer links can always be scrolled clear of the floats.
- Bottom bar with copyright text (`{{year}}` is replaced with the current year), Visa, Mastercard, PayPal and Amex icons, and a row of bottom links entered as JSON
- Admin JSON fields have "Beautify JSON", "Minify JSON" and "Validate JSON" buttons and are pretty-printed on page load
- Every setting is available at default, website and store view scope
- Removes the stock Luma and Hyva footer blocks (footer links, newsletter form, store switcher, copyright, report bugs link) from the layout
- Colours are read from CSS variables that fall back to the values in `etc/theme-config.json`; the module registers itself with the "Panth_Core" theme configuration so the colours can be managed centrally

## Compatibility

| Platform | Versions |
|---|---|
| Magento Open Source | 2.4.4 to 2.4.8 (as published on the product page) |
| Adobe Commerce | 2.4.4 to 2.4.8 (as published on the product page) |
| PHP | 8.1, 8.2, 8.3, 8.4 (`~8.1.0||~8.2.0||~8.3.0||~8.4.0` in `composer.json`) |
| Themes | Hyva and Luma |

Composer constraints on Magento packages: `magento/framework` ^103.0, `magento/module-store` ^101.0, `magento/module-theme` ^101.0, `magento/module-cms` ^104.0, `magento/module-config` ^101.0.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8
- PHP 8.1, 8.2, 8.3 or 8.4
- `mage2kishan/module-core` ^1.0 (module "Panth_Core"); it is declared in `require` and Composer installs it automatically. The module's `etc/module.xml` sequences after "Panth_Core", "Magento_Store", "Magento_Theme" and "Magento_Cms".
- No suggested packages are declared.

## Installation

```bash
composer require mage2kishan/module-footer
bin/magento module:enable Panth_Core Panth_Footer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

`setup:di:compile` is only needed in production mode. `setup:static-content:deploy -f` is listed because the module ships a LESS file under `view/frontend/web`.

Check that the module is enabled:

```bash
bin/magento module:status Panth_Footer
```

## Configuration

Admin path: Stores > Configuration > Panth Extensions > Footer Configuration. The same page is reachable from the admin menu entry Panth Extensions > Footer > Configuration, which the module adds in `etc/adminhtml/menu.xml`.

All settings live under the config path prefix `panth_footer/`. Defaults come from `etc/config.xml`.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| Enable Custom Footer | Yes | Renders the Panth footer template. The stock footer blocks are removed and the footer CSS is loaded only through the `panth_footer_enabled` layout handle, which is added while this setting is Yes. When set to No the theme's default footer (Luma links, newsletter and copyright, or the Hyva footer) is shown again. The back-to-top button has its own setting. |
| Footer Layout | 4 Columns | Number of columns rendered (2, 3 or 4). Only columns 1 to the chosen number are rendered, so a 3 column layout never shows column 4. |

Config paths: `panth_footer/general/enabled`, `panth_footer/general/layout`.

### Column 1 - Logo and About

| Setting | Default | What it does |
|---|---|---|
| Enable Column 1 | Yes | Shows or hides column 1. |
| Show Logo | Yes | Shows the store logo (the header logo set under Content > Design > Configuration) linked to the home page. Nothing is shown when no logo file can be resolved. |
| Column Title | (empty) | Heading printed under the logo. Empty hides it. |
| About Text | "Your trusted partner for quality products and exceptional service. Shop with confidence." | Paragraph under the logo. The tags `a`, `b`, `strong`, `em`, `i`, `u`, `small`, `span` and `br` are kept; other HTML is escaped. |
| Show Social Media Icons | Yes | Shows the "Follow Us" label and the social icons for every non-empty URL in "Social Media Links". |

Config paths: `panth_footer/column1/enabled`, `panth_footer/column1/show_logo`, `panth_footer/column1/title`, `panth_footer/column1/content`, `panth_footer/column1/show_social`.

### Column 2 - Quick Links

| Setting | Default | What it does |
|---|---|---|
| Enable Column 2 | Yes | Shows or hides column 2. |
| Column Title | Quick Links | Heading of the column. |
| Links (JSON Format) | My Account, Shopping Cart, Search Terms, Advanced Search | JSON array of objects with `title`, `url` and an optional `target` (`_self`, `_blank`, `_parent` or `_top`; other values are ignored). Links with `_blank` also get `rel="noopener noreferrer"`. Only relative URLs and `http`, `https`, `mailto` and `tel` URLs are rendered; any other scheme is replaced with `#`. Entries without a title are skipped. Saving the section rejects invalid JSON (or a JSON value that is not a list) with an error message naming the field; valid JSON is stored compact, so saving unchanged values does not rewrite them. |

Config paths: `panth_footer/column2/enabled`, `panth_footer/column2/title`, `panth_footer/column2/links`.

### Column 3 - Customer Service

| Setting | Default | What it does |
|---|---|---|
| Enable Column 3 | Yes | Shows or hides column 3. |
| Column Title | Customer Service | Heading of the column. |
| Links (JSON Format) | Contact Us, Customer Service, About Us, Orders and Returns | Same format and behaviour as column 2. |

Config paths: `panth_footer/column3/enabled`, `panth_footer/column3/title`, `panth_footer/column3/links`.

### Column 4 - Contact Information

| Setting | Default | What it does |
|---|---|---|
| Enable Column 4 | Yes | Shows or hides column 4. |
| Column Title | Contact | Heading of the column. |
| Show Contact Information | Yes | Renders phone, email, address and working hours with Font Awesome icons. When No, column 4 is not rendered. |
| Phone Number | 1-800-SHOP-NOW | Rendered as a `tel:` link; characters other than digits and `+` are stripped from the link target. |
| Email Address | support@store.com | Rendered as a `mailto:` link. |
| Physical Address | 123 Commerce Street, Business City, BC 12345 | Plain text; `<br>` tags are kept. |
| Working Hours | Mon-Fri: 9AM-6PM | Plain text with a clock icon; `<br>` tags are kept. |

Config paths: `panth_footer/column4/enabled`, `panth_footer/column4/title`, `panth_footer/column4/show_contact_info`, `panth_footer/column4/phone`, `panth_footer/column4/email`, `panth_footer/column4/address`, `panth_footer/column4/working_hours`.

### Social Media Links

| Setting | Default | What it does |
|---|---|---|
| Facebook URL | https://facebook.com | Icon link, opened in a new tab. Empty hides the icon. |
| Twitter/X URL | https://twitter.com | Same. |
| Instagram URL | https://instagram.com | Same. |
| LinkedIn URL | (empty) | Same. |
| YouTube URL | (empty) | Same. |
| Pinterest URL | (empty) | Same. |

Only relative, `http` and `https` URLs are rendered; a URL with any other scheme hides that icon.

Config paths: `panth_footer/social/facebook`, `panth_footer/social/twitter`, `panth_footer/social/instagram`, `panth_footer/social/linkedin`, `panth_footer/social/youtube`, `panth_footer/social/pinterest`.

### Newsletter Section

| Setting | Default | What it does |
|---|---|---|
| Enable Newsletter Section | Yes | Renders the newsletter block above the footer columns. |
| Section Title | Stay Connected | Heading. If empty the template falls back to "Stay in the Loop". |
| Section Subtitle | Get exclusive offers and updates delivered to your inbox | Text under the heading, with a built-in fallback when empty. |
| Benefits Text (JSON) | ["Exclusive offers","New arrivals first","Member-only sales"] | JSON array of strings shown with a check icon under the form. |
| Email Placeholder Text | Enter your email address | Placeholder of the email input. |
| Subscribe Button Text | Subscribe | Button label. |

Config paths: `panth_footer/newsletter/enabled`, `panth_footer/newsletter/title`, `panth_footer/newsletter/subtitle`, `panth_footer/newsletter/benefits`, `panth_footer/newsletter/placeholder_text`, `panth_footer/newsletter/button_text`.

### Back to Top Button

| Setting | Default | What it does |
|---|---|---|
| Enable Back to Top Button | Yes | Renders the floating button in `before.body.end`. |
| Button Position | Bottom Right | Places the button at the bottom right or bottom left of the window in both templates. An older stored value of `1` is listed as "Bottom Right (legacy value)", renders at the bottom right and is kept when the section is saved. |

Config paths: `panth_footer/back_to_top/enabled`, `panth_footer/back_to_top/position`.

### Bottom Bar

| Setting | Default | What it does |
|---|---|---|
| Show Payment Icons | Yes | Shows Visa, Mastercard, PayPal and Amex icons from Font Awesome. |
| Copyright Text | "&copy; {{year}} Your Store. Crafted with care for an exceptional shopping experience." | `{{year}}` is replaced with the current year. The tags `a`, `b`, `strong`, `em`, `i`, `u`, `small`, `span` and `br` are kept; other HTML is escaped. HTML entities such as `&copy;` stay as typed when the section is saved (the same applies to the About Text field). |
| Show Footer Bottom Links | Yes | Shows the bottom link row. |
| Footer Bottom Links (JSON) | Privacy Policy, Enable Cookies, Contact Us | Same format and URL rules as the column links, including `target`. |

Config paths: `panth_footer/bottom/show_payment_icons`, `panth_footer/bottom/copyright_text`, `panth_footer/bottom/show_footer_links`, `panth_footer/bottom/footer_links`.

The group comments in the admin note that footer, newsletter and back-to-top colours are not set here; they are managed through the Theme Customizer provided by "Panth_Core".

## Usage

### Layout changes

`view/frontend/layout/default.xml` does the following on every frontend page:

- Removes these blocks and containers: `footer_links`, `cms_footer_links_container`, `form.subscribe`, `newsletter_head_components`, `store_switcher`, `copyright`, `report.bugs`, `absolute_footer`, `footer-content` and `footer.newsletter`.
- Adds `panth.footer` (template `Panth_Footer::footer.phtml`) to the `footer` container, with the children `panth.footer.newsletter` (template `Panth_Footer::luma/newsletter.phtml`) and `panth.footer.logo` (alias `logo`, class `Magento\Theme\Block\Html\Header\Logo`, template `Panth_Footer::logo.phtml`).
- Adds `panth.footer.back_to_top` (template `Panth_Footer::luma/back-to-top.phtml`) to `before.body.end`.
- Adds Font Awesome 6.5.1 from cdnjs to the page head. The stylesheet carries a subresource integrity hash (`sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==`) and `crossorigin="anonymous"`, so the browser refuses a modified file.
- Adds `Panth_Footer::css/footer-fonts.css`, which declares the DM Sans font (weights 400 to 800, `font-display: swap`) from the self-hosted files `view/frontend/web/fonts/dm-sans-latin.woff2` and `dm-sans-latin-ext.woff2`. No request is made to Google Fonts. DM Sans is licensed under the SIL Open Font License 1.1; the license text is in `view/frontend/web/fonts/OFL.txt`.

`view/frontend/layout/default_hyva.xml` switches `panth.footer.newsletter` to `Panth_Footer::hyva/newsletter.phtml` and `panth.footer.back_to_top` to `Panth_Footer::hyva/back-to-top.phtml`. According to the layout comment this handle is added only when the Hyva theme is active.

### Managing content

Column links and bottom links are JSON arrays such as `[{"title":"Contact Us","url":"/contact"},{"title":"Blog","url":"https://blog.example.com","target":"_blank"}]`. Newsletter benefits are a JSON array of strings. The admin fields for these settings use the `JsonBeautifier` renderer, which adds format, minify and validate buttons.

### Newsletter behaviour

Both newsletter templates submit the form with `fetch()` to `newsletter/subscriber/new`, including the form key and the `X-Requested-With: XMLHttpRequest` header. Magento's controller always answers with a redirect and stores its result message in the `mage-messages` cookie. After the request the templates read that cookie, show the message text (success styling only when every message is a success message) and remove the cookie so the message is not shown again on the next page. When no message is found, or the request fails, "Something went wrong. Please try again." is shown. Subscribers are stored by Magento's newsletter module.

### Templates that can be overridden

- `view/frontend/templates/footer.phtml` (columns and bottom bar, shared by both themes)
- `view/frontend/templates/logo.phtml` (footer logo)
- `view/frontend/templates/luma/newsletter.phtml` and `view/frontend/templates/luma/back-to-top.phtml` (vanilla JavaScript, inline styles)
- `view/frontend/templates/hyva/newsletter.phtml` and `view/frontend/templates/hyva/back-to-top.phtml` (Alpine.js and Tailwind classes)

### Styling

`view/frontend/web/css/source/_module.less` contains the Luma overrides and is compiled into Luma's `styles-m.css` and `styles-l.css` by Magento's LESS pipeline.

## Developer Notes

- Module name: `Panth_Footer`
- Composer package: `mage2kishan/module-footer`
- Namespace: `Panth\Footer`
- `Panth\Footer\ViewModel\FooterData`: view model passed to the templates as `footer_view_model`; exposes `isEnabled()`, `getLayout()`, `getGridClasses()`, `getColumnData(int)`, `getSocialLinks()`, `getSocialIcon(string)`, `getCopyrightText()`, `showPaymentIcons()`, `showFooterLinks()`, `getFooterLinks()`, `isNewsletterEnabled()`, `getNewsletterTitle()`, `getNewsletterSubtitle()`, `getNewsletterBenefits()`, `getNewsletterPlaceholder()`, `getNewsletterButtonText()`, `isBackToTopEnabled()`, `getBackToTopPosition()` and `getAllowedHtmlTags()`
- `Panth\Footer\Helper\Data`: reads the `panth_footer/*` configuration at store scope, parses the JSON fields with `Magento\Framework\Serialize\Serializer\Json`, and provides colour getters that read `theme_customizer/*` paths with hard-coded fallbacks
- `Panth\Footer\Block\Adminhtml\Form\Field\JsonBeautifier`: admin field renderer for the JSON textareas
- `Panth\Footer\Model\Config\Source\Layout` and `Panth\Footer\Model\Config\Source\Position`: option sources for "Footer Layout" and "Button Position"
- `Panth\Footer\Model\Config\Backend\Enabled`: backend model of "Enable Custom Footer"; it extends `Magento\Framework\App\Config\Value` without additional logic
- `Panth\Footer\Model\Config\Backend\Json`: backend model of the four JSON fields; validates the value on save and stores it compact
- `Panth\Footer\Observer\AddEnabledHandle`: frontend `layout_load_before` observer that adds the `panth_footer_enabled` handle (stock footer removal and footer CSS) while "Enable Custom Footer" is Yes
- `etc/frontend/di.xml` adds `Panth_Footer` to the `registeredModules` argument of `Panth\Core\ViewModel\ThemeConfig`; `etc/theme-config.json` holds the default colour values
- ACL resources: `Panth_Footer::footer` ("Footer") and `Panth_Footer::config` ("Configuration"), both under `Panth_Core::panth_extensions`
- No plugins, preferences, controllers, routes or cron jobs are declared
- No `db_schema.xml`; the module creates no database tables

## Uninstallation

```bash
bin/magento module:disable Panth_Footer
composer remove mage2kishan/module-footer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The module creates no tables. Saved configuration values under `panth_footer/*` remain in `core_config_data` after removal; delete them manually if they are no longer wanted. `mage2kishan/module-core` stays installed if other modules depend on it.

## Support

- Product page: [kishansavaliya.com/magento-2-footer.html](https://kishansavaliya.com/magento-2-footer.html)
- Contact form: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- GitHub issues: [github.com/mage2sk/module-footer/issues](https://github.com/mage2sk/module-footer/issues)

## Documentation

[USER_GUIDE.md](USER_GUIDE.md) covers installation, checking that the module is active, general settings, configuring each column, social media links, the newsletter section, the back-to-top button, the bottom bar, colour customization and troubleshooting.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [kishansavaliya.com/magento-extensions.html](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [github.com/mage2sk/module-footer](https://github.com/mage2sk/module-footer)
- Packagist: [packagist.org/packages/mage2kishan/module-footer](https://packagist.org/packages/mage2kishan/module-footer)
