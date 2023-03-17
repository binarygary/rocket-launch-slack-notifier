<?php

namespace BinaryGary\Rocket\Endpoints;


class Count extends Base {

	const ENDPOINT = 'count';

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'GET',
			'callback' => [ $this, 'process' ],
			'permission_callback' => '__return_true'
		] );
	}

	public function process() {
		global $wpdb;

		return (int)$wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='slack_team' AND post_status='publish'");
	}

}
