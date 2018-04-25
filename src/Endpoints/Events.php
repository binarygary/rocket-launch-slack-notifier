<?php

namespace BinaryGary\Rocket\Endpoints;

class Events extends Base {

	const ENDPOINT = 'event';

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'POST',
			'callback' => [ $this, 'process' ],
		] );
	}

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function process() {
		$body = json_decode( $_POST['body'] );

		if ( isset( $body->challenge ) ) {
			echo $body->challenge;
			die;
		}
	}



}