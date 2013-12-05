<?php

/**
 * Tests for the base settings controller.
 */
class WC_Aelia_Settings_Test extends WP_UnitTestCase {
	const SETTINGS_KEY = 'test_settings';
	const TEXT_DOMAIN = 'test_domain';

	protected function get_test_settings($key = null) {
		$test_settings = array(
			'int_param' => 1,
			'string_param' => 'string_val',
		);

		if($key === null) {
			return $test_settings;
		}
		else {
			return $test_settings[$key];
		}
	}

	public function setUp() {
		parent::setUp();

		$renderer = new WC_Aelia_Settings_Renderer();
		$this->settings = new WC_Aelia_Settings(self::SETTINGS_KEY,
																						self::TEXT_DOMAIN,
																						$renderer);
	}

	public function test_default_settings() {
		$this->assertTrue(is_array($this->settings->default_settings()));
	}

	public function test_save() {
		$this->settings->save($this->get_test_settings());
		$this->assertEquals($this->get_test_settings(), $this->settings->load());
	}

	public function test_load() {
		$this->settings->save($this->get_test_settings());
		$this->assertEquals($this->get_test_settings(), $this->settings->load());
	}

	public function test_current_settings() {
		$this->settings->save($this->get_test_settings());
		$this->assertEquals($this->get_test_settings('string_param'),
												$this->settings->current_settings('string_param'));
	}

	public function test_validate_settings() {
		$test_settings = $this->get_test_settings();
		$this->assertEquals($test_settings, $this->settings->validate_settings($test_settings));
	}

	public function test_delete() {
		$this->settings->delete();
		$this->assertTrue($this->settings->load() === false);
	}
}
