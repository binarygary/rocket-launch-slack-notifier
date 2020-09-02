<?php

namespace BinaryGary\Rocket\Service_Providers;

use BinaryGary\Rocket\Settings\Defaults;
use BinaryGary\Rocket\Twitter\Message;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Twitter_Service_Provider implements ServiceProviderInterface {

	const TWITTER         = 'twitter';
	const TWITTER_MESSAGE = 'twitter.message';

	public function register( Container $container ) {

		$container[ self::TWITTER ] = function () {
			try {
				return new \Twitter(
					get_option( Defaults::TWITTER_CONUMER_KEY ),
					get_option( Defaults::TWITTER_CONUMER_SECRET ),
					get_option( Defaults::ACCESS_TOKEN ),
					get_option( Defaults::ACCESS_TOKEN_SECRET )
				);
			} catch ( \Exception $exception ) {
				return null;
			}
		};

		$container[ self::TWITTER_MESSAGE ] = function () use ( $container ) {
			return new Message( $container[ self::TWITTER ] );
		};
	}
}