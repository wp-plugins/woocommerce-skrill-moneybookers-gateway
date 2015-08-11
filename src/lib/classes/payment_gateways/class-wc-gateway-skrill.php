<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(class_exists('WC_Gateway_Skrill')) {
	return;
}

use Aelia\WC\SkrillGateway\WC_Skrill_Gateway_Plugin;
use Aelia\WC\Order as Aelia_Order;
use Aelia\WC\SkrillGateway\Definitions;

/**
 * Class to implement the Skrill payment system.
 */
class WC_Gateway_Skrill extends WC_Payment_Gateway {
	const PAYMENT_FAILED = -2;
	const PAYMENT_CANCELLED = -1;
	const PAYMENT_PENDING = 0;
	const PAYMENT_SUCCESSFUL = 2;

	// @var string The ID to use when logging messages
	protected $log_id = 'skrill';

	// @var WC_Logger The logger that will be used by the gateway.
	protected $logger;

	// @var array A list of currencies supported by the Skrill gateway
	protected $supported_currencies = array(
		'EUR' => 'Euro',
		'USD' => 'U.S. Dollar',
		'GBP' => 'British Pound',
		'HKD' => 'Hong Kong Dollar',
		'SGD' => 'Singapore Dollar',
		'JPY' => 'Japanese Yen',
		'CAD' => 'Canadian Dollar',
		'AUD' => 'Australian Dollar',
		'CHF' => 'Swiss Franc',
		'DKK' => 'Danish Krone',
		'SEK' => 'Swedish Krona',
		'NOK' => 'Norwegian Krone',
		'ILS' => 'Israeli Shekel',
		'MYR' => 'Malaysian Ringgit',
		'NZD' => 'New Zealand Dollar',
		'TRY' => 'New Turkish Lira',
		'AED' => 'Utd. Arab Emir. Dirham',
		'MAD' => 'Moroccan Dirham',
		'QAR' => 'Qatari Rial',
		'SAR' => 'Saudi Riyal',
		'TWD' => 'Taiwan Dollar',
		'THB' => 'Thailand Baht',
		'CZK' => 'Czech Koruna',
		'HUF' => 'Hungarian Forint',
		'SKK' => 'Slovakian Koruna',
		'EEK' => 'Estonian Kroon',
		'BGN' => 'Bulgarian Leva',
		'PLN' => 'Polish Zloty',
		'ISK' => 'Iceland Krona',
		'INR' => 'Indian Rupee',
		'LVL' => 'Latvian Lat',
		'KRW' => 'South-Korean Won',
		'ZAR' => 'South-African Rand',
		'RON' => 'Romanian Leu New',
		'HRK' => 'Croatian Kuna',
		'LTL' => 'Lithuanian Litas',
		'JOD' => 'Jordanian Dinar',
		'OMR' => 'Omani Rial',
		'RSD' => 'Serbian dinar',
		'TND' => 'Tunisian Dinar',
	);

	// @var array A list of languages supported by the Skrill gateway
	protected $supported_languages = array(
		'EN' => 'English',
		'DE' => 'Deutsch',
		'ES' => 'Español',
		'FR' => 'Français',
		'IT' => 'Italiano',
		'PL' => 'Polski',
		'GR' => 'Ελληνικά',
		'RO' => 'Limba română',
		'RU' => 'Русский язык',
		'TR' => 'Türkçe',
		'CN' => '中文',
		'CZ' => 'Čeština',
		'NL' => 'Nederlands',
		'DA' => 'Dasnk',
		'SV' => 'Svenska',
		'FI' => 'Suomi',
	);

	// @var array A map that associates 2-digits country codes to the 3-digit codes required by Skrill.
	// @link https://www.skrill.com/fileadmin/templates/main/res/material/documents/pdf/getting-started/skrill-integration-manual-en.pdf
	protected $country_codes_map = array(
		'AF' => 'AFG', // Afghanistan
		'AX' => 'ALA', // Åland Islands
		'AL' => 'ALB', // Albania
		'DZ' => 'DZA', // Algeria
		'AS' => 'ASM', // American Samoa
		'AD' => 'AND', // Andorra
		'AO' => 'AGO', // Angola
		'AI' => 'AIA', // Anguilla
		'AQ' => 'ATA', // Antarctica
		'AG' => 'ATG', // Antigua and Barbuda
		'AR' => 'ARG', // Argentina
		'AM' => 'ARM', // Armenia
		'AW' => 'ABW', // Aruba
		'AU' => 'AUS', // Australia
		'AT' => 'AUT', // Austria
		'AZ' => 'AZE', // Azerbaijan
		'BS' => 'BHS', // Bahamas
		'BH' => 'BHR', // Bahrain
		'BD' => 'BGD', // Bangladesh
		'BB' => 'BRB', // Barbados
		'BY' => 'BLR', // Belarus
		'BE' => 'BEL', // Belgium
		'BZ' => 'BLZ', // Belize
		'BJ' => 'BEN', // Benin
		'BM' => 'BMU', // Bermuda
		'BT' => 'BTN', // Bhutan
		'BO' => 'BOL', // Bolivia, Plurinational State of
		'BQ' => 'BES', // Bonaire, Sint Eustatius and Saba
		'BA' => 'BIH', // Bosnia and Herzegovina
		'BW' => 'BWA', // Botswana
		'BV' => 'BVT', // Bouvet Island
		'BR' => 'BRA', // Brazil
		'IO' => 'IOT', // British Indian Ocean Territory
		'BN' => 'BRN', // Brunei Darussalam
		'BG' => 'BGR', // Bulgaria
		'BF' => 'BFA', // Burkina Faso
		'BI' => 'BDI', // Burundi
		'KH' => 'KHM', // Cambodia
		'CM' => 'CMR', // Cameroon
		'CA' => 'CAN', // Canada
		'CV' => 'CPV', // Cabo Verde
		'KY' => 'CYM', // Cayman Islands
		'CF' => 'CAF', // Central African Republic
		'TD' => 'TCD', // Chad
		'CL' => 'CHL', // Chile
		'CN' => 'CHN', // China
		'CX' => 'CXR', // Christmas Island
		'CC' => 'CCK', // Cocos (Keeling) Islands
		'CO' => 'COL', // Colombia
		'KM' => 'COM', // Comoros
		'CG' => 'COG', // Congo
		'of' => 'the', // Congo, the Democratic Republic
		'CK' => 'COK', // Cook Islands
		'CR' => 'CRI', // Costa Rica
		'CI' => 'CIV', // Côte d'Ivoire
		'HR' => 'HRV', // Croatia
		'CU' => 'CUB', // Cuba
		'CW' => 'CUW', // Curaçao
		'CY' => 'CYP', // Cyprus
		'CZ' => 'CZE', // Czech Republic
		'DK' => 'DNK', // Denmark
		'DJ' => 'DJI', // Djibouti
		'DM' => 'DMA', // Dominica
		'DO' => 'DOM', // Dominican Republic
		'EC' => 'ECU', // Ecuador
		'EG' => 'EGY', // Egypt'El' => 'Sal', //
		'GQ' => 'GNQ', // Equatorial Guinea
		'ER' => 'ERI', // Eritrea
		'EE' => 'EST', // Estonia
		'ET' => 'ETH', // Ethiopia
		'FK' => 'FLK', // Falkland Islands (Malvinas)
		'FO' => 'FRO', // Faroe Islands
		'FJ' => 'FJI', // Fiji
		'FI' => 'FIN', // Finland
		'FR' => 'FRA', // France
		'GF' => 'GUF', // French Guiana
		'PF' => 'PYF', // French Polynesia
		'TF' => 'ATF', // French Southern Territories
		'GA' => 'GAB', // Gabon
		'GM' => 'GMB', // Gambia
		'GE' => 'GEO', // Georgia
		'DE' => 'DEU', // Germany
		'GH' => 'GHA', // Ghana
		'GI' => 'GIB', // Gibraltar
		'GR' => 'GRC', // Greece
		'GL' => 'GRL', // Greenland
		'GD' => 'GRD', // Grenada
		'GP' => 'GLP', // Guadeloupe
		'GU' => 'GUM', // Guam
		'GT' => 'GTM', // Guatemala
		'GG' => 'GGY', // Guernsey
		'GN' => 'GIN', // Guinea
		'GW' => 'GNB', // Guinea-Bissau
		'GY' => 'GUY', // Guyana
		'HT' => 'HTI', // Haiti
		'HM' => 'HMD', // Heard Island and McDonald Islands
		'VA' => 'VAT', // Holy See (Vatican City State)
		'HN' => 'HND', // Honduras
		'HK' => 'HKG', // Hong Kong
		'HU' => 'HUN', // Hungary
		'IS' => 'ISL', // Iceland
		'IN' => 'IND', // India
		'ID' => 'IDN', // Indonesia
		'IR' => 'IRN', // Iran, Islamic Republic of
		'IQ' => 'IRQ', // Iraq
		'IE' => 'IRL', // Ireland
		'of' => 'Man', // Isle
		'IL' => 'ISR', // Israel
		'IT' => 'ITA', // Italy
		'JM' => 'JAM', // Jamaica
		'JP' => 'JPN', // Japan
		'JE' => 'JEY', // Jersey
		'JO' => 'JOR', // Jordan
		'KZ' => 'KAZ', // Kazakhstan
		'KE' => 'KEN', // Kenya
		'KI' => 'KIR', // Kiribati
		'KP' => 'PRK', // Korea, Democratic People's Republic of
		'KR' => 'KOR', // Korea, Republic of
		'KW' => 'KWT', // Kuwait
		'KG' => 'KGZ', // Kyrgyzstan
		'LA' => 'LAO', // Lao People's Democratic Republic
		'LV' => 'LVA', // Latvia
		'LB' => 'LBN', // Lebanon
		'LS' => 'LSO', // Lesotho
		'LR' => 'LBR', // Liberia
		'LY' => 'LBY', // Libya
		'LI' => 'LIE', // Liechtenstein
		'LT' => 'LTU', // Lithuania
		'LU' => 'LUX', // Luxembourg
		'MO' => 'MAC', // Macao
		'MK' => 'MKD', // Macedonia, the former Yugoslav Republic of
		'MG' => 'MDG', // Madagascar
		'MW' => 'MWI', // Malawi
		'MY' => 'MYS', // Malaysia
		'MV' => 'MDV', // Maldives
		'ML' => 'MLI', // Mali
		'MT' => 'MLT', // Malta
		'MH' => 'MHL', // Marshall Islands
		'MQ' => 'MTQ', // Martinique
		'MR' => 'MRT', // Mauritania
		'MU' => 'MUS', // Mauritius
		'YT' => 'MYT', // Mayotte
		'MX' => 'MEX', // Mexico
		'FM' => 'FSM', // Micronesia, Federated States of
		'MD' => 'MDA', // Moldova, Republic of
		'MC' => 'MCO', // Monaco
		'MN' => 'MNG', // Mongolia
		'ME' => 'MNE', // Montenegro
		'MS' => 'MSR', // Montserrat
		'MA' => 'MAR', // Morocco
		'MZ' => 'MOZ', // Mozambique
		'MM' => 'MMR', // Myanmar
		'NA' => 'NAM', // Namibia
		'NR' => 'NRU', // Nauru
		'NP' => 'NPL', // Nepal
		'NL' => 'NLD', // Netherlands
		'NC' => 'NCL', // New Caledonia
		'NZ' => 'NZL', // New Zealand
		'NI' => 'NIC', // Nicaragua
		'NE' => 'NER', // Niger
		'NG' => 'NGA', // Nigeria
		'NU' => 'NIU', // Niue
		'NF' => 'NFK', // Norfolk Island
		'MP' => 'MNP', // Northern Mariana Islands
		'NO' => 'NOR', // Norway
		'OM' => 'OMN', // Oman
		'PK' => 'PAK', // Pakistan
		'PW' => 'PLW', // Palau
		'PS' => 'PSE', // Palestine, State of
		'PA' => 'PAN', // Panama
		'PG' => 'PNG', // Papua New Guinea
		'PY' => 'PRY', // Paraguay
		'PE' => 'PER', // Peru
		'PH' => 'PHL', // Philippines
		'PN' => 'PCN', // Pitcairn
		'PL' => 'POL', // Poland
		'PT' => 'PRT', // Portugal
		'PR' => 'PRI', // Puerto Rico
		'QA' => 'QAT', // Qatar
		'RE' => 'REU', // Réunion
		'RO' => 'ROU', // Romania
		'RU' => 'RUS', // Russian Federation
		'RW' => 'RWA', // Rwanda
		'BL' => 'BLM', // Saint Barthélemy
		'da' => 'Cun', // Saint Helena, Ascension and Tristan
		'KN' => 'KNA', // Saint Kitts and Nevis
		'LC' => 'LCA', // Saint Lucia
		'MF' => 'MAF', // Saint Martin (French part)
		'PM' => 'SPM', // Saint Pierre and Miquelon
		'VC' => 'VCT', // Saint Vincent and the Grenadines
		'WS' => 'WSM', // Samoa
		'SM' => 'SMR', // San Marino
		'ST' => 'STP', // Sao Tome and Principe
		'SA' => 'SAU', // Saudi Arabia
		'SN' => 'SEN', // Senegal
		'RS' => 'SRB', // Serbia
		'SC' => 'SYC', // Seychelles
		'SL' => 'SLE', // Sierra Leone
		'SG' => 'SGP', // Singapore
		'SX' => 'SXM', // Sint Maarten (Dutch part)
		'SK' => 'SVK', // Slovakia
		'SI' => 'SVN', // Slovenia
		'SB' => 'SLB', // Solomon Islands
		'SO' => 'SOM', // Somalia
		'ZA' => 'ZAF', // South Africa
		'GS' => 'SGS', // South Georgia and the South Sandwich Islands
		'SS' => 'SSD', // South Sudan
		'ES' => 'ESP', // Spain
		'LK' => 'LKA', // Sri Lanka
		'SD' => 'SDN', // Sudan
		'SR' => 'SUR', // Suriname
		'SJ' => 'SJM', // Svalbard and Jan Mayen
		'SZ' => 'SWZ', // Swaziland
		'SE' => 'SWE', // Sweden
		'CH' => 'CHE', // Switzerland
		'SY' => 'SYR', // Syrian Arab Republic
		'of' => 'Chi', // Taiwan, Province
		'TJ' => 'TJK', // Tajikistan
		'TZ' => 'TZA', // Tanzania, United Republic of
		'TH' => 'THA', // Thailand
		'TL' => 'TLS', // Timor-Leste
		'TG' => 'TGO', // Togo
		'TK' => 'TKL', // Tokelau
		'TO' => 'TON', // Tonga
		'TT' => 'TTO', // Trinidad and Tobago
		'TN' => 'TUN', // Tunisia
		'TR' => 'TUR', // Turkey
		'TM' => 'TKM', // Turkmenistan
		'TC' => 'TCA', // Turks and Caicos Islands
		'TV' => 'TUV', // Tuvalu
		'UG' => 'UGA', // Uganda
		'UA' => 'UKR', // Ukraine
		'AE' => 'ARE', // United Arab Emirates
		'GB' => 'GBR', // United Kingdom
		'US' => 'USA', // United States
		'UM' => 'UMI', // United States Minor Outlying Islands
		'UY' => 'URY', // Uruguay
		'UZ' => 'UZB', // Uzbekistan
		'VU' => 'VUT', // Vanuatu
		'VE' => 'VEN', // Venezuela, Bolivarian Republic of
		'VN' => 'VNM', // Viet Nam
		'VG' => 'VGB', // Virgin Islands, British
		'VI' => 'VIR', // Virgin Islands, U.S.
		'WF' => 'WLF', // Wallis and Futuna
		'EH' => 'ESH', // Western Sahara
		'YE' => 'YEM', // Yemen
		'ZM' => 'ZMB', // Zambia
		'ZW' => 'ZWE', // Zimbabwe
	);

	/**
	 * Returns WooCommerce instance.
	 *
	 * @return WooCommerce instance
	 */
	protected function woocommerce() {
		global $woocommerce;
		return $woocommerce;
	}

	/**
	 * Gets and option from the settings API. Implemented for compatibility with
	 * WooCommerce 1.6, whose class WC_Payment_Gateway doesn't implement this
	 * method.
	 *
	 * @param string key The option key.
	 * @param mixed empty_value The value to return if the option is not found.
	 * @return mixed
	 */
	public function get_option($key, $empty_value = null) {
		if(method_exists('WC_Payment_Gateway', 'get_option')) {
			return parent::get_option($key, $empty_value);
		}

		return get_value($key, $this->settings, $empty_value);
	}

	/**
	 * Indicates if debug mode is active.
	 *
	 * @return bool
	 */
	protected function debug_mode() {
		return ($this->debug == 'yes');
	}

	/**
	 * Adds a message to the log if debug mode is enabled.
	 *
	 * @param string message The message to log.
	 * @param bool is_debug_msg Indicates if the message should only be logged
	 * while debug mode is true.
	 */
	protected function log($message, $is_debug_msg = false) {
		if($is_debug_msg && !$this->debug_mode()) {
			return;
		}
		$this->logger->add($this->log_id, $message);
	}

	/**
	 * Convenience method. Returns a plugin path.
	 *
	 * @param string key The path key.
	 * @return string
	 */
	protected function path($key) {
		return WC_Skrill_Gateway_Plugin::instance()->path($key);
	}

	/**
	 * Convenience method. Returns a plugin URL.
	 *
	 * @param string key The URL key.
	 * @return string
	 */
	protected function url($key) {
		return WC_Skrill_Gateway_Plugin::instance()->url($key);
	}

	/**
	 * Indicates if a currency is supported by the gateway.
	 *
	 * @param string currency A currency code.
	 * @return bool
	 */
	protected function is_currency_supported($currency) {
		return array_key_exists($currency, $this->supported_currencies);
	}

	/**
	 * Indicates if a currency is supported by the gateway.
	 *
	 * @param string currency A currency code.
	 * @return bool
	 */
	protected function is_language_supported($language_code) {
		return in_array($language_code, $this->supported_languages);
	}

	/**
	 * Returns the language code that should be passed to Skrill gateway.
	 *
	 * @return string
	 */
	protected function get_gateway_language() {
		if(defined('ICL_LANGUAGE_CODE') &&
			$this->is_language_supported(strtoupper(ICL_LANGUAGE_CODE))) {
			return ICL_LANGUAGE_CODE;
		}
		else {
			return $this->default_language;
		}
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Get text domain from main plugin
		$this->text_domain = WC_Skrill_Gateway_Plugin::$text_domain;
		$this->logger = new \WC_Logger();

		$this->id = 'skrill';
		$this->method_title = __('Skrill (Moneybookers)', $this->text_domain);
		$this->method_description =
			__('Allows your customers to pay through Skrill (Moneybookers) gateway. ' .
				 'This plugin has been developed by <a href="http://aelia.co">' .
				 'Aelia</a>. If you find it useful, and would like to support its ' .
				 'development, please feel free to <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8ND89AA8B8QJ">'.
				 'make a donation</a>. Thanks.', $this->text_domain);

		$this->icon = apply_filters('woocommerce_skrill_icon', $this->url('images') . '/skrill.png');
		$this->has_fields = false;
		$this->test_url = 'http://www.moneybookers.com/app/test_payment.pl';
		$this->live_url = 'https://www.moneybookers.com/app/payment.pl';
		// Set URL for successful and failed payment notifications(using WC API)
		$this->status_url = add_query_arg(array('wc-api' => 'WC_Gateway_Skrill'), home_url('/'));

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');
		$this->email = $this->get_option('email');
		$this->secret_word = $this->get_option('secret_word');
		$this->default_language = $this->get_option('default_language');
		$this->test_mode = $this->get_option('test_mode');
		$this->debug = $this->get_option('debug');
		$this->skrill_mode = $this->get_option('skrill_mode');

		$this->set_hooks();
	}

	protected function set_hooks() {
		// "Save options" hook
		if($this->woocommerce()->version >= '2') {
			// WooCommerce 2.0+
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		}
		else {
			// WooCommerce 1.6
			add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));
		}

		// Payment listener/API hook
		add_action('woocommerce_api_wc_gateway_skrill', array($this, 'process_skrill_notification'));
		add_action('woocommerce_receipt_skrill', array($this, 'render_receipt_page'));
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		if(function_exists('wc_get_log_file_path')) {
			$log_file_path = wc_get_log_file_path($this->log_id);
		}
		else {
			$log_file_path = sprintf('woocommerce/logs/%s-%s.txt', $this->log_id, sanitize_file_name(wp_hash($this->log_id)));
		}

		$this->form_fields = array(
			'enabled' => array(
				'title' => __('Enable/Disable', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable Skrill (Moneybookers) checkout', 'woocommerce'),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __('Title', 'woocommerce'),
				'type' => 'text',
				'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
				'default' => __('Credit Card (Skrill)', 'woocommerce'),
				'desc_tip' => true,
			),
			'description' => array(
				'title' => __('Description', 'woocommerce'),
				'type' => 'textarea',
				'description' => __('This controls the description which the user sees during checkout.', 'woocommerce'),
				'default' => __('Pay securely with your credit card.', 'woocommerce')
			),
			'email' => array(
				'title' => __('Skrill Email', 'woocommerce'),
				'type' => 'text',
				'description' => __('The email address associated with your Skrill account.', 'woocommerce'),
				'default' => '',
				'desc_tip' => true,
			),
			'secret_word' => array(
				'title' => __('Secret Word', 'woocommerce'),
				'type' => 'text',
				'description' => __('The Secret Word that will be used to authenticate the communication with Skrill.', 'woocommerce'),
				'default' => '',
				'desc_tip' => true,
			),
			'default_language' => array(
				'title' => __('Default language for Skrill portal', 'woocommerce'),
				'type' => 'select',
				'description' => __('This will be the default language which will be used to display the Skrill portal to ' .
														'your customers, if they choose a language that Skrill does not support.', 'woocommerce'),
				'default' => '',
				'options' => $this->supported_languages,
			),
			'testing' => array(
				'title' => __('Gateway Testing', 'woocommerce'),
				'type' => 'title',
				'description' => '',
			),
			'test_mode' => array(
				'title' => __('Use Skrill test portal', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable Skrill test portal', 'woocommerce'),
				'default' => 'yes',
				'description' => __('Skrill test portal can be used to test payments.', 'woocommerce'),
			),
			'debug' => array(
				'title' => __('Debug Log', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Enable debug mode', 'woocommerce'),
				'default' => 'yes',
				'description' => sprintf(__('Enable debug mode and log Skrill events to file ' .
																		'<code>%s</code>.', 'woocommerce'),
																 $log_file_path),
			),
		);
	}

	/**
	 * Returns a value indicating the the Gateway is available or not.
	 *
	 * @return bool
	 */
	public function is_available() {
		if(!parent::is_available()) {
			return false;
		}

		$errors = array();
		if(empty($this->email)) {
			$errors[] =	__('Skrill Gateway - Email address is not configured.', $this->texdomain);
		}

		if(empty($this->secret_word)) {
			$errors[] =	__('Skrill Gateway - Secret word is not configured.', $this->texdomain);
		}

		$currency = get_woocommerce_currency();
		if(!$this->is_currency_supported($currency)) {
			$errors[] =	sprintf(__('Skrill Gateway - Currency not supported: "%s".', $this->texdomain),
													$currency);
		}

		// If, for any reason, the gateway is enabled, but not available due to
		// misconfiguration, log the issues and trigger a warning so that the Admin
		// can take the appropriate corrective action(s)
		if(!empty($errors)) {
			$errors[] = __('Skrill Gateway disabled.', $this->text_domain);

			foreach($errors as $error_msg) {
				$this->log($error_msg);
				trigger_error($error_msg, E_USER_WARNING);
			}

			return false;
		}

		return true;
	}

	/**
	 * Given a 2-digits ISO country code, returns the corresponding 3-digits ISO
	 * code required by Skrill.
	 *
	 * @param string $two_digits_country_code A 2-digits ISO country code.
	 * @return string
	 * @link https://www.skrill.com/fileadmin/templates/main/res/material/documents/pdf/getting-started/skrill-integration-manual-en.pdf
	 */
	protected function get_three_digits_country_code($two_digits_country_code) {
		// Return the 3-digits country code matchin the 2-digits one. If not found,
		// return the original one (not really ideal, but better than nothing)
		if(isset($this->country_codes_map[$two_digits_country_code])) {
			return $this->country_codes_map[$two_digits_country_code];
		}
		else {
			return $two_digits_country_code;
		}
	}

	/**
	 * Returns the billing fields that will be passed to Skrill.
	 *
	 * @param WC_Order order The order to process.
	 * @return array
	 */
	protected function get_billing_fields(WC_Order $order) {
		// Billing fields
		$billing_fields = array(
			'pay_from_email' => $order->billing_email,
			'bcompany' => $order->billing_company,
			'firstname' => $order->billing_first_name,
			'lastname' => $order->billing_last_name,
			'address' => $order->billing_address_1,
			'address2' => $order->billing_address_2,
			'phone_number' => $order->billing_phone,
			'postal_code' => $order->billing_postcode,
			'city' => $order->billing_city,
			'state' => $order->billing_state,
			'country' => $this->get_three_digits_country_code($order->billing_country),
		);

		return $billing_fields;
	}

	/**
	 * Get arguments to pass to Skrill.
	 *
	 * @param WC_Order order An order object.
	 * @return array
	 */
	protected function get_skrill_args(WC_Order $order) {
		$this->log(sprintf(__('Skrill - Generating payment form for order #%s.'),
											 $order->id),
							 true);

		$currency = get_woocommerce_currency();

		$order_cancel_url = $order->get_cancel_order_url();
		$order_total = number_format($order->order_total, 2, '.', '');

		$skrill_args = array(
			'pay_to_email' => $this->email,
			'language' => $this->get_gateway_language(),
			'currency' => $currency,
			'return_url' => $this->get_return_url($order),
			'cancel_url' => $order_cancel_url,
			'status_url' => $this->status_url,

			// Order details
			'merchant_fields' => 'order_key',
			'order_key' => $order->order_key,
			'transaction_id' => $order->id,
			'amount' => $order_total,
			'amount2' => $order_total,
			'amount2_description' => sprintf(__('Order #%s', $this->text_domain), $order->id),

			'comments' => $order->customer_note,
		);

		// Add billing details
		$skrill_args = array_merge($skrill_args, $this->get_billing_fields($order));

		$skrill_args = apply_filters('wc_gateway_skrill_form_fields', $skrill_args);

		$this->log(sprintf(__('Skrill arguments (JSON): %s', $this->text_domain),
											 json_encode($skrill_args)),
							 true);
		return $skrill_args;
	}

	/**
	 * Process the payment and return the result
	 *
	 * @access public
	 * @param int $order_id
	 * @return array
	 */
	function process_payment($order_id) {
		$order = new Aelia_Order($order_id);

		$redirect_url = add_query_arg(array('order' => $order->id,
																				'key' => $order->order_key,),
																	get_permalink(woocommerce_get_page_id('pay')));

		// Redirect to receipt page, which will contain the form that will actually
		// bring to the Skrill portal
		return array(
			'result' 	=> 'success',
			'redirect'	=> $redirect_url,
		);
	}

	/**
	 * Renders the intermediate page which will contain the form that, when
	 * submitted, will redirect the Customer to the Skrill portal.
	 *
	 * @param int order_id The ID of order placed by the Customer.
	 */
	public function render_receipt_page($order_id) {
		echo '<p>';
		echo __('Thank you for your order', $this->text_domain);
		echo '</p>';

		echo '<noscript>';
		echo __('Please click on the the button below to ' .
						'proceed to a secure page where you will be able to complete your payment.',
						$this->text_domain);
		echo '</noscript>';

		echo '<p id="skrill_redirect_message">';
		echo __('You will now be redirected to a secure page where you will be able to ' .
						'complete your payment', $this->text_domain);
		echo '</p>';

		echo $this->generate_skrill_form($order_id);
	}

	/**
	 * Builds the form that will submit the order details to the Skrill server.
	 *
	 * @param int order_id The ID of order placed by the Customer.
	 * @return string The HTML for the form.
	 */
	protected function generate_skrill_form($order_id) {
		// Render Skrill form
		//var_dump(implode("\n", $skrill_fields));die();

		$order = new Aelia_Order($order_id);
		// Retrieve all the arguments to pass to Skrill
		$skrill_args = $this->get_skrill_args($order);

		require($this->path('views') . '/gateways/skrill-receipt-page-view.php');
	}

	protected function is_response_hash_valid($posted_data) {
		$fields_to_hash = array(
			get_value('merchant_id', $posted_data),
			get_value('transaction_id', $posted_data),
			strtoupper(md5($this->secret_word)),
			get_value('mb_amount', $posted_data),
			get_value('mb_currency', $posted_data),
			get_value('status', $posted_data),
		);

		$hash = strtoupper(md5(implode('', $fields_to_hash)));

		$this->log(sprintf('Hash check. Fields to hash: %s. Hash: %s. Received hash: %s.',
											 json_encode($fields_to_hash),
											 $hash,
											 get_value('md5sig', $posted_data)),
							 true);

		return ($hash == get_value('md5sig', $posted_data));
	}

	/**
	 * Checks code returned by Skrill and returns a boolean indicating if
	 * payment was successful or not.
	 *
	 * @param array posted_data The data sent back by Skrill.
	 * @return bool
	 */
	protected function is_payment_successful($posted_data) {
		$result = true;
		$this->log(__('Skrill - Checking response data.'), true);
		$this->log(sprintf(__('Posted data (JSON): "%s".'), json_encode($posted_data)), true);

		$order_id = get_value('transaction_id', $posted_data);
		$order = new Aelia_Order($order_id);
		$this->log(sprintf(__('Order ID: "%s".'), $order_id), true);
		$this->log(sprintf(__('WooCommerce Order: "%s".'), json_encode($order)), true);

		// Check response hash
		$this->log(__('Checking response hash.'), true);
		if(!$this->is_response_hash_valid($posted_data)) {
			$result = false;
			$result_str = __('Response signature check failed.', $this->text_domain);
		}

		// Check that received payment recipient email matches the expected one
		if($result) {
			$this->log(__('Checking that received payment recipient email matches the expected one.'), true);
			$payment_recipient_email = get_value('pay_to_email', $posted_data);
			if($payment_recipient_email != $this->email) {
				$result = false;
				$result_str = sprintf(__('Payment recipient email mismatch. Expected email: ' .
																 '"%s". Received email: "%s".', $this->text_domain),
															$this->email,
															$payment_recipient_email);
			}
		}

		// Check that payment status is "successful"
		if($result) {
			$this->log(__('Checking that payment status is "successful".'), true);
			// Check payment status
			$status = get_value('status', $posted_data);
			if($status != self::PAYMENT_SUCCESSFUL) {
				$result = false;
				$result_str = sprintf(__('Payment failed. Failure reason code: "%s".', $this->text_domain),
															get_value('failed_reason_code', $posted_data, __('n/a', $this->text_domain)));
			}
		}

		// Check that received order key matches the one from the order
		if($result) {
			$this->log(__('Checking that that received order key matches the one from the order.'), true);
			$posted_order_key = get_value('order_key', $posted_data);

			if($posted_order_key != $order->order_key) {
				$result = false;
				$result_str = sprintf(__('Order key mismatch. Expected order key: "%s".' .
																 'Received order key: "%s".', $this->text_domain),
															$order->order_key,
															$posted_order_key);
			}
		}

		// Check that the total amount matches the one from the order
		if($result) {
			$this->log(__('Checking that the total amount matches the one from the order.'), true);
			$posted_order_total = get_value('amount', $posted_data);
			$order_total = $order->get_total();

			$this->log(sprintf(__('WooCommerce Order: "%s".'), json_encode($order)), true);
			$this->log(sprintf(__('WooCommerce Order Total: "%s". Posted order total: "%s".'),
												 $order_total,
												 $posted_order_total
												 ), true);

			$order_total = number_format($order_total, 2, '.', '');
			$this->log(sprintf(__('Formatted WooCommerce Order Total: "%s".'), $order_total), true);

			if($posted_order_total != $order_total) {
				$result = false;
				$result_str = sprintf(__('Order total mismatch. Expected order total: "%s".' .
																 'Received order total: "%s".', $this->text_domain),
															$order_total,
															$posted_order_total);
			}
		}

		if($result) {
			$result_str = __('Order payment was successful.', $this->text_domain);
		}
		$this->log($result_str, true);

		return $result;
	}

	/**
	 * Callback function, invoked when the User is redirected back to the shop
	 * after payment.
	 */
	public function process_skrill_notification() {
		$posted_data = $_POST;
		if($this->is_payment_successful($posted_data)) {
			$order_id = get_value('transaction_id', $posted_data);
			$order = new Aelia_Order($order_id);

			// Process successful payment
			$this->complete_order($order, $posted_data);

			// Redirect to "thank you" page
			wp_redirect($this->get_return_url($order));

			// This check is performed to allow testing of this public method. PHPUnit
			// will call it directly, the "exit" would cause the testing to fail. By
			// passing the "aelia_testing" parameter together with the data, we know
			// when the method is being tested, or running for real. It's a violation of
			// testing principles (the class behaves differently when tested), but it's
			// the only way to test this method
			if(get_value('aelia_testing', $posted_data, false) == true) {
				return true;
			}
			else {
				exit;
			}
		}
		//var_dump("SKRILL NOTIFICATION");
		//var_dump("GET", $_GET);
		//var_dump("POST", $_POST);die();
	}

	/**
	 * Stores additional details, passed by Skrill, against the order, for future
	 * reference.
	 *
	 * @param WC_Order An order object.
	 * @param array payment_details A list of payment details.
	 */
	protected function store_additional_payment_details(WC_Order $order, array $payment_details) {
		foreach($payment_details as $key => $value) {
			update_post_meta($order->id, $key, $value);
		}
	}

	/**
	 * Sets an order as "paid", adding a note with the ID of the transaction.
	 * Invoked upon the successful completion of a payment.
	 *
	 * @param WC_Order order The order.
	 * @param array posted_data An array of the data received with the POST.
	 */
	protected function complete_order($order, $posted_data) {
		// Add order note upon successful completion of payment
		$approval_code = get_value('approval_code', $posted_data);
		$order->add_order_note(sprintf(__('Skrill Payment Completed. Skrill transaction ID: "%s".',
																			$this->text_domain),
																	 get_value('mb_transaction_id', $posted_data)));
		// Set order status to "payment complete". This also reduces stock automatically.
		$order->payment_complete();

		// Store additional payment details, for reference
		$additional_details = array(
			'Skrill transaction ID' => get_value('mb_transaction_id', $posted_data),
			'Payer Skrill address' => get_value('pay_from_email', $posted_data),
			'Payment type' => get_value('payment_type', $posted_data),
		);
		$this->store_additional_payment_details($order, $additional_details);

		// Empty cart
		$this->woocommerce()->cart->empty_cart();
	}
}
