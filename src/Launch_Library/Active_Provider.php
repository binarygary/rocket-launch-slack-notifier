<?php

namespace BinaryGary\Rocket\Launch_Library;


class Active_Provider extends Active {

	const PROVIDER_ENDPIONT = 'https://launchlibrary.net/1.4/lsp';
	const LIMIT             = 'limit';
	const LIMIT_COUNT       = 100;

	const ACTIVE_PROVIDER_TRANSIENT = 'active_providers';

	public function get_active() {
		$provider_data = get_transient( self::ACTIVE_PROVIDER_TRANSIENT );
		if ( $provider_data ) {
			return $provider_data;
		}

		foreach ( $this->launches as $launch ) {
			$this->active[] = $launch->lsp->id;
		}

		$result    = wp_remote_get( $this->build_url() );
		$providers = json_decode( $result['body'] );

		$provider_data = [];
		foreach ( $providers->agencies as $agency ) {
			$provider_data[ 'provider' . sanitize_title( $agency->name ) ] = [
				'term'          => $agency->name,
				'request'       => 'lsp',
				'request_value' => $agency->id,
			];
		}

		set_transient( self::ACTIVE_PROVIDER_TRANSIENT, $provider_data, DAY_IN_SECONDS );

		return $provider_data;
	}

	private function build_url() {
		$url = add_query_arg( self::PROVIDER_ENDPIONT, [ self::LIMIT, self::LIMIT_COUNT ] );
		foreach ( $this->active as $active ) {
			$url = add_query_arg( $url, [ 'id', $active ] );
		}

		error_log( $url );
		return $url;
	}

}