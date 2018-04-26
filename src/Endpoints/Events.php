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
		$body = json_decode( $data->get_body() );

		if ( 'url_verification' === $body->type ) {
			print_r( $body );
			die;
		}

		if ( 'event_callback' === $body->type ) {
			$this->message->send( $body->token, $body->event->channel, $this->demo() );
		}
	}

	private function demo() {
		return [
			'text' => 'this is a test message',
		];
	}

}