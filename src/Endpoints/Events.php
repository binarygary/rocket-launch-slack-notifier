<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Endpoints\Events\Launch_Collection;
use BinaryGary\Rocket\Endpoints\Events\Help;
use BinaryGary\Rocket\Post_Types\Slack_Team;
use BinaryGary\Rocket\Post_Types\Slack_URL;
use BinaryGary\Rocket\Slack\Post_Message;

class Events extends Base {

	const ENDPOINT = 'event';

	protected $launch_collection;
	protected $help;
	protected $keywords;

	public function __construct( Post_Message $message, Launch_Collection $collection, Help $help ) {
		$this->launch_collection = $collection;
		$this->help              = $help;
		parent::__construct( $message );
	}

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'POST',
			'callback' => [ $this, 'process' ],
		] );
	}

	public function add_keyword( $keyword, $object ) {
		$this->keywords[ $keyword ] = $object;
	}

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function process( \WP_REST_Request $data ) {
		$body = json_decode( $data->get_body() );

		if ( 'url_verification' === $body->type ) {
			print_r( $body );
			die;
		}

		$args = [
			'post_content' => print_r( $body, 1 ),
			'post_status'  => 'publish',
			'post_type'    => \BinaryGary\Rocket\Post_Types\Events::NAME,
			'post_title'   => $body->event_id,
		];
		wp_insert_post( $args );

		if ( 'event_callback' === $body->type ) {

			$command = explode( ' ', $body->event->text );

			if ( isset( $body->event->channel_type ) && 'im' === $body->event->channel_type ) {
				$command = array_merge( [ 'im' ], $command );

				if ( isset( $body->event->bot_id ) ) {
					return;
				}
			}

			$command[1] = strtolower( $command[1] );

			if ( 'launch' == $command[1] ) {
				echo json_encode( $this->launch_collection->process_command( $command ) );

				$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $this->launch_collection->process_command( $command ) );
				die;
			}

			if ( array_key_exists( $command[1], $this->keywords ) ) {
				$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $this->keywords[ $command[1] ]->process( $body ) );
				die;
			}

			$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $this->help->process( $body ) );
			die;
		}
	}

	private function get_token( $team_id ) {

		global $wpdb;

		//SELECT post_content FROM `wp_e3c0865639_posts` WHERE post_title = 'T8C1R1ELB' AND  post_status = 'publish' AND post_type = 'slack_team' ORDER BY ID DESC LIMIT 0,1

		$token = $wpdb->get_var( $wpdb->prepare(
			"SELECT post_content FROM {$wpdb->posts} WHERE post_title='%s' AND post_status='publish' AND post_type='%s' ORDER BY ID DESC LIMIT 0,1 ",
			$team_id,
			Slack_Team::POST_TYPE
		) );

		error_log( $wpdb->last_query );




		error_log( 'team_id: ' . $team_id );
		error_log( 'token: ' . $token );

		return $token;

	}

}