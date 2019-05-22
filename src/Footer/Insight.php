<?php

namespace BinaryGary\Rocket\Footer;


class Insight {

	const ENDPOINT      = 'https://mars.nasa.gov/rss/api/?feed=weather&category=insight&feedtype=json';
	const TRANSIENT_KEY = 'insight_lander';

	public function weather() {
		$transient = get_transient( self::TRANSIENT_KEY );
		if ( $transient ) {
			return $transient;
		}

		$result = wp_remote_get( self::ENDPOINT, [ 'timeout' => 60 ] );
		if ( is_wp_error( $result ) ) {
			return false;
		}

		$insight = json_decode( $result['body'] );

		$sol = end( $insight->sol_keys );

		$string = sprintf( 'Curious about the weather on mars? Insight experienced a low of %s C and a high of %s C yesterday', round( $insight->{$sol}->AT->mn ), round( $insight->{$sol}->AT->mx ) );

		set_transient( self::TRANSIENT_KEY, $string, HOUR_IN_SECONDS );

		return $string;
	}

}