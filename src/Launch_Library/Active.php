<?php

namespace BinaryGary\Rocket\Launch_Library;


abstract class Active {

	const ENDPOINT                = 'https://launchlibrary.net/1.4/launch/next/250';
	const NEXT_LAUNCHES_TRANSIENT = 'rocket_next_250';

	protected $launches;
	protected $active = [];

	public function get_launches() {
		$this->launches = get_transient( self::NEXT_LAUNCHES_TRANSIENT );
		if ( $this->launches ) {
			return;
		}

		$result = wp_remote_get( self::ENDPOINT );
		$launch_results = json_decode( $result['body'] );
		$this->launches = $launch_results->launches;

		set_transient( self::NEXT_LAUNCHES_TRANSIENT, $this->launches, DAY_IN_SECONDS );
	}

	abstract public function get_active();

}