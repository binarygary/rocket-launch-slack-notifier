<?php

namespace BinaryGary\Rocket\Broker;

use BinaryGary\Rocket\Launch_Library\Launch;

class AnnounceAll {

	protected $services = [];

	public function __construct( ServiceInterface ...$services ) {
		$this->services = $services;
	}

	public function send_message( Launch $launch ) {
		foreach ( $this->services as $service ) {
			$service->send_message( $launch );
		}
	}

}