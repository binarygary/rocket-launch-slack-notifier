<?php

namespace BinaryGary\Rocket\Footer;

class People_In_Space {

	const ENDPOINT      = 'http://api.open-notify.org/astros.json';
	const TRANSIENT_KEY = 'people_in_space';

	public function get_count() {
		$transient = get_transient( self::TRANSIENT_KEY );
		if ( $transient ) {
			return $transient;
		}

		$result = wp_remote_get( self::ENDPOINT, [ 'timeout' => 60 ] );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		$people_in_space = json_decode( $result['body'] );

		$string = sprintf( 'There are currently %s humans in space', $people_in_space->number );

		set_transient( self::TRANSIENT_KEY, $string, HOUR_IN_SECONDS );

		return $string;
	}

}