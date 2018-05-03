<?php

namespace BinaryGary\Rocket\Launch_Library;


class Active_Pad extends Active {

	const PAD_TRANSIENT = 'active_pads';

	const LIMIT       = 'limit';
	const LIMIT_COUNT = 200;

	const PAD_ENPOINT       = 'https://launchlibrary.net/1.4/pad';
	const LOCATION_ENDPOINT = 'https://launchlibrary.net/1.4/location';

	public function get_active() {
		$pad_data = get_transient( self::PAD_TRANSIENT );
		if ( $pad_data ) {
			//return $pad_data;
		}

		foreach ( $this->get_launches() as $launch ) {
			foreach ( $launch->location->pads as $pad ) {
				$this->active[] = $pad->id;
			}
		}

		$result = wp_remote_get( $this->build_url() );
		$pads   = json_decode( $result['body'] );

		$pad_data     = [];
		$location_ids = [];
		foreach ( $pads->pads as $pad ) {
			if ( 5 == $pad->locationid ) {
				sleep(0);
			}

			$location_ids[ $pad->locationid ][] = $pad->id;
		}

		$result    = wp_remote_get( add_query_arg(
			[
				self::LIMIT => self::LIMIT_COUNT,
			],
			self::LOCATION_ENDPOINT
		) );
		$locations = json_decode( $result['body'] );

		foreach ( $locations->locations as $location ) {
			if ( ! array_key_exists( $location->id, $location_ids ) ) {
				continue;
			}
			$pad_data[ 'pad.' . sanitize_title( $location->name ) ] = [
				'term'          => $location->name,
				'request'       => 'locationid',
				'request_value' => implode( ',', $location_ids[ $location->id ] ),
			];
		}

		set_transient( self::PAD_TRANSIENT, $pad_data, DAY_IN_SECONDS );

		return $pad_data;

	}

	private function build_url() {
		$url = add_query_arg( self::LIMIT, self::LIMIT_COUNT, self::PAD_ENPOINT );
		$url = add_query_arg( 'id', implode( ',', array_unique( $this->active ) ), $url );

		return $url;
	}

}