<?php

namespace BinaryGary\Rocket\V2\Space_Devs;

use BinaryGary\Rocket\Launch_Library\Launch;
use BinaryGary\Rocket\Slack\Post_Message;
use BinaryGary\Rocket\Slack\Webhooks;
use BinaryGary\Rocket\Twitter\Message;

class Retriever extends Cacheable {

	const ENDPOINT = 'https://ll.thespacedevs.com/2.0.0/launch/upcoming/?format=json&limit=5&mode=detailed';

	const NEXT_5_SCHEDULED_LAUNCHES = 'next_5_scheduled_launches';
	const LAST_NOTIFICATION_SENT    = 'last_notification_sent';

	private $timestamp;
	private $messages;
	private $twitter;

	public function __construct( Webhooks $webhooks, Message $twitter ) {
		$this->messages  = $webhooks;
		$this->twitter   = $twitter;
		$this->timestamp = time();
	}

	public function range() {
		return [
			'one_day'     => [
				'min_range' => 86341,
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
		$result = $this->get( self::ENDPOINT );
		if ( is_wp_error( $result ) ) {
			return;
		}

		$launches = json_decode( $result['body'] );

		foreach ( $launches->results as $launch ) {
			$launch_object = new Launch();

			$launch_object->set( 'title', 'Launch Notice' );
			$launch_object->set( 'color', '#42f4bc' );
			$launch_object->set( 'launch_name', $launch->name );
			$launch_object->set( 'vehicle', $launch->rocket->configuration->full_name );
			$launch_object->set( 'launch_pad', $launch->pad->name );
			$launch_object->set( 'description', $launch->mission->description );
			$launch_object->set( 'netstamp', strtotime( $launch->net ) );
			$launch_object->set( 'isonet', $launch->net );
			$launch_object->set( 'net', date( 'F j, Y H:i:s e', strtotime( $launch->net ) ) );
			if ( ! empty ( $launch->vidURLs ) ) {
				$launch_object->set( 'video_url', $launch->vidURLs[0]->url );
				$launch_object->set( 'video_button', 'Launch Feed :rocket:' );
			}

			$this->process_launch( $launch );

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				$alert = new Webhooks( new Post_Message() );
				error_log( print_r( $launch_object,1) );
//				$alert->alert( $this->general_launch_info( $launch_object ) );
			}
		}

		$this->next_launches_update( $launches );
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

	private function general_launch_info( $launch ) {
		$message                              = $launch->message();
		$message['attachments'][0]['pretext'] = sprintf( '%s Launch Notice', 'General' );
		$message['attachments'][0]['color']   = '#9b9e63';

		return $message;
	}

	protected function process_launch( $launch ) {
		foreach ( $this->range() as $frequency => $range ) {
			if ( filter_var( $launch->netstamp - $this->timestamp, FILTER_VALIDATE_INT, [ 'options' => $range ] ) ) {
				$method  = "build_message_{$frequency}";
				$message = $this->$method( $launch );
				$this->messages->alert( $message );
				$this->twitter->send( $launch->message( false ) );
				update_option( self::LAST_NOTIFICATION_SENT, $message );
			}
		}
	}

	private function build_message_one_day( $launch ) {
		$launch->set( 'title', sprintf( '%s Launch Notice', '24 Hour' ) );
		$launch->set( 'color', '#42f4bc' );
		$launch->set( 'launch', $launch );

		return $launch->message();
	}

	private function build_message_one_hour( $launch ) {
		$launch->set( 'title', sprintf( '%s Launch Notice', '1 Hour' ) );
		$launch->set( 'color', '#35c496' );
		$launch->set( 'launch', $launch );

		return $launch->message();
	}

	private function build_message_five_minute( $launch ) {
		$launch->set( 'title', sprintf( '%s Launch Notice', '5 Minute' ) );
		$launch->set( 'color', '#268e6d' );
		$launch->set( 'launch', $launch );

		return $launch->message();
	}

}