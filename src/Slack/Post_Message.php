<?php

namespace BinaryGary\Rocket\Slack;


class Post_Message {

	const ENDPOINT = 'https://slack.com/api/chat.postMessage';

	public function send( $token, $channel, $message ) {
		wp_remote_post( self::ENDPOINT,
			[
				'headers' => [
					'Content-type' => 'application/json',
				],
				'body'    => json_encode( [
					'token'   => $token,
					'channel' => $channel,
					$message,
				] ),
			]
		);
	}

}
