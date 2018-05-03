<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class Help extends Command {

	const COMMANDS = [
		'launch',
	];

	public function process(): array {
		return [
			'text' => __( 'Sorry, we could not understand that search term', 'tribe' ),
		];
	}

	public function get_commands() {
		return self::COMMANDS;
	}

}