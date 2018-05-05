<?php

namespace BinaryGary\Rocket\Twitter;

class Message {

	/**
	 * @var \Twitter
	 */
	protected $twitter;

	public function __construct( \Twitter $twitter ) {
		$this->twitter = $twitter;
	}

	public function send( $message ){
		$this->twitter->send( $message );
	}

}