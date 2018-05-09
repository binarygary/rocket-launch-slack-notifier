<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class About extends Command {

	public function get_keyword() {
		return 'about';
	}

	public function process( $body ): array {
		return [
			'text' => 'Ground Control is a Slack App by the fine folks at https://binaryjazz.us and generally uses the https://launchlibrary.net/ public API.',
		];
	}

}