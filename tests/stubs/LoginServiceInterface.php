<?php

namespace Zeus\Facade\Tests\stubs;

interface LoginServiceInterface
{
    public function authenticate(string $username, string $password): bool;
    public function getLastLoginTime(): ?string;
}