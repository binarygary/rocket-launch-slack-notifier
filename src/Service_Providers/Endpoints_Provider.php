<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Endpoints\Events;
use BinaryGary\Rocket\Endpoints\OAuth;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Endpoints_Provider implements ServiceProviderInterface {

	const ENDPOINTS_OAUTH = 'endpoints.oauth';

	const ENDPOINT_EVENTS_COLLECTION = 'endpoints.events.collection';
	const ENDPOINTS_EVENTS           = 'endpoints.events';

	const ENDPOINT_EVENTS_SPACEX     = 'endpoint.events.spacex';

	public function register( Container $container ) {
		$container[ self::ENDPOINTS_OAUTH ] = function () use ( $container ) {
			return new OAuth( $container[ Slack_Provider::POST_MESSAGE ], $container[ Slack_Provider::REDIRECT_URI ] );
		};

		$container[ self::ENDPOINT_EVENTS_COLLECTION ] = function () use ( $container ) {
			return new Events\Collection();
		};

		$events = [
			self::ENDPOINT_EVENTS_SPACEX => Events\SpaceX::class,
		];

		foreach ( $events as $event => $class ) {
			$container[ $event ] = function () use ( $container, $class ) {
				return new $class( $container[ Launch_Library_Provider::LAUNCH ] );
			};
			add_action( 'init', function() use ( $container, $event ) {
				$container[ self::ENDPOINT_EVENTS_COLLECTION ]->add( $container[ $event ] );
			} );
		}

		$container[ self::ENDPOINTS_EVENTS ] = function () use ( $container ) {
			return new Events( $container[ Slack_Provider::POST_MESSAGE ], $container[ self::ENDPOINT_EVENTS_COLLECTION ] );
		};

		add_action( 'rest_api_init', function () use ( $container ) {
			$container[ self::ENDPOINTS_OAUTH ]->register();
			$container[ self::ENDPOINTS_EVENTS ]->register();
		} );

	}

}
