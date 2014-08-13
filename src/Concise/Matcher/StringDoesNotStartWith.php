<?php

namespace Concise\Matcher;

class StringDoesNotStartWith extends StringStartsWith
{
    public function supportedSyntaxes()
    {
        return array(
            '? does not start with ?' => 'Assert a string does not not start (begin) with another string.',
        );
    }

    public function match($syntax, array $data = array())
    {
        return !parent::match($syntax, $data);
    }
}
