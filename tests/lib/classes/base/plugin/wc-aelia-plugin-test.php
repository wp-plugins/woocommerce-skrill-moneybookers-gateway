<?php

/**
 * Tests for the base plugin class.
 */
class WC_Aelia_Plugin_Test extends WP_UnitTestCase {
	const SETTINGS_KEY = 'wc-aelia-plugin-test';
	const TEXT_DOMAIN = 'wc-aelia-plugin-test';

	public function setUp() {
		parent::setUp();

		$settings_page_renderer = new WC_Aelia_Settings_Renderer();
		$this->settings = new WC_Aelia_Settings(self::SETTINGS_KEY,
																						self::TEXT_DOMAIN,
																						$settings_page_renderer);
		$this->messages = new WC_Aelia_Messages();

		$this->plugin = new WC_Aelia_Plugin($this->settings, $this->messages);

		$plugin_class = get_class($this->plugin);
		$GLOBALS[$plugin_class::$plugin_slug] = $this->plugin;
	}

	public function test_settings_controller() {
		$controller = $this->plugin->settings_controller();
		$this->assertSame($controller, $this->settings);
	}

	public function test_messages_controller() {
		$controller = $this->plugin->messages_controller();
		$this->assertSame($controller, $this->messages);
	}

	public function test_instance() {
		$plugin_instance = $this->plugin->instance();
		$this->assertSame($plugin_instance, $this->plugin);
	}

	public function test_settings() {
		$controller = WC_Aelia_Plugin::settings();
		$this->assertSame($controller, $this->settings);
	}

	public function test_messages() {
		$controller = WC_Aelia_Plugin::messages();
		$this->assertSame($controller, $this->messages);
	}

	public function test_get_error_message() {
		$message = $this->plugin->get_error_message(WC_Aelia_Messages::ERR_FILE_NOT_FOUND);
		$this->assertTrue(!empty($message));
	}

	/* The tests below simply check that the methods run without errors. */
	public function test_wordpress_loaded() {
		$this->plugin->wordpress_loaded();
		$this->assertTrue(true);
	}

	public function test_woocommerce_loaded() {
		$this->plugin->woocommerce_loaded();
		$this->assertTrue(true);
	}

	public function test_plugins_loaded() {
		$this->plugin->plugins_loaded();
		$this->assertTrue(true);
	}

	public function test_include_template_functions() {
		$this->plugin->include_template_functions();
		$this->assertTrue(true);
	}

	public function test_register_widgets() {
		$this->plugin->register_widgets();
		$this->assertTrue(true);
	}

	public function test_register_styles() {
		$this->plugin->register_styles();

		$admin_styles_registered = wp_style_is(WC_Aelia_Plugin::$plugin_slug . '-admin', 'registered');
		$this->assertTrue($admin_styles_registered);

		$frontend_styles_registered = wp_style_is(WC_Aelia_Plugin::$plugin_slug . '-frontend', 'registered');
		$this->assertTrue($frontend_styles_registered);
	}

	public function test_load_admin_scripts() {
		$this->plugin->load_admin_scripts();

		$admin_styles_enqueued = wp_style_is(WC_Aelia_Plugin::$plugin_slug . '-admin', 'enqueued');
		$this->assertTrue($admin_styles_enqueued);
	}

	public function test_load_frontend_scripts() {
		$this->plugin->load_frontend_scripts();

		$frontend_styles_enqueued = wp_style_is(WC_Aelia_Plugin::$plugin_slug . '-frontend', 'enqueued');
		$this->assertTrue($frontend_styles_enqueued);
	}

	public function test_setup() {
		$this->plugin->setup();
		$this->assertTrue(true);
	}

	public function test_cleanup() {
		$this->plugin->cleanup();
		$this->assertTrue(true);
	}

	public function test_is_woocommerce_active() {
		$this->assertTrue(is_bool(WC_Aelia_Plugin::is_woocommerce_active()));
	}

	public function test_path() {
		$plugin_path = $this->plugin->path('plugin');
		$this->assertTrue(is_string($plugin_path) && !empty($plugin_path));
	}

	public function test_url() {
		$plugin_url = $this->plugin->url('plugin');
		$this->assertTrue(is_string($plugin_url) && !empty($plugin_url));
	}

	/**
	 * @expectedException Aelia_NotImplementedException
	 */
	public function test_factory() {
		WC_Aelia_Plugin::factory();
	}

	public function test_plugin_dir() {
		$plugin_dir = $this->plugin->plugin_dir();
		$this->assertTrue(!empty($plugin_dir));
	}
}
