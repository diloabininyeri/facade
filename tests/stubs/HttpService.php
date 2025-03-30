<?php

namespace Zeus\Facade\Tests\stubs;

class HttpService
{


    public function get(string $url): string
    {
        return $url;
    }

    public function post(string $url): string
    {
        return $url;
    }
}