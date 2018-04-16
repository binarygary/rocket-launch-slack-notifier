<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Post_Types\Slack_URL;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Post_Type_Provider implements ServiceProviderInterface {

	const SLACK_URL = 'post_types.slack_urls';

	public function register( Container $container ) {
		$container[ self::SLACK_URL ] = function () {
			return new Slack_URL();
		};
		add_action( 'init', function () use ( $container ) {
			$container[ self::SLACK_URL ]->register();
		} );
	}

}
