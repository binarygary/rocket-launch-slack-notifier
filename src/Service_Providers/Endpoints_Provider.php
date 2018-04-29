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

	const ENDPOINT_EVENTS_SPACEX = 'endpoint.events.spacex';

	const PROVIDERS_LAUNCHPOINT = 'https://launchlibrary.net/1.4/lsp?limit=100';
	const LOCATION_LAUNCHPOINT  = 'https://launchlibrary.net/1.4/location?limit=100';

	public function register( Container $container ) {
		$container[ self::ENDPOINTS_OAUTH ] = function () use ( $container ) {
			return new OAuth( $container[ Slack_Provider::POST_MESSAGE ], $container[ Slack_Provider::REDIRECT_URI ] );
		};

		$container[ self::ENDPOINT_EVENTS_COLLECTION ] = function () use ( $container ) {
			return new Events\Collection();
		};

		$container[ self::ENDPOINTS_EVENTS ] = function () use ( $container ) {
			return new Events( $container[ Slack_Provider::POST_MESSAGE ], $container[ self::ENDPOINT_EVENTS_COLLECTION ] );
		};

		add_action( 'rest_api_init', function () use ( $container ) {
			$result    = wp_remote_get( self::PROVIDERS_LAUNCHPOINT );
			$providers = json_decode( $result['body'] );
			foreach ( $providers->agencies as $agency ) {
				$container[ sanitize_title( $agency->name ) ] = function () use ( $container, $agency ) {
					return new Events\Launch( $container[ Launch_Library_Provider::LAUNCH ], [
						'term'          => $agency->name,
						'request'       => 'lsp',
						'request_value' => $agency->id,
					] );
				};
				$container[ self::ENDPOINT_EVENTS_COLLECTION ]->add( $container[ sanitize_title( $agency->name ) ] );
			}

			$result    = wp_remote_get( self::LOCATION_LAUNCHPOINT );
			$locations = json_decode( $result['body'] );
			foreach ( $locations->locations as $location ) {
				$container[ sanitize_title( $location->name ) ] = function () use ( $container, $location ) {
					return new Events\Launch( $container[ Launch_Library_Provider::LAUNCH ], [
						'term'          => $location->name,
						'request'       => 'locationid',
						'request_value' => $location->id,
					] );
				};
				$container[ self::ENDPOINT_EVENTS_COLLECTION ]->add( $container[ sanitize_title( $location->name ) ] );
			}

			$container[ self::ENDPOINTS_OAUTH ]->register();
			$container[ self::ENDPOINTS_EVENTS ]->register();
		} );

	}

}
