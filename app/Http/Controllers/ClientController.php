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
            'phone' => 'required|string|max:20',
            'zip_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'parish' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ];
    }

    protected function getAdditionalFields(Request $request): array
    {
        $additionalFields = [
            'cpf' => $request->cpf,
            'phone' => $request->phone,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'state' => $request->state,
            'parish' => $request->parish,
            'address' => $request->address,
            'referral_code' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
            'referral_points' => 0,
        ];

        if ($request->has('referral_code') && !empty($request->referral_code)) {
            $referrer = Client::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $additionalFields['referred_by'] = $referrer->id;
            }
        }

        return $additionalFields;
    }
}
