<?php

namespace BinaryGary\Rocket\Launch_Library;

use BinaryGary\Rocket\Slack\Post_Message;
use BinaryGary\Rocket\Slack\Webhooks;

class Retriever {

	const ENDPOINT = 'https://launchlibrary.net/1.4/launch/next/5';

	const DAILY_UPDATE = 'launch_daily_update';

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
			'one_day' => [
				'min_range'   => 86340,
				'max_range' => 86400,
			],
			'one_hour' => [
				'min_range'   => 3540,
				'max_range' => 3600,
			],
			'five_minute' => [
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
			$this->messages->alert( $this->build_message_one_day( $launch ) );
		}



		if ( $this->timestamp - get_option( self::DAILY_UPDATE, 0 ) > DAY_IN_SECONDS) {
			$this->daily_update( $launches );
			update_option( self::DAILY_UPDATE, $this->timestamp, false );
		}

	}

	protected function process_launch( $launch ) {

		foreach ( $this->range() as $frequency => $range ) {
			if ( filter_var( $launch->netstamp - $this->timestamp, FILTER_VALIDATE_INT, [ 'options' => $range ] ) ) {
				$message = $this->build_message_{$frequency}( $launch );
				$this->messages->alert( $message );
			}
		}

	}

	private function build_message_one_day( $launch ) {
		$message = [
			'attachments' => [
				[
					'pretext' => sprintf( '%s Launch Notice', '24 Hour' ),
					'color'   => '#000000',
					'title'   => $launch->name,
					'fields'  => [
						[
							'title' => 'Net Launch',
							'value' => sprintf( '<!date^%s^{date_num} {time}|%s>', $launch->netstamp, $launch->net ),
							'short' => false,
						],
						[
							'title' => 'Vehicle',
							'value' => $launch->rocket->name,
							'short' => false,
						],
						[
							'title' => 'Launch Pad',
							'value' => $launch->location->name,
							'short' => false,
						],
					],
				],
			],
		];

		if ( isset( $launch->missions[0]->description ) ) {
			$message['attachements']['text'] = $launch->missions[0]->description;
		}

		return $message;
	}

	private function build_message_one_hour( $launch ) {

	}

	private function build_message_five_minute( $launch ) {

	}

	private function daily_update( $launches ) {
		$message = '';
		foreach ( $launches->launches as $launch ) {
			$message .= sprintf( '%s From %s on a %s%s',
				$launch->name,
				$launch->location->name,
				$launch->rocket->name,
				PHP_EOL
			);
		}

		$this->messages->alert( $message );
	}

	public function add_interval( $schedules ) {
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __('Once a minute')
		);
		return $schedules;
	}
}
