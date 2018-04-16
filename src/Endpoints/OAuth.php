<?php

namespace BinaryGary\Rocket\Endpoints;

class OAuth extends Base {

	const ENDPOINT = '/oauth';

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'GET',
			'callback' => [ $this, 'auth' ],
		] );
	}

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function auth() {
		print_r( $_GET );
		print_r( $_POST );
	}

}
