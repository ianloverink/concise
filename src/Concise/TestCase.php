<?php

namespace Concise;

use Concise\Mock\MockBuilder;
use Concise\Services\AssertionBuilder;
use Concise\Syntax\MatcherParser;

class TestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var array
	 */
	protected $_mocks = array();

	/**
	 * @return \Concise\Syntax\MatcherParser
	 */
	protected function getMatcherParserInstance()
	{
		return MatcherParser::getInstance();
	}

	/**
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!isset($this->$name)) {
			throw new \Exception("No such attribute '{$name}'.");
		}
		return $this->$name;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$parser = MatcherParser::getInstance();
		if(in_array($name, $parser->getKeywords())) {
			throw new \Exception("You cannot assign an attribute with the keyword '$name'.");
		}
		$this->$name = $value;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return get_object_vars($this);
	}

	/**
	 * @return string
	 */
	protected function getRealTestName()
	{
		$name = substr($this->getName(), 20);
		$pos = strpos($name, ':');
		return substr($name, 0, $pos);
	}

	/**
	 * These attributes are provided by the base PHPUnit classes.
	 * @return array
	 */
	public static function getPHPUnitProperties()
	{
		return array(
			'backupGlobals' => null,
			'backupGlobalsBlacklist' => array(),
			'backupStaticAttributes' => null,
			'backupStaticAttributesBlacklist' => array(),
			'runTestInSeparateProcess' => null,
			'preserveGlobalState' => true,
		);
	}

	/**
	 * @param  string $assertionString
	 */
	public function assert($assertionString)
	{
		if(count(func_get_args()) > 1 || is_bool($assertionString)) {
			$builder = new AssertionBuilder(func_get_args());
			$assertion = $builder->getAssertion();
		}
		else {
			$assertion = $this->getMatcherParserInstance()->compile($assertionString, $this->getData());
		}
		if($this instanceof TestCase) {
			$assertion->setTestCase($this);
		}
		else {
			$assertion->setTestCase(new TestCase());
		}
		$assertion->run();
	}

	public function tearDown()
	{
		if(substr($this->getName(), 4, 1) === '_') {
			$assertion = str_replace("_", " ", substr($this->getName(), 5));
			$this->assert($assertion);
		}
		foreach($this->_mocks as $mock) {
			foreach($mock['mockBuilder']->getRules() as $method => $methodWiths) {
				foreach($methodWiths as $withKey => $rule) {
					// Negative times means it is a stub.
					if($rule['times'] < 0) {
						continue;
					}

					if(null === $rule['with']) {
						$this->assert(count($mock['instance']->getCallsForMethod($method)), equals, $rule['times']);
					}
					else {
						foreach($mock['instance']->getCallsForMethod($method) as $call) {
							$this->assert($call, exactly_equals, $rule['with']);
						}
					}
				}
			}
		}

		global $_currentTestCase;
		$_currentTestCase = null;

		parent::tearDown();
	}
	
	/**
	 * @param  string $className
	 * @param  array  $constructorArgs
	 * @return MockBuilder
	 */
	protected function mock($className = '\stdClass', array $constructorArgs = array())
	{
		return new MockBuilder($this, $className, false, $constructorArgs);
	}

	/**
	 * @param  string $className
	 * @param  array  $constructorArgs
	 * @return MockBuilder
	 */
	protected function niceMock($className = '\stdClass', array $constructorArgs = array())
	{
		return new MockBuilder($this, $className, true, $constructorArgs);
	}

	/**
	 * @param MockBuilder $mockBuilder
	 * @param object      $mockInstance
	 */
	public function addMockInstance(MockBuilder $mockBuilder, $mockInstance)
	{
		$this->_mocks[] = array(
			'mockBuilder' => $mockBuilder,
			'instance' => $mockInstance,
		);
	}

	public function setUp()
	{
		global $_currentTestCase;
		parent::setUp();
		$_currentTestCase = $this;

		if(!defined('__KEYWORDS_LOADED')) {
			$parser = MatcherParser::getInstance();

			$all = array();
			foreach($parser->getAllMatcherDescriptions() as $syntax => $description) {
				$simpleSyntax = preg_replace('/\\?(:[a-zA-Z0-9-]+)/', '?', $syntax);
				foreach(explode('?', $simpleSyntax) as $part) {
					$p = trim($part);
					$all[str_replace(' ', '_', $p)] = $p;
				}
			}

			foreach($all as $name => $value) {
				if(!defined($name)) {
					define($name, $value);
				}
			}

			define('__KEYWORDS_LOADED', 1);
		}
	}
}

require_once(__DIR__ . '/AssertThat.php');
