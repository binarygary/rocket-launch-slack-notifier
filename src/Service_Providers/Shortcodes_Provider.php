<?php

namespace BinaryGary\Rocket\Service_Providers;

use BinaryGary\Rocket\Shortcodes\Last_Launch;
use BinaryGary\Rocket\Shortcodes\Next_Five;
use BinaryGary\Rocket\Shortcodes\Slack_Button;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class Shortcodes_Provider implements ServiceProviderInterface {

	const SLACK_BUTTON = 'shortcode.slack_button';
	const NEXT_FIVE    = 'shortcode.next_five';
	const LAST_LAUNCH  = 'shortcode.las_launch';

	public function register( Container $container ) {
		$container[ self::SLACK_BUTTON ] = function () use ( $container ) {
			return new Slack_Button( $container[ Endpoints_Provider::ENDPOINTS_OAUTH ] );
		};

		$container[ self::LAST_LAUNCH ] = function () {
			return new Last_Launch();
		};

		$container[ self::NEXT_FIVE ] = function () {
			return new Next_Five();
		};

		add_action( 'init', function () use ( $container ) {
			$container[ self::SLACK_BUTTON ]->generate();
			$container[ self::NEXT_FIVE ]->generate();
			$container[ self::LAST_LAUNCH ]->generate();
		} );
	}

}
