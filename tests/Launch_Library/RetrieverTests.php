<?php

use PHPUnit\Framework\TestCase;

// Tests for refactoring the Retriever

class RetrieverTests extends TestCase {



	public function testCreate() {
		$retriever = new \BinaryGary\Rocket\Launch_Library\Retriever(


			new \BinaryGary\Rocket\Slack\Webhooks( new \BinaryGary\Rocket\Slack\Post_Message() ),
			new \BinaryGary\Rocket\Twitter\Message( new Twitter() )
		);
	}

}