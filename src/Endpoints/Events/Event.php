<?php

namespace BinaryGary\Rocket\Endpoints\Events;

use BinaryGary\Rocket\Launch_Library\Launch;
use function Sodium\add;

abstract class Event {

	const ENDPOINT = 'https://launchlibrary.net/1.4/launch';

	/**
	 * @var Launch
	 */
	protected $launch;

	public function __construct( Launch $launch ) {
		$this->launch = $launch;
	}

	abstract public function get_keyword();

	abstract public function process(): array;

	abstract public function query_name();

	abstract public function query_value();

	public function setup_endpoint( $params ) {
		$params = wp_parse_args( $params, [
			'startdate'         => date( 'Y-m-d' ),
			'mode'              => 'verbose',
			'limit'             => 1,
			$this->query_name() => $this->query_value(),
		] );

		return add_query_arg( $params, self::ENDPOINT );
	}

	public function get_launches( $params = [] ): array {
		$result = wp_remote_get( $this->setup_endpoint( $params ) );

		return json_decode( $result['body'] );
	}

}