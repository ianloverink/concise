<?php

namespace Concise\Matcher;

use \Concise\TestCase;

class TrueTest extends AbstractMatcherTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->matcher = new True();
	}

	public function testWillRespondToCorrectSyntax()
	{
		$this->assertTrue($this->matcher->matchesSyntax('true'));
	}
}
