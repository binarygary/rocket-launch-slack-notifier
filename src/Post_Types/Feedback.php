<?php

namespace BinaryGary\Rocket\Post_Types;


class Feedback extends Post_Type {

	const NAME = 'slack_feedback';

	public function post_type() {
		return self::NAME;
	}

	public function args() {
		return [
			'public' => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'labels'       => [
				'menu_name' => __( 'Slack Feedback', 'tribe' ),
			],
		];
	}
}