<?php
/*
Plugin Name: Rocket Launch Slack Notifier
Description: Send launch notices to slack.
Author:      Gary Kovar
Version:     1.0
Author URI:  https://www.binarygary.com/
*/

require_once trailingslashit( __DIR__ ) . 'vendor/autoload.php';

// Start the core plugin
add_action( 'plugins_loaded', function () {
	textsmash()->init();
}, 1, 0 );

function textsmash() {
	return \TextSmash\Lists\Core::instance( new Pimple\Container( [ 'plugin_file' => __FILE__ ] ) );
}
