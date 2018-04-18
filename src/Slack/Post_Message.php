<?php

namespace BinaryGary\Rocket\Slack;


class Post_Message {

	const ENDPOINT = 'https://slack.com/api/chat.postMessage';

	public function send( $token, $channel, $message ) {
		$result = wp_remote_post( self::ENDPOINT,
			[
				'headers' => [
					'Content-type' => 'application/json',
				],
				'body'    => [
					'token'   => $token,
					'channel' => $channel,
					'text'    => json_encode( $message ),

				],
			]
		);

		print_r( $result );
		die;
	}

}
