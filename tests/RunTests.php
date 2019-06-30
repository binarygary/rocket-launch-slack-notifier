<?php

use PHPUnit\Framework\TestCase;

final class RunTests extends TestCase {
	public function testCanRun() {
		$this->assertEquals( 1, 1 );
	}
}