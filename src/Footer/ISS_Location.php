<?php

namespace BinaryGary\Rocket\Footer;

class ISS_Location {

	const ENDPOINT      = 'http://api.open-notify.org/iss-now.json';
	const TRANSIENT_KEY = 'iss_location';

	public function location() {
		$transient = get_transient( self::TRANSIENT_KEY );
		if ( $transient ) {
			return $transient;
		}

		$result = wp_remote_get( self::ENDPOINT, [ 'timeout' => 60 ] );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		$iss = json_decode( $result['body'] );

		$string = sprintf( 'Looking for the ISS? It\'s at %s, %s', $iss->iss_position->latitude, $iss->iss_position->longitude );

		set_transient( self::TRANSIENT_KEY, $string, MINUTE_IN_SECONDS );

		return $string;
	}

}