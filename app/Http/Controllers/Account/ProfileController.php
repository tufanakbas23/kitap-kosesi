<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('account.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'       => 'required|string|max:50',
            'surname'    => 'required|string|max:50',
            'email'      => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'phone'      => ['nullable', 'regex:/^0?5\d{9}$/'],
            'birth_date' => 'nullable|date|before:today',
            'gender'     => 'required|in:kadin,erkek,belirtilmemis',
        ], [
            'phone.regex' => 'Telefon numarası 05XX XXX XX XX formatında olmalıdır.',
        ]);

        $user->update($data + ['newsletter' => $request->boolean('newsletter')]);

        return back()->with('success', 'Üyelik bilgileriniz güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', 'different:current_password', Password::min(6)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'Mevcut şifreniz hatalı.',
            'password.different'                => 'Yeni şifre mevcut şifrenizden farklı olmalıdır.',
        ]);

        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Şifreniz başarıyla değiştirildi.');
    }
}
