<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Slack\Redirect_URI;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Slack_Provider implements ServiceProviderInterface {

	const REDIRECT_URI = 'slack.redirect_uri';

	public function register( Container $container ) {
		$container[ self::REDIRECT_URI ] = function () use ( $container ) {
			return new Redirect_URI( $container[ Endpoints_Provider::ENDPOINTS_OAUTH ] );
		};
	}

}
