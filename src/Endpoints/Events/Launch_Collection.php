<?php

namespace BinaryGary\Rocket\Endpoints\Events;

use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;

class Launch_Collection {

	private $events = [];

	public function add( Event $event ) {
		$this->events[ strtolower( $event->get_keyword() ) ] = $event;
	}

	public function events() {
		return $this->events;
	}

	public function get_event( $event_name ) {
		if ( isset( $this->events[ $event_name ] ) ) {
			return $this->events[ $event_name ];
		}

		throw new \Exception( __( 'Event does not exist', 'tribe' ) );
	}

	public function delete_event( $event_name ) {
		unset ( $this->events[ $event_name ] );
	}

	public function process_command( $command ) {
		$command_concat = strtolower( implode( ' ', array_slice( $command, 2 ) ) );
		if ( array_key_exists( $command_concat, $this->events() ) ) {
			$event = $this->get_event( $command_concat );

			return $event->process();
		}
		$fuzz    = new Fuzz();
		$process = new Process( $fuzz );

		$event_name = $process->extractOne( $command_concat, array_keys( $this->events() ), null, [
			$fuzz,
			'ratio',
		] );
		if ( $event_name[1] > 50 ) {
			$get_event = $this->get_event( $event_name[0] );

			return $get_event->process();
		}

		// return HELP!

	}

}