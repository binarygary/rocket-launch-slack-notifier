<?php

namespace BinaryGary\Rocket\Shortcodes;


class Last_Launch {

	public function generate() {
		add_shortcode( 'last_launch', [ $this, 'last_launch' ] );
	}

	public function last_launch() {

	}

}