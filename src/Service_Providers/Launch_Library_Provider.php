<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Launch_Library\Launch;
use BinaryGary\Rocket\Launch_Library\Retriever;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Launch_Library_Provider implements ServiceProviderInterface {

	const RETRIEVER = 'launch_library.retriever';
	const LAUNCH    = 'launch_library.launch';

	public function register( Container $container ) {
		$container[ self::RETRIEVER ] = function () use ( $container ) {
			return new Retriever( $container[ Slack_Provider::WEBHOOKS ] );
		};

		$container[ self::LAUNCH ] = function() {
			return new Launch();
		};

		add_action( 'init', function() {
			$next_scheduled = wp_next_scheduled( 'launch_cron' );
			if ( $next_scheduled ) {
				wp_unschedule_event( $next_scheduled, 'launch_cron' );
			}
		} );

		add_filter( 'cron_schedules', function ( $schedules ) use ( $container ) {
			return $container[ self::RETRIEVER ]->add_interval( $schedules );
		} );

//		add_action( 'launch_cron', function () use ( $container ) {
//			$container[ self::RETRIEVER ]->get_launches();
//		} );
	}

}
