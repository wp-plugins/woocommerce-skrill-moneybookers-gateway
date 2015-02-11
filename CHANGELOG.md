# Aelia Skrill Gateway - Change Log

## Version 1.x
####1.2.20.150211
* Fixed incorrect class reference.

####1.2.19.150211
* Improved compatibility with WooCommerce 2.3:
	* `WooCommerce::logger()` method has been removed. Altered logging mechanism to deal with breaking change.

####1.2.18.150208
* Updated `readme.txt`.

####1.2.16.150124
* Fixed Status URL. The URL was incorrectly forced to HTTP, thus making the Skrill response fail when the site was configured to use only HTTPS.

####1.2.15.150107
* Fixed bug in debug code. The bug prevented debug information from being logged.

####1.2.14.141218
* Updated base class for requirement checks.

####1.2.13.141120
* Added logic to translate the 2-digits currency codes used by WooCommerce into the 3-digits codes required by Skrill.

####1.2.12.141116
* Improved "missing requirements" message:
	* Corrected wording to highlight the support policy.
	* Corrected link to the support section.

####1.2.11.140922
* Added compatibility with WooCommerce 2.2.x.

####1.2.10.140819
* Updated README files.

####1.2.9.140819
* Updated logic used to for requirements checking.

####1.2.8.140619
* Modified loading of Aelia_WC_RequirementsChecks class to work around quirks of Opcode Caching extensions, such as APC and XCache.

####1.2.7.140614
* Added Portoguese (Portugal) localisation files. Courtesy of [jpBenfica](http://www.loja77.com/).

####1.2.6.140611
* Improved compatibility with Codestyling Localization plugin.

####1.2.5.140610
* Removed unneeded file.

####1.2.4.140610
* Altered loading of main plugin file to use location of loader file.

####1.2.3.140610
* Fixed dependency issue for WordPress repository.

####1.2.2.140610
* Renamed main plugin file to match plugin slug.

####1.2.1.140605
* Corrected plugin name and text domain in requirement checking class.

####1.2.0.140527
* Redesigned plugin to use the Aelia Foundation Classes (AFC) plugin.
* Removed donation widget, as it was not used anyway.

####1.0.8.140416
* Removed code that created an unused entry in WooCommerce menu.

####1.0.7.140324
* Updated base classes.

####1.0.6.140219
* Fixed dependencies.

####1.0.5.140214
* Updated requirements.

####1.0.4.131205
* Added missing Composer dependencies

####1.0.3.131201
* Fixed checks related to debug mode.
* Fixed loading of JavaScript on payment page.
* Fixed checks on data posted by MoneyBookers.

####1.0.2.131122
* Fixed bug in settings page.

####1.0.0.130102
* First official release
