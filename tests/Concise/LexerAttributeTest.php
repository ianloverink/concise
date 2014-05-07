<?php

namespace Concise;

class LexerAttributeTest extends LexerTestCase
{
	protected function assertion()
	{
		return 'a equals b';
	}

	protected function expectedTokens()
	{
		return array('a', 'equals', 'b');
	}

	protected function expectedSyntax()
	{
		return '? equals ?';
	}

	protected function expectedArguments()
	{
		return array(new Attribute('a'), new Attribute('b'));
	}
}