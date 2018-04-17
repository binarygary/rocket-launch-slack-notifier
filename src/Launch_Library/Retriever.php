<?php

namespace BinaryGary\Rocket\Launch_Library;

use BinaryGary\Rocket\Slack\Post_Message;
use BinaryGary\Rocket\Slack\Webhooks;

class Retriever {

	const ENDPOINT = 'https://launchlibrary.net/1.4/launch/next/5';

	/**
	 * @var Post_Message
	 */
	protected $messages;

	protected $timestamp;

	public function __construct( Webhooks $webhooks ) {
		$this->messages = $webhooks;
		$this->timestamp = time();
	}

	public function range() {
		return [
			'24 Hour' => [
				'min_range'   => 86340,
				'max_range' => 86400,
			],
			'1 Hour' => [
				'min_range'   => 3540,
				'max_range' => 3600,
			],
			'5 Minutes' => [
				'min_range'   => 240,
				'max_range' => 300,
			],
		];
	}

	public function get_launches() {
		$result = wp_remote_get( self::ENDPOINT );
		$launches = json_decode( $result['body']);

		foreach ( $launches->launches as $launch ) {
			$this->process_launch( $launch );
		}

	}

	protected function process_launch( $launch ) {

		foreach ( $this->range() as $frequency => $range ) {
			if ( filter_var( $launch->netstamp - $this->timestamp, FILTER_VALIDATE_INT, [ 'options' => $range ] ) ) {
				$message = sprintf( '%s From %s on a %s',
					$launch->name,
					$launch->location->name,
					$launch->rocket->name
				);

				$this->messages->alert( $message );
			}
		}

	}

	public function add_interval( $schedules ) {
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __('Once a minute')
		);
		return $schedules;
	}
}
