# Squatch Consent

A lightweight WordPress cookie consent banner that allows visitors to
opt in or opt out of cookies and tracking scripts.

Squatch Consent provides a simple way to display a customizable consent
notice while controlling when third-party tracking scripts are loaded.

## Features

-   Simple cookie consent banner
-   Accept or reject cookie tracking
-   Stores visitor consent preference
-   Customizable consent message
-   Custom banner colors:
    -   Background color
    -   Text color
    -   Button color
-   Optional privacy policy link
-   Add tracking scripts that only load after consent
-   Supports:
    -   Google Analytics
    -   Google Tag Manager
    -   Meta Pixel
    -   Other third-party tracking scripts
-   Lightweight assets with no dependencies
-   Built-in Squatch Creative admin styling

## Installation

1.  Download or clone this repository.
2.  Upload the plugin folder to:

`/wp-content/plugins/squatch-consent/`

3.  Activate Squatch Consent from the WordPress Plugins screen.
4.  Configure settings under:

Tools → Squatch Consent

## Configuration

The settings page allows administrators to customize:

### Consent Message

The text displayed to visitors inside the consent banner.

### Banner Styling

Customize the appearance of the consent banner:

-   Background color
-   Text color
-   Accept/reject button color

### Privacy Policy Link

Enable or disable automatically adding a link to the site's WordPress
privacy policy page.

### Tracking Scripts

Paste tracking scripts that should only run after a visitor accepts
cookies.

Scripts added here are automatically converted to:

```{=html}
<script type="text/plain" data-squatch-consent>
```
They will only execute after consent is granted.

## How Consent Works

When a visitor first loads the site:

1.  The consent banner is displayed.
2.  The visitor chooses Accept or Reject.
3.  The preference is stored in a browser cookie:

`squatch_consent`

4.  Tracking scripts are enabled only after acceptance.

### Versioning

Plugin assets use the plugin version as the cache-busting version.

Updating the plugin version will force browsers to load updated CSS and
JavaScript files.

## Requirements

-   WordPress 5+
-   PHP 7+

## Changelog

### 1.003

-   Added customizable consent banner styling
-   Added tracking script management
-   Added privacy policy link option
-   Improved admin settings page
-   Added Squatch Creative branding

## Author

Squatch Creative https://squatchcreative.com
