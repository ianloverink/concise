<?php

namespace Concise\Mock;

use \Concise\TestCase;

abstract class AbstractMockBuilderTestCase extends TestCase
{
    protected function expectFailure($message)
    {
        $this->setExpectedException('\InvalidArgumentException', $message);
    }

    protected function notApplicable()
    {
        $this->assert(true);
    }

    protected function mockBuilder()
    {
        return $this->mock($this->getClassName(), array(1, 2));
    }

    protected function niceMockBuilder()
    {
        return $this->niceMock($this->getClassName(), array(1, 2));
    }

    public function testMockCanBeCreatedFromAnObjectThatExists()
    {
        $mock = $this->mockBuilder()
                     ->done();
        $this->assert($mock, instance_of, $this->getClassName());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage myMethod() does not have an associated action - consider a niceMock()?
     */
    public function testCallingMethodThatHasNoAssociatedActionWillThrowAnException()
    {
        $mock = $this->mockBuilder()
                     ->done();
        $mock->myMethod();
    }

    public function testNiceMockCanBeCreatedFromAnObjectThatExists()
    {
        $mock = $this->niceMockBuilder()
                     ->done();
        $this->assert($mock, instance_of, $this->getClassName());
    }

    public function testCallingMethodThatHasNoAssociatedActionOnANiceMockWillUseOriginal()
    {
        $mock = $this->niceMockBuilder()
                     ->done();
        $this->assert($mock->myMethod(), equals, 'abc');
    }

    abstract public function getClassName();

    // Constructor

    public function testMocksCanHaveTheirConstructorDisabled()
    {
        $mock = $this->mock($this->getClassName())
                     ->disableConstructor()
                     ->done();
        $this->assert($mock->constructorRun, is_false);
    }

    public function testMockReceivesConstructorArguments()
    {
        $mock = $this->mockBuilder()
                     ->done();
        $this->assert($mock->constructorRun, equals, 2);
    }

    public function testNiceMockReceivesConstructorArguments()
    {
        $mock = $this->niceMockBuilder()
                     ->done();
        $this->assert($mock->constructorRun, equals, 2);
    }

    // Do

    public function testACallbackCanBeSet()
    {
        $mock = $this->mockBuilder()
                     ->stub('myMethod')->andDo(function () {})
                     ->done();
        $this->assert($mock->myMethod(), equals, null);
    }

    public function testTheCallbackWillBeExecuted()
    {
        $a = 123;
        $mock = $this->mockBuilder()
                     ->stub('myMethod')->andDo(function () use (&$a) {
                         $a = 456;
                     })
                     ->done();
        $mock->myMethod();
        $this->assert($a, equals, 456);
    }

    public function testTheCallbackWillNotBeExecutedIfNotCalled()
    {
        $a = 123;
        $this->mockBuilder()
             ->stub('myMethod')->andDo(function () use (&$a) {
                 $a = 456;
             })
             ->done();
        $this->assert($a, equals, 123);
    }
}
