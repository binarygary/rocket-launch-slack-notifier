<?php

namespace BinaryGary\Rocket\Slack;


class Post_Message {

	const ENDPOINT = 'https://slack.com/api/chat.postMessage';

	public function send( $token, $channel, $message ) {
		$result = wp_remote_post( self::ENDPOINT,
			[
				'headers' => [
					'Content-type' => 'application/x-www-form-urlencoded',
				],
				'body'    => [
					'token'   => $token,
					'channel' => $channel,
					'text'    => $message,
				],
			]
		);

		error_log( $result, 1 );
	}

}
