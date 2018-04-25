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
		if ( isset( $_POST['challenge'] ) ) {
			echo $_POST['challenge'];
			die;
		}
	}



}