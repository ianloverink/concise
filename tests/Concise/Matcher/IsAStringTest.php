<?php

namespace Concise\Matcher;

use \Concise\TestCase;

class IsAStringTest extends AbstractMatcherTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->matcher = new IsAString();
	}

	public function testIsAString()
	{
		$this->assert('"123" is a string');
	}

	public function testIsAStringFailure()
	{
		$this->assertFailure('123 is a string');
	}
}
