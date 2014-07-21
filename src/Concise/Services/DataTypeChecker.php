<?php

namespace Concise\Services;

class DataTypeChecker
{
	protected $excludeMode = false;

	protected $context = array();

	public function setContext(array $context)
	{
		$this->context = $context;
	}

	public function check(array $acceptedTypes, $value)
	{
		if($this->excludeMode === true) {
			return $this->throwInvalidArgumentException($acceptedTypes, $value, false, "must not be");
		}

		if(count($acceptedTypes) === 0) {
			return $value;
		}
		return $this->throwInvalidArgumentException($acceptedTypes, $value, true, "not found in");
	}

	protected function matchesInAcceptedTypes(array $acceptedTypes, $value)
	{
		foreach($acceptedTypes as $acceptedType) {
			if($this->matches($acceptedType, $value)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param boolean $expecting
	 * @param string $message
	 */
	protected function throwInvalidArgumentException(array $acceptedTypes, $value, $expecting, $message)
	{
		$match = $this->matchesInAcceptedTypes($acceptedTypes, $value);
		if($expecting === $match) {
			if(is_object($value) && $value instanceof \Concise\Syntax\Token\Attribute) {
				$value = $this->getAttribute($value->getValue());
			}
			if(in_array('class', $acceptedTypes) && is_string($value) && substr($value, 0, 1) === '\\') {
				return substr($value, 1);
			}
			if(in_array('regex', $acceptedTypes)) {
				return $value;
			}
			return $value;
		}
		$accepts = implode(' or ', $acceptedTypes);
		throw new \InvalidArgumentException($this->getType($value) . " $message " . $accepts);
	}

	/**
	 * @param string $name
	 */
	protected function getAttribute($name)
	{
		if(!array_key_exists($name, $this->context)) {
			throw new \Exception("Attribute '$name' does not exist.");
		}
		return $this->context[$name];
	}

	protected function getType($value)
	{
		if(is_object($value)) {
			if(get_class($value) === 'Concise\Syntax\Token\Regexp') {
				return 'regex';
			}
			if(get_class($value) === 'Concise\Syntax\Token\Attribute') {
				return $this->getType($this->getAttribute($value->getValue()));
			}
		}
		if(is_callable($value)) {
			return 'callable';
		}
		return gettype($value);
	}

	protected function singleMatch($type, $value)
	{
		return $type === $this->simpleType($this->getType($value));
	}

	protected function matches($type, $value)
	{
		if($type === 'number') {
			return $this->singleMatch('int', $value) || $this->singleMatch('float', $value) || is_numeric($value);
		}
		return $this->singleMatch($this->simpleType($type), $value);
	}

	protected function simpleType($type)
	{
		$aliases = array(
			'integer' => 'int',
			'double'  => 'float',
			'class'   => 'string',
			'bool'    => 'boolean',
			'regex'   => 'string',
		);
		if(array_key_exists($type, $aliases)) {
			return $aliases[$type];
		}
		return $type;
	}

	public function setExcludeMode()
	{
		$this->excludeMode = true;
	}
}