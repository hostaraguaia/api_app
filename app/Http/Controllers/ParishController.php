<?php

namespace App\Http\Controllers;

use App\Models\Parish;
use Illuminate\Http\Request;

class ParishController extends BaseAuthController
{
    protected function getModelClass(): string
    {
        return Parish::class;
    }

    protected function getTokenName(): string
    {
        return 'ParishToken';
    }

    protected function getAdditionalRegistrationRules(): array
    {
        return [
            'cnpj' => 'nullable|unique:parishs',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish_priest' => 'nullable|string|max:255',
        ];
    }

    protected function getAdditionalFields(Request $request): array
    {
        return [
            'cnpj' => $request->cnpj,
            'phone' => $request->phone,
            'address' => $request->address,
            'diocese' => $request->diocese,
            'parish_priest' => $request->parish_priest,
        ];
    }
}
