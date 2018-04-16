<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Post_Types\Slack_URL;
use BinaryGary\Rocket\Settings\Defaults;

class OAuth extends Base {

	const ENDPOINT = '/oauth';

	const SLACK_CONFIRM = 'https://slack.com/api/oauth.access';

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'GET',
			'callback' => [ $this, 'auth' ],
		] );
	}

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function auth() {
		$args = [
			'body' => [
				'client_id' => get_option( Defaults::SLACK_APP_ID ),
				'client_secret' => get_option( Defaults::SLACK_APP_SECRET ),
				'code' => $_GET['code'],
				'redirect_uri' => $this->get_endpoint_url(),
			],
		];

		$results = wp_remote_post( self::SLACK_CONFIRM, $args );

		$this->save_auth( json_decode( $results['body'] ) );
	}

	private function save_auth( $body ) {
		if ( $body->ok ) {
			return;
		}

		$args = [
			'post_title' => $body->incoming_webhook->url,
			'post_statue' => 'publish',
			'post_type' => Slack_URL::POST_TYPE,
		];

		$slack_url_id = wp_insert_post( $args );

		update_post_meta( $slack_url_id, 'response', $body );
	}

}
