<?php

namespace BinaryGary\Rocket\Endpoints\Events;

class Collection {

	private $events = [];

	public function add( Event $event ) {
		$this->events[ $event->get_keyword() ] = $event;
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

}