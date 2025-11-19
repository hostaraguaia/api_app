<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends BaseAuthController
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    protected function getTokenName(): string
    {
        return 'UserToken';
    }
}
