<?php

namespace App\DTO\User;

use Spatie\LaravelData\Data;

class UpdateOrCreateDTO extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $username,
        public ?string $pin,
        public ?string $email,
        public ?string $password,
        public array $accesses = [],
    ) {}
}