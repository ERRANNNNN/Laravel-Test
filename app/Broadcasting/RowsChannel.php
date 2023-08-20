<?php

namespace App\Broadcasting;

use App\Models\User;

class RowsChannel
{
    public function __construct()
    {
        //
    }

    /**
     * Аутентификация канала
     * @param User $user
     * @return array|bool
     */
    public function join(User $user): array|bool
    {
        return auth()->check();
    }
}
