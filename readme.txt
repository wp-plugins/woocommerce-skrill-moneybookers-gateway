=== WooCommerce Skrill Gateway Plugin ===
Contributors: daigo75
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8ND89AA8B8QJ
Tags: woocommerce, skrill
Requires at least: 3.4
Tested up to: 3.7
Stable tag: 1.0.4.131205
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows WooCommerce to accept payments using Skrill gateway.

== Description ==
The plugin will add a new payment gateway called Skrill (Moneybookers), which will add support for the Skrill payment gateway. Upon checkout, your customers will be redirected to a secure portal where they can complete their payment. Both standard and Quick Checkout modes are supported. - See more at: http://dev.pathtoenlightenment.net/shop/skrill-gateway-for-woocommerce-2-0-x/


##Requirements
* WordPress 3.4+ (plugin has been tested up to WordPress 3.7)
* PHP 5.3+
* WooCommerce 1.6/2.0.x (plugin has been tested up to WooCommerce 2.0.20)

## Current limitations
* Plugin does not support pre-authorisation or subscriptions.

## Notes
* The plugin is provided free of charge, and it's not covered by free spport. Should you have any questions about this product, please use the [product support section on this site](http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway). We will deal with each enquiry as soon as possible.


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