<?php

namespace BinaryGary\Rocket\Slack;


use BinaryGary\Rocket\Post_Types\Message_Log;

class Post_Message {

	const ENDPOINT = 'https://slack.com/api/chat.postMessage';

	public function send( $token, $channel, $message ) {

		if ( 'debug' === $channel ) {
			print_r( $message );
			return;
		}

		$message['channel'] = $channel;

		$result = wp_remote_post( self::ENDPOINT,
			[
				'headers'  => [
					'Content-Type' => 'application/json',
					'Authorization' => "Bearer $token",
				],
				'body'     => json_encode( $message ),
				'blocking' => false,
			]
		);

		// Log the request results.
		$args = [
			'post_content' => print_r( $result, 1) . print_r( $message, 1 ),
			'post_status'  => 'publish',
			'post_type'    => Message_Log::POST_TYPE,
			'post_title'   => $channel,
		];

		return wp_insert_post( $args );
	}

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
