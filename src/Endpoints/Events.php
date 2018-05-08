<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Endpoints\Events\Launch_Collection;
use BinaryGary\Rocket\Endpoints\Events\Help;
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

			if ( 'im' === $body->event->channel_type ) {
				$command = [ 'im' ] + $command;
				$bot_user_id = $this->get_bot_user_id( $body->team_id );

				if ( ! isset( $body->event->bot_id ) && $bot_user_id == $body->event->bot_id ) {
					return;
				}
				die;
			}

			$command[1] = strtolower( $command[1] );

			if ( 'launch' == $command[1] ) {
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
		$hooks = new \WP_Query( [
			'post_type'      => Slack_URL::POST_TYPE,
			'posts_per_page' => 1,
			'post_statue'    => 'publish',
			'post_title'     => $team_id,
		] );

		$body = get_post_meta( $hooks->posts[0]->ID, 'response', true );

		return $body->access_token;
	}

	private function get_bot_user_id( $team_id ) {
		$bots = new \WP_Query( [
			'post_type'      => Slack_URL::POST_TYPE,
			'posts_per_page' => 1,
			'post_statue'    => 'publish',
			'post_title'     => $team_id,
		] );

		$body = get_post_meta( $bots->posts[0]->ID, 'response', true );

		return $body->bot->bot_user_id;
	}

}