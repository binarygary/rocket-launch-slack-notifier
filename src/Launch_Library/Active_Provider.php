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

		foreach ( $this->get_launches() as $launch ) {
			if ( isset( $launch->lsp->id ) ) {
				$this->active[] = $launch->lsp->id;
			}
		}

		$result    = wp_remote_get( $this->build_url() );
		$providers = json_decode( $result['body'] );

		$provider_data = [];
		foreach ( $providers->agencies as $agency ) {
			$provider_data[ 'provider.' . sanitize_title( $agency->name ) ] = [
				'term'          => $agency->name,
				'request'       => 'lsp',
				'request_value' => $agency->id,
			];

			$provider_data[ 'provider.' . sanitize_title( $agency->abbrev ) ] = [
				'term'          => $agency->abbrev,
				'request'       => 'lsp',
				'request_value' => $agency->id,
			];
		}

		set_transient( self::ACTIVE_PROVIDER_TRANSIENT, $provider_data, DAY_IN_SECONDS );

		return $provider_data;
	}

	private function build_url() {
		$url = add_query_arg( self::LIMIT, self::LIMIT_COUNT, self::PROVIDER_ENDPIONT );
		$url = add_query_arg( 'id', implode( ',', array_unique( $this->active ) ), $url );

		return $url;
	}

}