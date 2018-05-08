<?php

namespace BinaryGary\Rocket\Endpoints\Events;


use BinaryGary\Rocket\Launch_Library\Launch as Launch_Message;

class Launch extends Event {

	protected $term;
	protected $request;
	protected $request_value;

	public function __construct( Launch_Message $launch, $args ) {
		$this->term          = $args['term'];
		$this->request       = $args['request'];
		$this->request_value = $args['request_value'];
		parent::__construct( $launch );
	}

	public function get_keyword() {
		return $this->term;
	}

	public function query_name() {
		return $this->request;
	}

	public function query_value() {
		return $this->request_value;
	}

	public function process( $command ): array {
		foreach ( $this->get_launches() as $launch ) {
			$this->launch->set( 'title', sprintf( '%s Launch Notice', $this->term ) );
			$this->launch->set( 'color', 'a71930' );
			$this->launch->set( 'launch', $launch );

			return $this->launch->message();
		}

		return [
			'text' => sprintf( 'Sorry, could not find any %s launches', $this->term ),
		];
	}

}