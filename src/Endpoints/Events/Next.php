<?php

namespace BinaryGary\Rocket\Endpoints\Events;


use BinaryGary\Rocket\Launch_Library\Active;

class Next extends Launch {

	protected $count = 5;

	public function get_keyword() {
		return 'next';
	}

	public function process( $command ): array {
		if ( $command[3] ) {
			$this->count = (int) $command[3];
		}

		$launches = [];

		foreach ( array_slice( $this->get_launches( [ 'limit' => $this->count ]), 0, $this->count ) as $launch ) {
			$this->launch->set( 'title', sprintf( '%s Launch Notice', $this->term ) );
			$this->launch->set( 'color', 'a71930' );
			$this->launch->set( 'launch', $launch );

			$launches = array_merge( $launches, $this->launch->message()['attachments'] );
		}

		return [
			'attachments' => $launches,
		];
	}

}