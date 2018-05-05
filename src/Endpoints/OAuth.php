<?php

namespace BinaryGary\Rocket\Endpoints;

use BinaryGary\Rocket\Post_Types\Slack_Team;
use BinaryGary\Rocket\Post_Types\Slack_URL;
use BinaryGary\Rocket\Settings\Defaults;
use BinaryGary\Rocket\Slack\Redirect_URI;

class OAuth extends Base {

	const ENDPOINT = '/oauth';

	const SLACK_CONFIRM = 'https://slack.com/api/oauth.access';

	/**
	 * @var Redirect_URI
	 */
	protected $redirect_uri;

	public function __construct( $message, Redirect_URI $redirect_URI ) {
		$this->redirect_uri = $redirect_URI;
		parent::__construct( $message );
	}

	public function register() {
		register_rest_route( self::PATH, self::ENDPOINT, [
			'methods'  => 'GET',
			'callback' => [ $this, 'auth' ],
		] );
	}

	public function endpoint() {
		return self::ENDPOINT;
	}

	public function auth() {
		$args = [
			'body' => [
				'client_id'     => get_option( Defaults::SLACK_APP_ID ),
				'client_secret' => get_option( Defaults::SLACK_APP_SECRET ),
				'code'          => $_GET['code'],
				'redirect_uri'  => $this->get_endpoint_url(),
			],
		];

		$results = wp_remote_post( self::SLACK_CONFIRM, $args );

		$this->save_auth( json_decode( $results['body'] ) );
	}

	private function save_auth( $body ) {

		if ( ! $body->ok ) {
			$this->redirect_uri->failure();
			die;
		}

		$team_id = $this->create_slack_team( $body );

		$user_id = $this->user( $body );

		$slack_url_id = $this->create_slack_url( $body, $user_id, $team_id );

		// @TODO: refactor this out...update Post_Message
		update_post_meta( $slack_url_id, 'response', $body );

		$this->redirect_uri->success();
		die;
	}

	private function post_exists( $title ) {
		$post = get_page_by_title( $title, 'OBJECT', Slack_URL::POST_TYPE );
		if ( isset( $post->ID ) ) {
			return $post->ID;
		}

		return 0;
	}

	private function create_slack_team( $body ) {
		$args = [
			'post_content' => $body->access_token,
			'post_status'  => 'publish',
			'post_type'    => Slack_Team::POST_TYPE,
			'post_title'   => $body->team_id,
			'ID'           => $this->post_exists( $body->team_id ),
		];

		return wp_insert_post( $args );
	}

	private function user( $body ) {
		if ( username_exists( $body->user_id ) ) {
			return username_exists( $body->user_id );
		}

		return wp_insert_user( [
			'user_login' => $body->user_id,
			'user_pass'  => null,
		] );

	}

	/**
	 * @param $body
	 * @param $user_id
	 * @param $team_id
	 *
	 * @return int|\WP_Error
	 */
	private function create_slack_url( $body, $user_id, $team_id ) {
		$args = [
			'post_content' => $body->incoming_webhook->url,
			'post_author'  => $user_id,
			'post_status'  => 'publish',
			'post_type'    => Slack_URL::POST_TYPE,
			'post_parent'  => $team_id,
			'post_title'   => $body->incoming_webhook->channel_id,
			'ID'           => $this->post_exists( $body->incoming_webhook->channel_id ),
		];

		return wp_insert_post( $args );
	}

}
