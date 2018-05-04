<?php

namespace BinaryGary\Rocket\Shortcodes;


use BinaryGary\Rocket\Launch_Library\Retriever;

class Next_Five {

	public function generate() {
		add_shortcode( 'next_five_launches', [ $this, 'next_five_launches' ] );
	}

	public function next_five_launches() {
		return get_option( Retriever::NEXT_5_SCHEDULED_LAUNCHES );
	}

}