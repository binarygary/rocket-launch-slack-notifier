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

	public function process( \WP_REST_Request $data ) {
		$body = $data->get_body();

		if ( 'url_verification' === $body['type'] ) {
			print_r( $body );
			die;
		}
	}



}