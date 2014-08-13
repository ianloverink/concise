<?php

namespace Concise\Services;

use \Concise\TestCase;

class AssertionBuilderTest extends TestCase
{
    public function testCanFindAssertionWithArguments()
    {
        $assertion = $this->getAssertionWithArgs(array(123, 'equals', 123));
        assert_that($assertion->getMatcher(), instance_of, '\Concise\Matcher\Equals');
    }

    /**
	 * @expectedException \Exception
	 * @expectedExceptionMessage No such matcher for syntax '? array ?'.
	 */
    public function testWillThrowExceptionIfAssertionCannotBeFound()
    {
        $builder = new AssertionBuilder(array('foo', 'array', 123));
        $builder->getAssertion();
    }

    public function testAssertionBuilderWillAcceptTrue()
    {
        $assertion = $this->getAssertionWithArgs(array(true));
        $this->assert($assertion->getMatcher(), instance_of, '\Concise\Matcher\True');
    }

    public function testAssertionBuilderWillAcceptFalse()
    {
        $assertion = $this->getAssertionWithArgs(array(false));
        $this->assert($assertion->getMatcher(), instance_of, '\Concise\Matcher\False');
    }

    public function testAssertionBuilderWillAcceptTrueFollowedByOtherArguments()
    {
        $assertion = $this->getAssertionWithArgs(array(true, 'is true'));
        $this->assert($assertion->getMatcher(), not_instance_of, '\Concise\Matcher\True');
    }

    public function testAssertionBuilderWillAcceptFalseFollowedByOtherArguments()
    {
        $assertion = $this->getAssertionWithArgs(array(false, 'is false'));
        $this->assert($assertion->getMatcher(), not_instance_of, '\Concise\Matcher\False');
    }

    protected function getAssertionWithArgs(array $args)
    {
        $builder = new AssertionBuilder($args);

        return $builder->getAssertion();
    }
}
