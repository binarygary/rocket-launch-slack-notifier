<?php

namespace BinaryGary\Rocket\Service_Providers;

use BinaryGary\Rocket\Shortcodes\Slack_Button;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class Shortcodes_Provider implements ServiceProviderInterface {

	const SLACK_BUTTON = 'slack_button';

	public function register( Container $container ) {
		$container[ self::SLACK_BUTTON ] = function () use ( $container ) {
			return new Slack_Button( $container[ Slack_Provider::REDIRECT_URI ] );
		};

		add_action( 'init', function () use ( $container ) {
			$container[ self::SLACK_BUTTON ]->generate();
		} );
	}

}
