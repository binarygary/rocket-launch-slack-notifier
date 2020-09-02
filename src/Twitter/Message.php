<?php

namespace BinaryGary\Rocket\Twitter;

class Message {

	protected $twitter;

	public function __construct( $twitter ) {
		$this->twitter = $twitter;
	}

	public function send( $message ){
		try {
			$this->twitter->send( $message );
		} catch ( \Exception $exception ) {
			error_log( 'twitter send: ' . print_r( $message, 1 ) );
			return;
		}
	}

}