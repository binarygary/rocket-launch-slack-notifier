<?php

namespace BinaryGary\Rocket\Service_Providers;

use BinaryGary\Rocket\V2\Space_Devs\Retriever;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Space_Devs_Provider implements ServiceProviderInterface {

	const CRON      = 'space_devs.cron';
	const RETRIEVER = 'space_devs.retriever';

	public function register( Container $container ) {
		$container[ self::RETRIEVER ] = function () use ( $container ) {
			return new Retriever(
				$container[ Slack_Provider::WEBHOOKS ]
			);
		};

		add_action( 'init', function () {
			if ( ! wp_next_scheduled( 'launch_cron_v2' ) ) {
				wp_schedule_event( time(), 'minutely', 'launch_cron_v2' );
			}
		} );

		add_action( 'launch_cron_v2', function () use ( $container ) {
			$container[ self::RETRIEVER ]->get_launches();
		} );
	}
}
