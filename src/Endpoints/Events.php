<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Endpoints\Events\Launch_Collection;
use BinaryGary\Rocket\Endpoints\Events\Help;
use BinaryGary\Rocket\Post_Types\Slack_URL;
use BinaryGary\Rocket\Slack\Post_Message;
use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;

class Events extends Base {

	const ENDPOINT = 'event';

	protected $collection;
	protected $help;

	public function __construct( Post_Message $message, Launch_Collection $collection, Help $help ) {
		$this->collection = $collection;
		$this->help       = $help;
		parent::__construct( $message );
	}

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'POST',
			'callback' => [ $this, 'process' ],
		] );
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

		if ( 'event_callback' === $body->type ) {

			$command = explode( ' ', $body->event->text );

			if ( 'launch' == $command[1] ) {
				// @TODO: figure out the aliasing.
				$command_concat = strtolower( implode( ' ', array_slice( $command, 2 ) ) );
				if ( array_key_exists( $command_concat, $this->collection->events() ) ) {
					$event = $this->collection->get_event( $command_concat );
					$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $event->process() );
					die;
				} else {
					$fuzz    = new Fuzz();
					$process = new Process( $fuzz );

					$event_name = $process->extractOne( $command_concat, array_keys( $this->collection->events() ), null, [
						$fuzz,
						'ratio',
					] );
					if ( $event_name[1] > 50 ) {
						$get_event = $this->collection->get_event( $event_name[0] );
						$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $get_event->process() );
						die;
					}
				}


				$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, $this->help->process() );
				die;
			}

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

}