<?php

namespace BinaryGary\Rocket\Post_Types;


class Message_Log extends Post_Type {

	const POST_TYPE = 'message_log';

	public function post_type() {
		return self::POST_TYPE;
	}

	public function args() {
		return [
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'labels'       => [
				'menu_name' => __( 'Slack Messages', 'tribe' ),
			],
		];
	}

}