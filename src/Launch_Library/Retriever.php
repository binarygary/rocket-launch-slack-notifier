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
		$this->messages  = $webhooks;
		$this->timestamp = time();
	}

	public function range() {
		return [
			'one_day'     => [
				'min_range' => 86340,
				'max_range' => 86400,
			],
			'one_hour'    => [
				'min_range' => 3540,
				'max_range' => 3600,
			],
			'five_minute' => [
				'min_range' => 240,
				'max_range' => 300,
			],
		];
	}

	public function get_launches() {
		$result   = wp_remote_get( self::ENDPOINT );
		$launches = json_decode( $result['body'] );

		foreach ( $launches->launches as $launch ) {
			$this->process_launch( $launch );
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				$this->messages->alert( $this->general_launch_info( $launch ) );
			}
		}

		// @TODO: refactor this to handle on a per
		if ( $this->timestamp - get_option( self::DAILY_UPDATE, 0 ) > DAY_IN_SECONDS ) {
			$this->daily_update( $launches );
			update_option( self::DAILY_UPDATE, $this->timestamp, false );
		}

	}

	protected function process_launch( $launch ) {

		foreach ( $this->range() as $frequency => $range ) {
			if ( filter_var( $launch->netstamp - $this->timestamp, FILTER_VALIDATE_INT, [ 'options' => $range ] ) ) {
				$method  = "build_message_{$frequency}";
				$message = $this->$method( $launch );
				$this->messages->alert( $message );
			}
		}

	}

	private function build_message_one_day( $launch ) {
		$message['attachments'][0] = [
			'pretext' => sprintf( '%s Launch Notice', '24 Hour' ),
			'color'   => '#42f4bc',
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
		];

		if ( ! ( $launch->netstamp ) ) {
			$message['attachments'][0]['fields'][0] = [
				'title' => 'Expected Launch Date',
				'value' => sprintf( '<!date^%s^{date_num}|%s>', strtotime( $launch->isonet ), $launch->net ),
				'short' => false,
			];
		}

		if ( isset( $launch->missions[0]->description ) ) {
			$message['attachments'][0]['text'] = $launch->missions[0]->description;
		}

		return $message;
	}

	private function build_message_one_hour( $launch ) {
		$message = $this->build_message_one_day( $launch );
		$message['attachments'][0]['pretext'] = sprintf( '%s Launch Notice', '1 Hour' );
		$message['attachments'][0]['color']   = '#35c496';
		return $message;
	}

	private function build_message_five_minute( $launch ) {
		$message = $this->build_message_one_day( $launch );
		$message['attachments'][0]['pretext'] = sprintf( '%s Launch Notice', '5 Minute' );
		$message['attachments'][0]['color']   = '#268e6d';
		if ( isset( $launch->vidURLs[0] ) ) {
			$message['attachments'][0]['actions'] = [
				'type' => 'button',
				'text' => 'Live Launch Feed :rocket:',
				'url'  => $launch->vidURLs[0],
			];
		}

		return $message;
	}

	private function general_launch_info( $launch ) {
		$message = $this->build_message_five_minute( $launch );
		$message['attachments'][0]['pretext'] = sprintf( '%s Launch Notice', 'General' );
		$message['attachments'][0]['color']   = '#9b9e63';

		return $message;
	}

	private function daily_update( $launches ) {
		$message = '*Next 5 Scheduled Launches*' . PHP_EOL;
		foreach ( $launches->launches as $launch ) {
			$message .= sprintf( '%s From %s at %s|%s>%s',
				$launch->name,
				$launch->location->name,
				$this->time( strtotime( $launch->isonet) , $launch->status ),
				$launch->net,
				PHP_EOL
			);

		}

		$this->messages->alert( [ 'text' => $message, 'mrkdwn' => true ] );
	}

	private function time ( $time, $status ) {
		if ( 1 != $status ) {
			return sprintf( '<!date^%s^{date_num}', $time );
		}

		return sprintf( '<!date^%s^{date_num} {time}', $time );
	}

	public function add_interval( $schedules ) {
		$schedules['minutely'] = array(
			'interval' => 60,
			'display'  => __( 'Once a minute' ),
		);

		return $schedules;
	}
}
