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

			return $event->process( $command );
		}
		$fuzz    = new Fuzz();
		$process = new Process( $fuzz );

		$event_name = $process->extractOne( $command_concat, array_keys( $this->events() ), null, [
			$fuzz,
			'ratio',
		] );
		if ( $event_name[1] > 35 ) {
			$get_event = $this->get_event( $event_name[0] );

			return $get_event->process( $command );
		}

		return [
			'text' => 'I did not understand what launch provider or launch pad you are looking for. Here is a list of words you can use after launch: ' . $this->get_keywords(),
		];

	}

	private function get_keywords() {
		$words = [];
		foreach ( $this->events() as $event ) {
			$words[] = $event->get_keyword();
		}

		asort( $words );
		return '`' . implode( '` , `', $words ) . '`';
	}

}