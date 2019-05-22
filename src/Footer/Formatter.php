<?php

namespace BinaryGary\Rocket\Footer;

class Formatter {

	public function random() {
		$people_in_space = new People_In_Space();
		$iss_location    = new ISS_Location();
		$insight         = new Insight();

		$messages = [
			'Try @groundcontrol launch next 10',
			'Share your feedback! @groundcontrol feedback <your kind feedback>',
			$people_in_space->get_count(),
			$iss_location->location(),
			$insight->weather(),
		];

		while ( 1 ) {
			$index = array_rand( $messages );
			if ( $messages[ $index ] ) {
				return $messages[ $index ];
			}
		}
	}

}