<?php

namespace BinaryGary\Rocket\Endpoints;


use BinaryGary\Rocket\Slack\Post_Message;

abstract class Base {

	const PATH = 'rocket-slack/v1';

	/**
	 * @var Post_Message
	 */
	protected $message;

	public function __construct( Post_Message $message ) {
		$this->message = $message;
	}

	abstract public function register();

	abstract public function endpoint();

	public function get_endpoint_url() {
		return get_rest_url( get_current_blog_id(), self::PATH . $this->endpoint() );
	}

}
