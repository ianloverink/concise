<?php
 
namespace Concise;
 
/**
 * When using concise with non-PHPUnit it has to still remain compatible
 */
class MyTinyTestSuite
{
    protected $testCase;
 
    public function __construct()
    {
        $this->testCase = new TestCase();
    }
 
    public function checkSomething()
    {
        $this->testCase->assert(3 + 5, equals, 8);
    }
 
    public function checkSomethingElse()
    {
        $this->testCase->assert(3 + 5, equals, 7);
    }
}
 
class TestCaseExternalTest extends TestCase
{
    public function testAnExternalRunner()
    {
        $suite = new MyTinyTestSuite();
        $suite->checkSomething();
    }
 
    public function testAnExternalRunnerWillThrowAnExceptionOnFailure()
    {
        $this->assert(function () {
            $suite = new MyTinyTestSuite();
            $suite->checkSomethingElse();
        }, throws, "PHPUnit_Framework_AssertionFailedError");
    }
}