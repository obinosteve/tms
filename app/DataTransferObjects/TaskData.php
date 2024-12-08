<?php

namespace App\DataTransferObjects;

use App\Models\User;

class TaskData
{
    public function __construct(
        public readonly User $user,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $status,
        public readonly string $dueDate
    ) {}

    public static function fromRequest(array $data): self
    {
        return new static(
            user: GetUserById($data['userId']),
            title: $data['title'],
            description: $data['description'],
            status: $data['status'],
            dueDate: GetDueDate($data['dueDate'])
        );
    }
}
