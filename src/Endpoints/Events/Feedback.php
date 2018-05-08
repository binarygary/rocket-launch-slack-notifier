<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class Feedback extends Command {

	public function get_keyword() {
		return 'feedback';
	}

	public function process( $body ): array {

		$args = [
			'post_content' => $body,
			'post_status'  => 'publish',
			'post_type'    => \BinaryGary\Rocket\Post_Types\Feedback::NAME,
			'post_title'   => $body->event_id,
		];
		wp_insert_post( $args );

		return [
			'text' => 'Thanks for taking the time to share your feedback.',
		];
	}

}