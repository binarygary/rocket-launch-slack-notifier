<?php
namespace BinaryGary\Rocket\V2\Space_Devs;

abstract class Cacheable {

	public function get( $url, $cache_length = MINUTE_IN_SECONDS * 30 ) {
		$response = get_transient( md5( $url ) );
		if ( false === $response ) {
			$response = wp_remote_get( $url );

			set_transient( md5( $url ), $response, $cache_length );
		}

		return $response;
	}

}