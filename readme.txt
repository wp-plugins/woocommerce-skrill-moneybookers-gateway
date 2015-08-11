=== WooCommerce Skrill Gateway Plugin ===
Contributors: daigo75
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8ND89AA8B8QJ
Tags: woocommerce, skrill, moneybookers
Requires at least: 3.6
Tested up to: 4.3
Stable tag: 1.2.25.150811
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows WooCommerce to accept payments using Skrill gateway.

== Description ==
The plugin will add a new payment gateway called Skrill (Moneybookers), which will add support for the Skrill payment gateway. Upon checkout, your customers will be redirected to a secure portal where they can complete their payment. Both standard and Quick Checkout modes are supported. - See more at: http://aelia.co/shop/skrill-gateway-for-woocommerce-2-0-x/

= IMPORTANT =
**Make sure that you read and understand the plugin requirements and the FAQ before installing this plugin**. Almost all support requests we receive are related to missing requirements, and incomplete reading of the message that is displayed when such requirements are not met.

= Included localisations =
* English (GB)
* Portoguese (PT), courtesy of [jpBenfica](http://www.loja77.com/).

= Requirements =
* WordPress 3.6 or later
* PHP 5.3 or later
* WooCommerce 2.1x/2.2.x/2.3.x/2.4.x
* [Aelia Foundation Classes plugin for WooCommerce](http://aelia.co/downloads/wc-aelia-foundation-classes.zip) 1.0.10.140819 or later.

= Current limitations =
* Plugin does not yet support pre-authorisation or subscriptions.

= Notes =
* This plugin is provided as a **free** alternative to the many commercial plugins that add the Skrill payment gateway to WooCommerce, and it's not automatically covered by free support. See FAQ for more details.

== Frequently Asked Questions ==

= I get a message saying that plugin could not be loaded, what do I do? =

The plugin includes a dependency checking mechanism that will prevent it from loading if its requirements are not met. This mechanism is a great improvement over the typical behaviour exhibited by other plugins, that simply crash when some requirements are missing.

If your installation is missing some requirements, you will see the following message:

**Plugin *<plugin name>* could not be loaded due to missing requirements.**

* <list of missing requirements here>

***Note**: even though the plugin might be showing as "active", it will not load and its features will not be available until its requirements are met.*

If you see the above message, simply go through at the list of requirements and ensure that your system covers them. Once they missing requirements are installed, the plugin will work automatically.

**Tip**
The most common mistake is to forget the installation of the [Aelia Foundation Classes for WooCommerce](http://aelia.co/downloads/wc-aelia-foundation-classes.zip), i.e. the framework on which this plugin is based.

= What is the support policy for this plugin? =

As indicated in the Description section, we offer this plugin **free of charge**, but we cannot afford to also provide free, direct support for it as we do for our paid products.
Should you encounter any difficulties with this plugin, and need support, you have several options:

1. **Report the issue in the [Support section, above](http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway)**, and we will look into it as soon as possible. This option is **free of charge**, and it's offered on a best effort basis. Please note that we won't be able to offer hands-on troubleshooting on issues related to a specific site, such as incompatibilities with a specific environment or 3rd party plugins.
2. **[Contact us](http://aelia.co/contact) to request standard paid support**. As part of paid support, you will receive direct assistance from our team, who will troubleshoot your site and help you to make it work smoothly. We can also help you with installation, customisation and development of new features.
3. **Use one of the available commercial plugins**, such as [the one from PatSaTech](http://bit.ly/1vo1rXV_wp), which comes at a fair price and with free support included.

= I have a question unrelated to support, where can I ask it? =

Should you have any question about this product, please use the [product support section on this site](http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway). We will deal with each enquiry as soon as possible.

== Installation ==

1. Extract the zip file and drop the contents in the ```wp-content/plugins/``` directory of your WordPress installation.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to ```WooCommerce > Settings > Payment Gateways > Skrill``` to configure the plugin.

For more information about installation and management of plugins, please refer to [WordPress documentation](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

= Setup =
On the settings page, the following settings are required:

* **Skrill Email**: this is the email address associated to your Skrill merchant account.
* **Secret Word**: this is the secret word you entered in your Skrill account settings.

If you wish to get more details about Skrill, please refer to [Skrill website](http://www.skrill.com/).

= Integration with Currency Switcher =
This plugin is fully compatible with the [Currency Switcher](aelia.co/shop/currency-switcher-woocommerce/). If you are using the Currency Switcher, and you find out that the Skrill gateway is not appearing on the checkout page, please go to ```WooCommerce > Currency Switcher Options > Payment Gateways``` and make sure that **Skrill** is listed amongst the **Enabled Gateways**.

== Changelog ==

= 1.2.25.150811 =
* Verified compatibility with WordPress 4.3.
* Verified compatibility with WooCommerce 2.4.x.
* Fixed path of log file in plugin settings UI.
* Updated requirement checking class.

= 1.2.24.150722 =
* Added Dutch localisation files. Courtesy of Diane Bakker.

= 1.2.23.150418 =
* Updated class used for requirements checking. The class now supports automatic installation of required plugins.

= 1.2.22.150303 =
* Improved requirement checking. The plugin can now auto-install the AFC plugin.
* Altered build script to include language files.

= 1.2.21.150227 =
* Updated base class usef for requirement checking.

= 1.2.20.150211 =
* Fixed incorrect class reference.

= 1.2.19.150211 =
* Improved compatibility with WooCommerce 2.3:
	* `WooCommerce::logger()` method has been removed. Altered logging mechanism to deal with breaking change.

= 1.2.18.150208 =
* Updated `readme.txt`.

= 1.2.18.150208 =
* Fixed Status URL. The URL was incorrectly forced to HTTP, thus making the Skrill response fail when the site was configured to use only HTTPS. Many thanks to **yeray** for the heads up.

= 1.2.15.150107 =
* Fixed bug in debug code. The bug prevented debug information from being logged.

= 1.2.14.141218 =
* Updated base class for requirement checks.

= 1.2.13.141120 =
* Added logic to translate the 2-digits currency codes used by WooCommerce into the 3-digits codes required by Skrill.

= 1.2.12.141116 =
* Improved "missing requirements" message:
	* Corrected wording to highlight the support policy.
	* Corrected link to the support section.

= 1.2.11.140922 =
* Added compatibility with WooCommerce 2.2.x.

= 1.2.10.140819 =
* Updated README files.

= 1.2.9.140819 =
* Updated logic used to for requirements checking.

= 1.2.8.140619 =
* Modified loading of Aelia_WC_RequirementsChecks class to work around quirks of Opcode Caching extensions, such as APC and XCache.

= 1.2.7.140614 =
* Added Portoguese (Portugal) localisation files.

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
