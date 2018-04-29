<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Endpoints\Events\Collection;
use BinaryGary\Rocket\Post_Types\Slack_URL;
use BinaryGary\Rocket\Slack\Post_Message;

class Events extends Base {

	const ENDPOINT = 'event';

	protected $collection;

	public function __construct( Post_Message $message, Collection $collection ) {
		$this->collection = $collection;
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
			$shortest = - 1;

			foreach ( $this->collection->events() as $key => $event ) {
				$lev = levenshtein( $body->text, $key );
				if ( $lev <= $shortest || $shortest < 0 ) {
					// set the closest match, and shortest distance
					$closest  = $event;
					$shortest = $lev;
				}

			}

			$this->message->send( $this->get_token( $body->team_id ), $body->event->channel, [
				'text' => print_r( $closest, 1 ),
			] );
			die;
		}
	}

	private function demo() {
		return [
			'text' => 'this is a test message',
		];
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