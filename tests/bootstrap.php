<?php

/**
 * Enabled the plugins required by the plugin being tested.
 *
 * @param array required_plugins An array of the required plugins.
 */
if(!function_exists('enable_require_plugins')) {
	function enable_required_plugins(array $required_plugins) {
		foreach($required_plugins as $plugin) {
			printf("Activating plugin '%s'...\n", $plugin);
			$result = activate_plugin($plugin);
			if($result == null) {
				echo "Success.\n";
			}
			else {
				$errors = $result->get_error_messages();

				printf("Could not activate plugin '%s'. See errors below.\n", $plugin);
				exit(implode("\n", $errors));
			}
		}
	}
}

// Disable reporting of everything but errors. This is done because bootstrapping
// WP at this stage may cause warnings to be issued about "headers already
// sent by", which we can safely ignore
$error_reporting_original = error_reporting();
error_reporting(E_ERROR);

// The path to wordpress-tests
// Path to wordpress unit test framework
$path = '/src/wp_unit/bootstrap.php';

if(file_exists($path)) {
  require_once $path;
} else {
   exit("Couldn't find path to wp_unit bootstrap.php. Expected location: '$path'.\n");
}

// Restore original error reporting level
error_reporting($error_reporting_original);

// Load Composer Autoloader
$composer_autoloader = realpath(__DIR__ . '/../src/vendor/autoload.php');
if(file_exists($composer_autoloader)) {
  require_once $composer_autoloader;
} else {
   exit("Couldn't find path to composer autoloader. Expected location: '$composer_autoloader'.\n");
}

// Try to activate all required plugins
$required_plugins = array(
	'woocommerce/woocommerce.php',
);

enable_required_plugins($required_plugins);
