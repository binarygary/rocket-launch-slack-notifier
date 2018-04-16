<?php

namespace BinaryGary\Rocket\Slack;

use BinaryGary\Rocket\Endpoints\OAuth;

class Redirect_URI {

	/**
	 * @var OAuth
	 */
	protected $oauth;

	public function __construct( OAuth $oauth ) {
		$this->oauth = $oauth;
	}

	public function get_url() {
		return $this->oauth->get_endpoint_url();
	}

}
