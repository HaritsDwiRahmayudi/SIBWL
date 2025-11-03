<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth; // <-- 1. TAMBAHKAN IMPORT INI

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Jika pengguna sudah terverifikasi, redirect seperti biasa.
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Tandai email sebagai terverifikasi.
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // <-- 2. PERUBAHAN UTAMA DIMULAI DARI SINI

        // Logout pengguna setelah verifikasi berhasil
        Auth::guard('web')->logout();

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('status', 'Verifikasi email berhasil! Silakan login untuk melanjutkan.');
    }
}