<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Регистрация
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Создаём токен для Sanctum (SPA не требует токена, но если хотите доп. безопасность)
        // При SPA аутентификации токены обычно не используются, используется cookie.
        // Sanctum автоматически аутентифицирует через cookie, если запрос из SPA.
        // Поэтому просто возвращаем пользователя.
        return response()->json(['user' => $user], 201);
    }

    // Логин
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Предоставленные учетные данные неверны.'],
            ]);
        }

        $request->session()->regenerate();

        $user = User::where('email', $request->email)->first();

        return response()->json(['user' => $user]);
    }

    // Логаут
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Успешный выход']);
    }

    // Получение текущего пользователя
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()   // оборачиваем в объект { user: ... }
        ]);
    }
}
