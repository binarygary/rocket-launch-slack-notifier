<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class SpaceX extends Event {

	const KEYWORD = 'spacex';
	const LABEL   = 'SpaceX';
	const REQUEST = 'lsp';
	const LSP     = 121;

	public function get_keyword() {
		return self::KEYWORD;
	}

	public function query_name() {
		return self::REQUEST;
	}

	public function query_value() {
		return self::LSP;
	}

	public function process(): array {
		foreach ( $this->get_launches() as $launch ) {
			$this->launch->set( 'title', sprintf( '%s Launch Notice', self::LABEL ) );
			$this->launch->set( 'color', 'a71930' );
			$this->launch->set( 'launch', $launch );

			return $this->launch->message();
		}
	}

}