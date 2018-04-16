<?php

namespace BinaryGary\Rocket\Service_Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use BinaryGary\Rocket\Settings\Defaults;

class Settings_Provider implements ServiceProviderInterface {

	const DEFAULTS = 'settings.defaults';

	public function register( Container $container ) {
		$container[ self::DEFAULTS ] = function () {
			return new Defaults();
		};

		add_action( 'admin_menu', function () use ( $container ) {
			$container[ self::DEFAULTS ]->create_menu();
		} );

		add_action( 'admin_init', function () use ( $container ) {
			$container[ self::DEFAULTS ]->register_settings();
		} );
	}

}
