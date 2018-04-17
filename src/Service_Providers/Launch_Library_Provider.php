<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Launch_Library\Retriever;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Launch_Library_Provider implements ServiceProviderInterface {

	const RETRIEVER = 'launch_library.retriever';

	public function register( Container $container ) {
		$container[ self::RETRIEVER ] = function () use ( $container ) {
			return new Retriever( $container[ Slack_Provider::WEBHOOKS ] );
		};

		add_action( 'init', function() {
			if ( ! wp_next_scheduled( 'launch_cron' ) ) {
				wp_schedule_event( time(), 'minutely', 'launch_cron' );
			}
		} );

		add_filter( 'cron_schedules', function () use ( $container ) {
			$container[ self::RETRIEVER ]->add_interval();
		} );

		add_action( 'launch_cron', function () use ( $container ) {
			$container[ self::RETRIEVER ]->get_launches();
		} );
	}

}
