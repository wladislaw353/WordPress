<?php

use RobotsTxt\RobotsTxtPlugin;

/**
 *
 * Plugin Name:       Robots.txt Editor
 * Description:       Robots.txt for WordPress
 * Version:           1.1.4
 * Author:            Processby
 * Author URI:        https://processby.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       robots-txt-editor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

call_user_func( function () {

	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	$main = new RobotsTxtPlugin( __FILE__ );

	register_activation_hook( __FILE__, [ $main, 'activate' ] );

	register_deactivation_hook( __FILE__, [ $main, 'deactivate' ] );

	register_uninstall_hook( __FILE__, [ RobotsTxtPlugin::class, 'uninstall' ] );

	$main->run();
} );