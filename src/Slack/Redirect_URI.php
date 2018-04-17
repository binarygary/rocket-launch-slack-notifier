<?php

namespace BinaryGary\Rocket\Slack;

use BinaryGary\Rocket\Endpoints\OAuth;
use BinaryGary\Rocket\Settings\Defaults;

class Redirect_URI {

	public function success() {
		wp_redirect( get_permalink( get_option( Defaults::SUCCESS_PAGE ) ) );
	}

	public function failure() {
		wp_redirect( get_permalink( get_option( Defaults::FAILURE_PAGE ) ) );
	}

}
