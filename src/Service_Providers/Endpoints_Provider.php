<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Endpoints\Events;
use BinaryGary\Rocket\Endpoints\OAuth;
use BinaryGary\Rocket\Launch_Library\Active_Pad;
use BinaryGary\Rocket\Launch_Library\Active_Provider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Endpoints_Provider implements ServiceProviderInterface {

	const ENDPOINTS_OAUTH = 'endpoints.oauth';

	const ENDPOINT_EVENTS_COLLECTION = 'endpoints.events.collection';
	const ENDPOINTS_EVENTS           = 'endpoints.events';
	const ENDPOINTS_EVENTS_HELP      = 'endpoints.events.help';

	const ENDPOINT_EVENTS_SPACEX = 'endpoint.events.spacex';

	const ACTIVE_PROVIDERS = 'launch_library.active_provider';
	const ACTIVE_PADS      = 'launch_library.active_pads';

	public function register( Container $container ) {
		$container[ self::ENDPOINTS_OAUTH ] = function () use ( $container ) {
			return new OAuth( $container[ Slack_Provider::POST_MESSAGE ], $container[ Slack_Provider::REDIRECT_URI ] );
		};

		$container[ self::ENDPOINT_EVENTS_COLLECTION ] = function () use ( $container ) {
			return new Events\Collection();
		};

		$container[ self::ENDPOINTS_EVENTS_HELP ] = function () use ( $container ) {
			return new Events\Help();
		};

		$container[ self::ENDPOINTS_EVENTS ] = function () use ( $container ) {
			return new Events( $container[ Slack_Provider::POST_MESSAGE ], $container[ self::ENDPOINT_EVENTS_COLLECTION ], $container[ self::ENDPOINTS_EVENTS_HELP ] );
		};

		$container[ self::ACTIVE_PROVIDERS ] = function () {
			return new Active_Provider();
		};

		$container[ self::ACTIVE_PADS ] = function () {
			return new Active_Pad();
		};

		add_action( 'rest_api_init', function () use ( $container ) {
			foreach ( $container[ self::ACTIVE_PROVIDERS ]->get_active() as $agency => $attributes ) {
				$container[ $agency ] = function () use ( $container, $attributes ) {
					return new Events\Launch( $container[ Launch_Library_Provider::LAUNCH ], $attributes );
				};
				$container[ self::ENDPOINT_EVENTS_COLLECTION ]->add( $container[ $agency ] );
			}

//			foreach ( $container[ self::ACTIVE_PADS ]->get_active() as $pad => $attributes ) {
//				$container[ $pad ] = function () use ( $container, $attributes ) {
//					return new Events\Launch( $container[ Launch_Library_Provider::LAUNCH ], $attributes );
//				};
//				$container[ self::ENDPOINT_EVENTS_COLLECTION ]->add( $container[ $pad ] );
//			}

			$container[ self::ENDPOINTS_OAUTH ]->register();
			$container[ self::ENDPOINTS_EVENTS ]->register();
		} );

	}

}
