<?php

namespace Concise\Mock;

use \Concise\TestCase;

class MockConstructor1
{
    public $constructorRun = false;

    public function __construct()
    {
        $this->constructorRun = true;
    }
}

class MockConstructor2
{
    public function __construct($abc)
    {
    }
}

class Mock3
{
    public function __construct($a)
    {
    }
}

class MockBuilderConstructorTest extends TestCase
{
    public function testDisableConstructorCanBeChained()
    {
        $mock = $this->mock('\Concise\Mock\MockConstructor1')
                     ->disableConstructor()
                     ->done();
        $this->assert($mock, instance_of, '\Concise\Mock\MockConstructor1');
    }

    public function testMocksCanHaveTheirConstructorDisabledWithArguments()
    {
        $mock = $this->mock('\Concise\Mock\MockConstructor2')
                     ->disableConstructor()
                     ->done();
        $this->assert($mock, instance_of, '\Concise\Mock\MockConstructor2');
    }

    public function testMockReceivesConstructorArguments()
    {
        $mock = $this->mock('\Concise\Mock\Mock3', array('foo'))
                     ->done();
        $this->assert($mock, instance_of, '\Concise\Mock\Mock3');
    }

    public function testNiceMockReceivesConstructorArguments()
    {
        $mock = $this->niceMock('\Concise\Mock\Mock3', array('foo'))
                     ->done();
        $this->assert($mock, instance_of, '\Concise\Mock\Mock3');
    }
}
