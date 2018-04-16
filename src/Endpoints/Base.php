<?php

namespace BinaryGary\Rocket\Endpoints;


abstract class Base {

	const PATH = 'rocket-slack/v1';

	abstract public function register();

	abstract public function endpoint();

	public function get_endpoint_url() {
		return get_rest_url( get_current_blog_id(), self::PATH . $this->endpoint() );
	}

}
