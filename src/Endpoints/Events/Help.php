<?php

namespace BinaryGary\Rocket\Endpoints\Events;


class Help extends Command {

	public function get_keyword() {
		return 'help';
	}

	public function process( $body ): array {
		return [
			'text' => __( 'Hi, I\'m Ground Control!

Usually I\'ll sit here quietly waiting for you to ask me about upcoming launches...more on that later.
When you installed me, you authorized me to post to a channel.
In that channel I\'ll post a *24 hour* launch notice, a *1 hour* launch notice and a *5 minute* launch notice for every orbital launch I know about.

Launch notices will include the type of vehicle, launch location, some mission information, and the launch time.

_Commands:_
I can understand a few commands, but you\'ll need to @ me to get my attention.

`launch [who/where]` Try `@ground_control launch SpaceX` or `@ground_control launch cape canaveral` and I\'ll reply with the next launch I know of by that provider.

`about` 

`feedback`

`help` This message! You found it!!! Huzzah :rocket:', 'tribe' ),
		];
	}

}