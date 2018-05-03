<?php

namespace BinaryGary\Rocket\Launch_Library;


class Active_Pad extends Active {

	const PAD_TRANSIENT = 'active_pads';

	const LIMIT       = 'limit';
	const LIMIT_COUNT = 200;

	const PAD_ENPOINT = 'https://launchlibrary.net/1.4/pad';

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

		$result    = wp_remote_get( $this->build_url() );
		$pads = json_decode( $result['body'] );

		$pad_data = [];
		foreach ( $pads->pads as $pad ) {
			$pad_data[ 'pad.' . sanitize_title( $pad->name ) ] = [
				'term'          => $pad->name,
				'request'       => 'lsp',
				'request_value' => $pad->id,
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