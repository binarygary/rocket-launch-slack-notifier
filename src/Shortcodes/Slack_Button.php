<?php

namespace BinaryGary\Rocket\Shortcodes;

use BinaryGary\Rocket\Settings\Defaults;
use BinaryGary\Rocket\Slack\Redirect_URI;

class Slack_Button {

	const ENDPOINT = 'https://slack.com/oauth/authorize?scope=incoming-webhook';

	protected $redirect_URI;

	public function __construct( Redirect_URI $redirect_URI ) {
		$this->redirect_URI = $redirect_URI;
	}

	public function generate() {
		add_shortcode( 'slack_button', [ $this, 'build_button' ] );
	}

	public function build_button() {
		return sprintf( '<a href="%s"><img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" /></a>',
			$this->build_url()
		);
	}

	private function build_url() {
		$args = [
			'scope'        => 'incoming-webhook',
			'client_id'    => get_option( Defaults::SLACK_APP_ID ),
			'redirect_uri' => $this->redirect_URI->get_url(),
		];

		return add_query_arg( $args, self::ENDPOINT );
	}

}
