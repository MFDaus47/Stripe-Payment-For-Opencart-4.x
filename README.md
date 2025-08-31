# STRIPE Payment Module For OPENCART 4.x

## Overview

This Stripe payment module provides secure payment processing for OpenCart 4.x using Stripe's modern Payment Intents API and Stripe Elements.

## Features

- ✅ **OpenCart 4.x Compatible** - Fully compatible with OpenCart 4.x framework
- ✅ **Payment Intents API** - Uses Stripe's latest Payment Intents API for enhanced security
- ✅ **Stripe Elements** - Modern, PCI-compliant payment forms with Stripe Elements
- ✅ **PHP 8.1+ Support** - Compatible with modern PHP versions
- ✅ **Customer Management** - Save and manage customer payment methods
- ✅ **Admin Integration** - Full admin panel integration with order management
- ✅ **Refund Support** - Process refunds directly from OpenCart admin
- ✅ **Multi-environment** - Support for both test and live Stripe environments

## Installation

1. **Backup your OpenCart installation** before proceeding
2. Upload all files from the `upload/` directory to your OpenCart root directory
3. Install the extension through OpenCart admin:
   - Go to **Extensions > Installer**
   - Upload the extension files or install manually
   - Go to **Extensions > Extensions > Payments**
   - Find "Stripe" and click **Install**
4. Configure the extension with your Stripe API keys

## Configuration

### Stripe API Setup

1. Log into your [Stripe Dashboard](https://dashboard.stripe.com)
2. Get your API keys from **Developers > API Keys**
3. In OpenCart admin, go to **Extensions > Extensions > Payments > Stripe**
4. Enter your:
   - **Test Secret Key** (for testing)
   - **Test Publishable Key** (for testing)
   - **Live Secret Key** (for production)
   - **Live Publishable Key** (for production)
5. Set your preferred currency and order status
6. Enable the extension

### Environment Settings

- **Test Mode**: Use for development and testing
- **Live Mode**: Use for production payments
- Switch between modes using the Environment dropdown

## Requirements

- **OpenCart**: 4.x
- **PHP**: 8.1 or higher
- **Stripe Account**: Active Stripe account with API access
- **SSL Certificate**: Required for live payments

## Migration from OpenCart 3.x

This version includes significant improvements:

- **Modernized Stripe Integration**: Migrated from deprecated Charges API to Payment Intents
- **Enhanced Security**: Uses Stripe Elements for PCI compliance
- **PHP 8.1+ Compatibility**: Updated for modern PHP versions
- **Improved Error Handling**: Better error messages and debugging

## Troubleshooting

### Common Issues

1. **"Invalid API Key" Error**

   - Verify your Stripe API keys are correct
   - Ensure you're using the right keys for your environment (test/live)

2. **Payment Form Not Loading**

   - Check browser console for JavaScript errors
   - Ensure Stripe.js is loading correctly

3. **Refund Not Working**
   - Verify the payment was processed through Payment Intents
   - Check Stripe dashboard for payment status

### Debug Mode

Enable logging in OpenCart admin:

- Go to **System > Settings > Edit Store > Server**
- Set **Error Log Level** to **Debug**

## Support

For issues specific to this OpenCart extension:

- Check the [Issues](https://github.com/your-repo/issues) section
- Review Stripe's [documentation](https://stripe.com/docs)

For Stripe API issues:

- Visit [Stripe Support](https://support.stripe.com)
- Check [Stripe Status](https://status.stripe.com)

## Changelog

### Version 4.x.x

- ✅ Migrated to Payment Intents API
- ✅ Updated to Stripe Elements
- ✅ Added PHP 8.1+ support
- ✅ Improved error handling
- ✅ Enhanced admin interface

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

Originally forked from [emreacar/Stripe-Payment-For-Opencart-3.x](https://github.com/emreacar/Stripe-Payment-For-Opencart-3.x)

For more information about Stripe: [www.stripe.com](https://www.stripe.com)
