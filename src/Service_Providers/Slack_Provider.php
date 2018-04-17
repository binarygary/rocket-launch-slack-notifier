<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Slack\Post_Message;
use BinaryGary\Rocket\Slack\Redirect_URI;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Slack_Provider implements ServiceProviderInterface {

	const REDIRECT_URI = 'slack.redirect_uri';
	const POST_MESSAGE = 'slack.post_message';

	public function register( Container $container ) {
		$container[ self::REDIRECT_URI ] = function () {
			return new Redirect_URI();
		};

		$container[ self::POST_MESSAGE ] = function () {
			return new Post_Message();
		};
	}

}
