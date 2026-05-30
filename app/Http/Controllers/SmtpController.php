<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SmtpController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Read directly from config/env
        $setting = (object) [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'from_name' => config('mail.from.name'),
            'from_email' => config('mail.from.address'),
        ];

        return view('pages.settings.smtp', compact('setting'));
    }

    public function update(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'required|string',
        ]);

        try {
            $this->updateEnv([
                'MAIL_HOST' => $validated['host'],
                'MAIL_PORT' => $validated['port'],
                'MAIL_USERNAME' => $validated['username'],
                'MAIL_PASSWORD' => $validated['password'],
                'MAIL_ENCRYPTION' => $validated['encryption'],
                'MAIL_FROM_ADDRESS' => $validated['from_email'],
                'MAIL_FROM_NAME' => $validated['from_name'],
            ]);

            return redirect()->back()->with('success', 'SMTP settings updated in .env successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update .env: ' . $e->getMessage());
        }
    }

    protected function updateEnv(array $data)
    {
        $path = base_path('.env');

        if (!File::exists($path)) {
            return;
        }

        $content = File::get($path);

        foreach ($data as $key => $value) {
            // Check if key exists
            if (preg_match("/^{$key}=/m", $content)) {
                // Replace existing key
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                // Append new key
                $content .= "\n{$key}=\"{$value}\"";
            }
        }

        File::put($path, $content);
    }
}
