<?php

namespace Zeus\Facade\Tests\stubs;

readonly class LoginController
{
    public function __construct(private LoginServiceInterface $loginService)
    {
    }

    public function authenticateUser(string $username, string $password): bool
    {
        return $this->loginService->authenticate($username, $password);
    }

    public function getLastLoginTime(): ?string
    {
        return $this->loginService->getLastLoginTime();
    }
}