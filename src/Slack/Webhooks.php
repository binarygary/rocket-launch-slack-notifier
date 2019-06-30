<?php

namespace BinaryGary\Rocket\Slack;


use BinaryGary\Rocket\Post_Types\Message_Log;
use BinaryGary\Rocket\Post_Types\Slack_URL;

class Webhooks {

	public function alert( $message ) {
		$hooks = new \WP_Query( [
			'post_type'      => Slack_URL::POST_TYPE,
			'posts_per_page' => - 1,
			'post_statue'    => 'publish',
		] );

		foreach ( $hooks->posts as $post ) {
			$this->incoming_webhook( $post->post_content, $message );
		}

	}

	// @TODO: rename this...it's terrible.
	public function incoming_webhook( $url, $message ) {
		$result = wp_remote_post( $url,
			[
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => json_encode( $message ),
			]
		);

		$args = [
			'post_content' => print_r( $result, 1) . print_r( $message, 1 ),
			'post_status'  => 'publish',
			'post_type'    => Message_Log::POST_TYPE,
			'post_title'   => $url,
		];

		return wp_insert_post( $args );
	}

}
