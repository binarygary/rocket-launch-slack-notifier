<?php

namespace BinaryGary\Rocket\Endpoints\Events;


use BinaryGary\Rocket\Launch_Library\Active;

class Next extends Launch {

	protected $count = 5;

	public function get_keyword() {
		return 'next';
	}

	public function process( $command ): array {
		if ( $command[3] ) {
			$this->count = (int) $command[3] < 10 ? $command[3] : 10;
		}

		$launches = [];

		foreach ( array_slice( $this->get_launches( [ 'limit' => $this->count ]), 0, $this->count ) as $launch ) {
			$launches['attachments'][]['text'] = sprintf( '%s - %s - %s',
				$launch->name,
				sprintf( '<!date^%s^{date_num}|%s>', strtotime( $launch->isonet ), $launch->net ),
				$launch->location->name
			);
		}

		return $launches;
	}

}