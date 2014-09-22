#WooCommerce Skrill Gateway Plugin

**Tags**: woocommerce, skrill, skrill connect
**Requires at least**: 3.4
**Tested up to**: 3.9.1

Allows WooCommerce to accept payments using Skrill gateway.

##Description
Skrill Gateway for WooCommerce 1.6/2.0 adds support for Skrill, a service from Moneybookers, to the available payment methods. By enabling it, your customer will be able to complete payments through a secure portal.

Should you have any questions about this product, please have a look at the [Knowledge Base](https://aelia.freshdesk.com/support/solutions), or feel free to [contact us](http://aelia.co/contact/).

##Included localisations
* English (GB)
* Portoguese (PT), courtesy of [jpBenfica](http://www.loja77.com/).

##Requirements
* WordPress 3.6 or later
* PHP 5.3 or later
* WooCommerce 2.0.x/2.1x/2.2.x
* [AFC plugin for WooCommerce](http://aelia.co/downloads/wc-aelia-foundation-classes.zip) 1.0.10.140819 or later.

Installation
---
1. Extract the zip file and drop the contents in the ```wp-content/plugins/``` directory of your WordPress installation.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to ```WooCommerce > Settings > Payment Gateways > Skrill``` to configure the plugin.

For more information about installation and management of plugins, please refer to [WordPress documentation](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

##Setup
On the settings page, the following settings are required:

* **Skrill Email**: this is the email address associated to your Skrill merchant account.
* **Secret Word**: this is the secret word you entered in your Skrill account settings.

If you wish to get more details about Skrill, please refer to [Skrill website](http://www.skrill.com/).

### Integration with Currency Switcher
This plugin is fully compatible with the [Currency Switcher](aelia.co/shop/currency-switcher-woocommerce/). If you are using the Currency Switcher, and you find out that the Skrill gateway is not appearing on the checkout page, please go to ```WooCommerce > Currency Switcher Options > Payment Gateways``` and make sure that **Skrill** is listed amongst the **Enabled Gateways**.
