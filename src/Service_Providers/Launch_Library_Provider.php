<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Launch_Library\Retriever;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Launch_Library_Provider implements ServiceProviderInterface {

	const RETRIEVER = 'launch_library.retriever';

	public function register( Container $container ) {
		$container[ self::RETRIEVER ] = function() use ( $container ){
			return new Retriever( $container[ Slack_Provider::POST_MESSAGE ] );
		};

		add_action( 'init', function() use ($container) {
			$container[ self::RETRIEVER ]->get_launches();
		});
	}

}
