<?php

namespace Concise\Matcher;

class UrlHasPart extends AbstractMatcher
{
    public function supportedSyntaxes()
    {
        return array(
            'url ?:string has scheme ?:string' => 'URL has scheme.',
            'url ?:string has host ?:string' => 'URL has host.',
            'url ?:string has port ?:int' => 'URL has port.',
            'url ?:string has user ?:string' => 'URL has user.',
            'url ?:string has password ?:string' => 'URL has password.',
        );
    }

    public function match($syntax, array $data = array())
    {
        $parts = [
            'port' => PHP_URL_PORT,
            'host' => PHP_URL_HOST,
            'user' => PHP_URL_USER,
            'scheme' => PHP_URL_SCHEME,
            'password' => PHP_URL_PASS,
        ];
        foreach ($parts as $kw => $part) {
            if (strpos($syntax, $kw)) {
                return parse_url($data[0], $part) == $data[1];
            }
        }
    }

    public function getTags()
    {
        return array(Tag::URLS);
    }
}
