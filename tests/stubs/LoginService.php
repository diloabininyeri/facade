<?php

namespace Zeus\Facade\Tests\stubs;

class LoginService implements LoginServiceInterface
{

    public function authenticate(string $username, string $password): bool
    {
        return false;
    }

    public function getLastLoginTime(): ?string
    {
        return date('Y-m-d H:i');
    }
}