<?php

namespace BinaryGary\Rocket\Launch_Library;

use BinaryGary\Rocket\Slack\Post_Message;
use BinaryGary\Rocket\Slack\Webhooks;
use BinaryGary\Rocket\Twitter\Message;

class Retriever {

	const ENDPOINT = 'https://launchlibrary.net/1.4/launch/next/5';

	const DAILY_UPDATE = 'launch_daily_update';

	const LAST_NOTIFICATION_SENT = 'last_notification_sent';
	const NEXT_5_SCHEDULED_LAUNCHES = 'next_5_scheduled_launches';

	/**
	 * @var Post_Message
	 */
	protected $messages;

	/**
	 * @var Launch
	 */
	protected $launch;

	/**
	 * @var Message
	 */
	protected $twitter;

	protected $timestamp;

	public function __construct( Webhooks $webhooks, Message $twitter ) {
		$this->messages  = $webhooks;
		$this->twitter   = $twitter;
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
			$this->launch = new Launch();

			$this->process_launch( $launch );
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				$this->launch->set( 'title', sprintf( '%s Launch Notice', '24 Hour' ) );
				$this->launch->set( 'color', '#42f4bc' );
				$this->launch->set( 'launch', $launch );
				$this->twitter->send( $this->launch->message( false ) );
				die;
				$this->messages->alert( $this->general_launch_info( $launch ) );
			}
		}

		$this->next_launches_update( $launches );

	}

	protected function process_launch( $launch ) {

		foreach ( $this->range() as $frequency => $range ) {
			if ( filter_var( $launch->netstamp - $this->timestamp, FILTER_VALIDATE_INT, [ 'options' => $range ] ) ) {
				$method  = "build_message_{$frequency}";
				$message = $this->$method( $launch );
				$this->messages->alert( $message );
				$this->twitter->send( $this->launch->message( false ) );
				update_option( self::LAST_NOTIFICATION_SENT, $message );
			}
		}

	}

	private function build_message_one_day( $launch ) {
		$this->launch->set( 'title', sprintf( '%s Launch Notice', '24 Hour' ) );
		$this->launch->set( 'color', '#42f4bc' );
		$this->launch->set( 'launch', $launch );

		return $this->launch->message();
	}

	private function build_message_one_hour( $launch ) {
		$this->launch->set( 'title', sprintf( '%s Launch Notice', '1 Hour' ) );
		$this->launch->set( 'color', '#35c496' );
		$this->launch->set( 'launch', $launch );

		return $this->launch->message();
	}

	private function build_message_five_minute( $launch ) {
		$this->launch->set( 'title', sprintf( '%s Launch Notice', '5 Minute' ) );
		$this->launch->set( 'color', '#268e6d' );
		$this->launch->set( 'video_button', 'Live Launch Feed :rocket:' );
		$this->launch->set( 'launch', $launch );

		return $this->launch->message();
	}

	private function general_launch_info( $launch ) {
		$message = $this->build_message_five_minute( $launch );
		$message['attachments'][0]['pretext'] = sprintf( '%s Launch Notice', 'General' );
		$message['attachments'][0]['color']   = '#9b9e63';

		update_option( self::LAST_NOTIFICATION_SENT, $message );

		return $message;
	}

	private function next_launches_update( $launches ) {
		$message = '<ul>';
		foreach ( $launches->launches as $launch ) {
			$message .= sprintf( '<li>%s From %s at %s</li>',
				$launch->name,
				$launch->location->name,
				$launch->net
			);
		}
		$message .= '</ul>';

		update_option( self::NEXT_5_SCHEDULED_LAUNCHES, $message );

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
