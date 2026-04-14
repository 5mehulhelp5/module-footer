# Panth Footer

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange)]()
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue)]()
[![Hyva + Luma](https://img.shields.io/badge/Theme-Hyva%20%2B%20Luma-brightgreen)]()

**Fully configurable footer module for Magento 2** with native support
for both Hyva (Alpine.js) and Luma (vanilla JS) themes. Replace the
default Magento footer with a modern, multi-column layout that is fully
managed from the admin panel.

---

## Features

- **Multi-column layout** — choose 2, 3, or 4 column layouts with
  independent content per column
- **Column 1 — Logo and About** — display store logo, about text, and
  social media icons
- **Columns 2 and 3 — Link columns** — JSON-configured navigation links
  with a built-in JSON beautifier in the admin
- **Column 4 — Contact information** — phone, email, address, and
  working hours with Font Awesome icons
- **Social media icons** — Facebook, Twitter/X, Instagram, LinkedIn,
  YouTube, Pinterest with inline SVG
- **Newsletter section** — AJAX newsletter subscription with benefits
  list, fully customizable text and colors
- **Back to top button** — smooth-scroll button with configurable
  position (bottom-right or bottom-left)
- **Footer bottom bar** — copyright text with `{{year}}` placeholder,
  payment icons, and configurable bottom links
- **Theme Customizer integration** — all colors are managed centrally
  via Panth_ThemeCustomizer (if installed)
- **Dual theme support** — ships Hyva (Alpine.js) and Luma (vanilla JS)
  templates; the correct set is loaded automatically

---

## Installation

### Composer (recommended)

```bash
composer require mage2kishan/module-footer
bin/magento module:enable Panth_Footer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip
2. Extract to `app/code/Panth/Footer`
3. Run the same `module:enable ... cache:flush` commands above

---

## Requirements

| | Required |
|---|---|
| Magento | 2.4.4 - 2.4.8 (Open Source / Commerce / Cloud) |
| PHP | 8.1 / 8.2 / 8.3 / 8.4 |
| Panth_Core | ^1.0 (installed automatically via Composer) |

---

## Configuration

Navigate to **Stores > Configuration > Panth Extensions > Footer
Configuration**.

| Section | What it controls |
|---|---|
| General Settings | Enable/disable footer, column layout |
| Column 1-4 | Per-column content, links, contact info |
| Social Media Links | Platform URLs |
| Newsletter Section | Enable/disable, title, subtitle, benefits, button text |
| Back to Top Button | Enable/disable, position |
| Bottom Bar | Copyright text, payment icons, bottom links |

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | https://kishansavaliya.com |
| WhatsApp | +91 84012 70422 |

---

## License

Proprietary - see `LICENSE.txt`. Distributed exclusively via the Adobe
Commerce Marketplace.

---

## About the developer

Built and maintained by **Kishan Savaliya** - https://kishansavaliya.com.
Part of the Panth Infotech extension suite for Magento 2.
