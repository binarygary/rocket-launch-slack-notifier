<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Endpoints\OAuth;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Endpoints_Provider implements ServiceProviderInterface {

	const ENDPOINTS_OAUTH = 'endpoints.oauth';

	private function endpoints() {
		return [
			self::ENDPOINTS_OAUTH => OAuth::class,
		];
	}

	public function register( Container $container ) {
		foreach ( $this->endpoints() as $endpoint => $class ) {
			$container[ $endpoint ] = function () use ( $class ) {
				return new $class();
			};

			add_action( 'rest_api_init', function () use ( $container, $endpoint ) {
				$container[ $endpoint ]->register();
			} );
		}
	}

}
