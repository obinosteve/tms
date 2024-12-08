<?php

use App\Models\User;
use Carbon\Carbon;

function GetDueDate(?string $dueDate): ?string
{
    if (!$dueDate) {
        return null;
    }

    $dueDate = Carbon::createFromFormat('m/d/Y', $dueDate)->format('Y-m-d');

    return $dueDate;
}

function GetUserById(?int $userId): User
{
    if (!$userId) {
        return auth()->user();
    }

    return User::find($userId);
}
