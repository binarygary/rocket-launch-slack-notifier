<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Endpoints\Events;
use BinaryGary\Rocket\Endpoints\OAuth;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Endpoints_Provider implements ServiceProviderInterface {

	const ENDPOINTS_OAUTH  = 'endpoints.oauth';
	const ENDPOINTS_EVENTS = 'endpoints.events';

	public function register( Container $container ) {
		$container[ self::ENDPOINTS_OAUTH ] = function () use ( $container ) {
			return new OAuth( $container[ Slack_Provider::POST_MESSAGE ], $container[ Slack_Provider::REDIRECT_URI ] );
		};

		$container[ self::ENDPOINTS_EVENTS ] = function () {
			return new Events();
		};

		add_action( 'rest_api_init', function () use ( $container ) {
			$container[ self::ENDPOINTS_OAUTH ]->register();
			$container[ self::ENDPOINTS_EVENTS ]->register();
		} );

	}

}
