# SHR Quick View Modal

A Shopware 6 plugin that enhances the shopping experience by allowing customers to quickly view product details directly from category pages through an elegant modal window, without leaving their current page.

## Features

### 1. Quick View Button
- Seamlessly integrated quick view button on product listings
- Configurable button visibility (always visible or show on hover)
- Customizable button icon (eye, magnifying glass, or lightning bolt)
- Custom button color support

### 2. Interactive Modal Window
- Responsive product image gallery with slider
- Product details including title, description, and price
- Variant selection capability
- Quantity selector
- Direct "Add to cart" functionality
- Link to full product page

### 3. Product Comparison
- Built-in product comparison functionality
- Compare button in quick view modal
- Maximum product comparison limit (configurable)
- Persistent comparison list using local storage
- Compare page with detailed product comparison

### 4. Recently Viewed Products
- Track and display recently viewed products
- Configurable number of items to show
- Persistent across sessions

### 5. Enhanced Accessibility
- ARIA attributes for screen readers
- Keyboard navigation support
- Focus trap in modal for better keyboard usage
- High contrast visual indicators

### 6. Performance Optimized
- Asynchronous content loading
- Optimized image quality settings
- Efficient DOM manipulation
- Minimal initial page load impact

### 7. Multi-Language Support
- Built-in translations for:
  - English (en_GB)
  - German (de_DE)
  - Spanish (es_ES)
  - Portuguese (pt_PT)
  - Chinese (zh_CN)

## Requirements

- Shopware 6.3.0 or higher
- Compatible with all standard Shopware 6 themes
- PHP 7.4 or higher

## Installation

### Via Composer
```bash
composer require shr/quick-view-modal
bin/console plugin:refresh
bin/console plugin:install --activate ShrQuickViewModal
bin/console cache:clear
```

### Manual Installation
1. Download the plugin from GitHub
2. Extract and copy the folder to your `custom/plugins` directory
3. Install and activate through Shopware Admin panel

## Configuration

Navigate to Settings > System > Plugins > ShrQuickViewModal > Configuration

### General Settings
- Enable/Disable plugin
- Show button on hover only
- Button icon selection
- Button color customization

### Content Settings
- Enable/Disable image gallery
- Show/Hide short description
- Show/Hide technical data
- Show/Hide reviews
- Show/Hide shipping information

### Recently Viewed Products
- Enable/Disable feature
- Set maximum number of items

### Comparison Features
- Enable/Disable product comparison
- Set maximum number of comparable products

### Performance
- Image quality settings (High/Medium/Low)
- Caching options

## Support

For issues, feature requests, or contributions:
- Create an issue on GitHub
- Submit a pull request
- Contact: support@shr-example.com

## License

MIT License - See LICENSE file for details