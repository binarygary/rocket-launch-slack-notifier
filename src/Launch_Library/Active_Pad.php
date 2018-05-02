<?php

namespace BinaryGary\Rocket\Launch_Library;


class Active_Pad extends Active {

	const PAD_TRANSIENT = 'active_pads';

	const LIMIT       = 'limit';
	const LIMIT_COUNT = 200;

	const PAD_ENPOINT = 'https://launchlibrary.net/1.4/pad';

	public function get_active() {
		$provider_data = get_transient( self::PAD_TRANSIENT );
		if ( $provider_data ) {
			return $provider_data;
		}

		foreach ( $this->launches as $launch ) {
			foreach ( $launch->location->pads as $pad ) {
				$this->active[] = $pad->id;
			}
		}


	}

	private function build_url() {
		$url = add_query_arg( self::LIMIT, self::LIMIT_COUNT, self::PAD_ENPOINT );
		foreach ( $this->active as $active ) {
			$url = add_query_arg( 'id', $active, $url );
		}

		return $url;
	}

}