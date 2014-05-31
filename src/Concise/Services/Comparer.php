<?php

namespace Concise\Services;

class Comparer
{
	protected $convertToString;

	public function setConvertToString(\Concise\Services\ConvertToString $convertToString)
	{
		$this->convertToString = $convertToString;
	}

	public function compare($a, $b)
	{
		$a = $this->convertToString->convertToString($a);
		$b = $this->convertToString->convertToString($b);
		return $a === $b;
	}
}
