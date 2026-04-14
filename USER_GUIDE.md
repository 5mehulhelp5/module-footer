# Panth Footer - User Guide

Panth_Footer replaces the default Magento footer with a fully
configurable, modern footer that supports both Hyva and Luma themes.
All settings are managed from the Magento admin panel.

---

## Table of contents

1. [Installation](#1-installation)
2. [Verifying the module is active](#2-verifying-the-module-is-active)
3. [General settings](#3-general-settings)
4. [Configuring columns](#4-configuring-columns)
5. [Social media links](#5-social-media-links)
6. [Newsletter section](#6-newsletter-section)
7. [Back to top button](#7-back-to-top-button)
8. [Bottom bar](#8-bottom-bar)
9. [Color customization](#9-color-customization)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Installation

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

## 2. Verifying the module is active

```bash
bin/magento module:status Panth_Footer
# Module is enabled
```

You should see "Footer" under the Panth Infotech sidebar menu in
the admin.

---

## 3. General settings

Navigate to **Stores > Configuration > Panth Extensions > Footer
Configuration > General Settings**.

| Setting | Default | Description |
|---|---|---|
| Enable Custom Footer | Yes | Toggle the entire custom footer on/off |
| Footer Layout | 4 Columns | Choose 2, 3, or 4 column layout |

---

## 4. Configuring columns

### Column 1 - Logo and About

- **Enable Column 1** - show/hide
- **Show Logo** - display the store logo
- **Column Title** - optional heading
- **About Text** - short company description (HTML allowed)
- **Show Social Media Icons** - display social links below the about text

### Columns 2 and 3 - Link columns

- **Enable Column** - show/hide
- **Column Title** - heading text (e.g. "Shop", "Customer Service")
- **Links (JSON Format)** - enter links as JSON array

Link format:
```json
[
    {"title": "New Arrivals", "url": "/what-is-new.html", "target": "_self"},
    {"title": "Sale", "url": "/sale.html", "target": "_self"}
]
```

Use `"_blank"` for external links. The admin includes a JSON beautifier
with Beautify, Minify, and Validate buttons.

### Column 4 - Contact Information

- **Enable Column** - show/hide
- **Column Title** - heading text
- **Show Contact Information** - toggle contact details
- **Phone Number** - displayed with a click-to-call link
- **Email Address** - displayed with a mailto link
- **Physical Address** - multiline address
- **Working Hours** - business hours text

---

## 5. Social media links

Navigate to **Footer Configuration > Social Media Links**.

Enter full URLs for each platform. Leave empty to hide a platform.

Supported platforms: Facebook, Twitter/X, Instagram, LinkedIn,
YouTube, Pinterest.

---

## 6. Newsletter section

Navigate to **Footer Configuration > Newsletter Section**.

| Setting | Default | Description |
|---|---|---|
| Enable Newsletter Section | Yes | Show/hide the newsletter area |
| Section Title | Stay Connected | Heading text |
| Section Subtitle | Get exclusive offers... | Subheading text |
| Benefits Text (JSON) | ["Exclusive offers",...] | JSON array of benefit items |
| Email Placeholder Text | Enter your email address | Input placeholder |
| Subscribe Button Text | Subscribe | Button label |

The newsletter form submits via AJAX to the standard Magento newsletter
subscriber endpoint.

---

## 7. Back to top button

Navigate to **Footer Configuration > Back to Top Button**.

| Setting | Default | Description |
|---|---|---|
| Enable Back to Top Button | Yes | Show/hide the scroll-to-top button |
| Button Position | Bottom Right | Bottom Right or Bottom Left |

The button appears after scrolling down 300px and uses smooth scrolling.

---

## 8. Bottom bar

Navigate to **Footer Configuration > Bottom Bar**.

| Setting | Default | Description |
|---|---|---|
| Show Payment Icons | Yes | Display Visa, Mastercard, PayPal, Amex icons |
| Copyright Text | (c) {{year}} Your Store... | Use `{{year}}` for auto year; HTML allowed |
| Show Footer Bottom Links | Yes | Toggle bottom links |
| Footer Bottom Links (JSON) | [{"title":"Privacy Policy",...}] | JSON array of links |

---

## 9. Color customization

All footer colors (background, text, headings, newsletter, back-to-top
button) are managed via the **Theme Customizer** module if installed.
Navigate to **Stores > Configuration > Panth Extensions > Theme
Customizer** to adjust colors.

---

## 10. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Footer not showing | Module disabled or "Enable Custom Footer" is No | Check module status and config |
| Default footer still visible | Layout XML cache | `bin/magento cache:flush` |
| Newsletter form not working | Form key missing or expired | Hard refresh the page |
| JSON links not rendering | Invalid JSON in config | Use the Validate button in admin |
| Colors not changing | Theme Customizer not installed | Install Panth_ThemeCustomizer |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Free email support is provided on a best-effort basis.
