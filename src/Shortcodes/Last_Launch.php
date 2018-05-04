<?php

namespace BinaryGary\Rocket\Shortcodes;


use BinaryGary\Rocket\Launch_Library\Retriever;

class Last_Launch {

	public function generate() {
		add_shortcode( 'last_launch', [ $this, 'last_launch' ] );
	}

	public function last_launch() {
		return print_r( get_option( Retriever::LAST_NOTIFICATION_SENT ), 1 );
	}

}