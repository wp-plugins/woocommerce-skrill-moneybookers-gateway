=== WooCommerce Skrill Gateway Plugin ===
Contributors: daigo75
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8ND89AA8B8QJ
Tags: woocommerce, skrill, moneybookers
Requires at least: 3.6
Tested up to: 3.9.1
Stable tag: 1.2.6.140611
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows WooCommerce to accept payments using Skrill gateway.

== Description ==
The plugin will add a new payment gateway called Skrill (Moneybookers), which will add support for the Skrill payment gateway. Upon checkout, your customers will be redirected to a secure portal where they can complete their payment. Both standard and Quick Checkout modes are supported. - See more at: http://dev.pathtoenlightenment.net/shop/skrill-gateway-for-woocommerce-2-0-x/


##Requirements
* WordPress 3.6+
* PHP 5.3+
* WooCommerce 2.0.x/2.1x
* [AFC plugin for WooCommerce](http://dev.pathtoenlightenment.net/downloads/wc-aelia-foundation-classes.zip) 1.0.5.140611+.

## Current limitations
* Plugin does not support pre-authorisation or subscriptions.

## Notes
* The plugin is provided free of charge, and it's not covered by free support. Should you have any questions about this product, please use the [product support section on this site](http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway). We will deal with each enquiry as soon as possible (usually within a few days).


== Installation ==

1. Extract the zip file and drop the contents in the ```wp-content/plugins/``` directory of your WordPress installation.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to ```WooCommerce > Settings > Payment Gateways > Skrill``` to configure the plugin.

For more information about installation and management of plugins, please refer to [WordPress documentation](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

###Setup
On the settings page, the following settings are required:

* **Skrill Email**: this is the email address associated to your Skrill merchant account.
* **Secret Word**: this is the secret word you entered in your Skrill account settings.

If you wish to get more details about Skrill, please refer to [Skrill website](http://www.skrill.com/).

### Integration with Currency Switcher
This plugin is fully compatible with the [Currency Switcher](dev.pathtoenlightenment.net/shop/currency-switcher-woocommerce/). If you are using the Currency Switcher, and you find out that the Skrill gateway is not appearing on the checkout page, please go to ```WooCommerce > Currency Switcher Options > Payment Gateways``` and make sure that **Skrill** is listed amongst the **Enabled Gateways**.

== Changelog ==

= 1.2.6.140611 =
* Improved compatibility with Codestyling Localization plugin.

= 1.2.5.140610 =
* Removed unneeded file.

= 1.2.4.140610 =
* Altered loading of main plugin file to use location of loader file.

= 1.2.3.140610 =
* Fixed dependency issue for WordPress repository.

= 1.2.2.140610 =
* Renamed main plugin file to match plugin slug.

= 1.2.1.140605 =
* Corrected plugin name and text domain in requirement checking class.

= 1.2.0.140527 =
* Redesigned plugin to use the Aelia Foundation Classes (AFC) plugin.
* Removed donation widget, as it was not used anyway.

= 1.0.8.140416 =
* Removed code that created an unused entry in WooCommerce menu.

= 1.0.7.140324 =
* Updated base classes.

= 1.0.6.140219 =
* Fixed dependencies.

= 1.0.5.130214 =
* Updated requirements.

= 1.0.4.131205 =
* Added missing Composer dependencies.

= 1.0.3.131201 =
* Fixed checks related to debug mode.
* Fixed loading of JavaScript on payment page.
* Fixed checks on data posted by MoneyBookers.

= 1.0.2.131122 =
* Fixed bug in settings page.

= 1.0.0.130102 =
* First official release
