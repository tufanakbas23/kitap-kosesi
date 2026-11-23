<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'E-posta adresi veya şifre hatalı.'])->onlyInput('email');
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Hesabınız pasif durumda.'])->onlyInput('email');
        }

        // Oturum yenilenince sepet kaybolmasın
        $cart = session('cart');
        $request->session()->regenerate();
        if ($cart) {
            session(['cart' => $cart]);
        }

        $default = Auth::user()->is_admin ? route('admin.dashboard') : route('home');

        return redirect()->intended($default)->with('success', 'Hoş geldiniz, ' . Auth::user()->name . '!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:50',
            'surname'    => 'required|string|max:50',
            'email'      => 'required|email|max:100|unique:users,email',
            'phone'      => ['nullable', 'regex:/^0?5\d{9}$/'],
            'password'   => ['required', 'confirmed', Password::min(6)->letters()->numbers()],
            'terms'      => 'accepted',
            'newsletter' => 'nullable|boolean',
        ], [
            'phone.regex'    => 'Telefon numarası 05XX XXX XX XX formatında olmalıdır.',
            'terms.accepted' => 'Demo üyelik koşullarını okuduğunuzu onaylayın.',
        ]);

        $user = User::create([
            'name'       => $data['name'],
            'surname'    => $data['surname'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'password'   => $data['password'],
            'newsletter' => $request->boolean('newsletter'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Üyeliğiniz oluşturuldu. KitapKöşesi\'ne hoş geldiniz!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Güvenli bir şekilde çıkış yaptınız.');
    }
}
