<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class Help extends Command {

	const COMMANDS = [
		'launch',
	];

	public function process(): array {

	}

	public function get_commands() {
		return self::COMMANDS;
	}

}