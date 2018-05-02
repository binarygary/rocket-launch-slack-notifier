<?php

namespace BinaryGary\Rocket\Endpoints\Events;


abstract class Command {

	abstract public function process(): array;

}