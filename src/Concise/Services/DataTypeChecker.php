<?php

namespace Concise\Services;

class DataTypeChecker
{
	public function check($accepts, $value)
	{
		if($accepts === 'int') {
			throw new \InvalidArgumentException();
		}
		return true;
	}
}
