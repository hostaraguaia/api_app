<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends BaseAuthController
{
    protected function getModelClass(): string
    {
        return Client::class;
    }

    protected function getTokenName(): string
    {
        return 'ClientToken';
    }

    protected function getAdditionalRegistrationRules(): array
    {
        return [
            'cpf' => 'nullable|unique:clients',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ];
    }

    protected function getAdditionalFields(Request $request): array
    {
        return [
            'cpf' => $request->cpf,
            'phone' => $request->phone,
            'address' => $request->address,
        ];
    }
}
