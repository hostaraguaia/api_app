<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    /**
     * Show the login form for users
     */
    public function showLoginForm()
    {
        return view('user.login');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form for users
     */
    public function showRegisterForm()
    {
        return view('user.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('user.dashboard');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show the user dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        return view('user.dashboard', compact('user'));
    }

    /**
     * Show the complete ranking of participants
     */
    public function showRanking()
    {
        $user = Auth::guard('web')->user();

        // Get all clients with their best quiz attempt (if any)
        $ranking = \App\Models\Client::select(
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.referral_points',
                'clients.created_at as client_created_at',
                \DB::raw('COALESCE(MAX(quiz_attempts.score), 0) as score'),
                \DB::raw('MAX(quiz_attempts.created_at) as quiz_date')
            )
            ->leftJoin('quiz_attempts', function($join) {
                $join->on('clients.id', '=', 'quiz_attempts.user_id')
                     ->where('quiz_attempts.user_type', '=', \App\Models\Client::class);
            })
            ->groupBy('clients.id', 'clients.name', 'clients.email', 'clients.referral_points', 'clients.created_at')
            ->orderByRaw('COALESCE(MAX(quiz_attempts.score), 0) + clients.referral_points DESC')
            ->orderBy('clients.created_at')
            ->get();

        return view('user.ranking', compact('user', 'ranking'));
    }

    /**
     * Export all participant emails as CSV
     */
    public function exportEmails()
    {
        $clients = \App\Models\Client::select('name', 'email', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'participantes_emails_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add header row
            fputcsv($file, ['Nome', 'Email', 'Data de Cadastro'], ';');

            // Add data rows
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->name,
                    $client->email,
                    $client->created_at->format('d/m/Y H:i:s')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
