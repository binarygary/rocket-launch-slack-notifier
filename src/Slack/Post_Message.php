<?php

namespace BinaryGary\Rocket\Slack;


class Post_Message {

	const ENDPOINT = 'https://slack.com/api/chat.postMessage';

	public function send( $token, $channel, $message ) {

		$message['channel'] = $channel;

		wp_remote_post( self::ENDPOINT,
			[
				'headers' => [
					'Content-Type' => 'application/json',
					'Authorization' => "Bearer $token",
				],
				'body'    => json_encode( $message ),
			]
		);

	}

}
