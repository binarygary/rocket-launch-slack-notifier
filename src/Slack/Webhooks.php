<?php

namespace BinaryGary\Rocket\Slack;


use BinaryGary\Rocket\Post_Types\Slack_URL;

class Webhooks {

	/**
	 * @var Post_Message
	 */
	protected $post_message;

	public function __construct( Post_Message $post_message ) {
		$this->post_message = $post_message;
	}

	public function alert( $message ) {
		$hooks = new \WP_Query( [
			'post_type'      => Slack_URL::POST_TYPE,
			'posts_per_page' => - 1,
			'post_statue'    => 'publish',
		] );

		foreach ( $hooks->posts as $post ) {
			$this->post_message->incoming_webhook( $post->post_content, $message );
		}

		if ( empty ($hooks->post ) ) {
			error_log( 'slack webhook: ' . print_r( $message, 1 ) );
		}

	}

}
