<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showAuthForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            if (strtolower(Auth::user()->role) === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard')->with('success', 'Ви успішно увійшли в систему!');
        }

        return back()->withErrors([
            'email' => 'Невірний email або passwords.',
        ])->onlyInput('email');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'User',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Реєстрація успішна! Ласкаво просимо.');
    }


    public function dashboard()
    {
        $user = Auth::user();

        if (strtolower($user->role) === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $myEstablishments = Establishment::where('user_id', $user->id)->get();

        return view('dashboard', compact('user', 'myEstablishments'));
    }


    public function adminDashboard()
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);

        $users = \App\Models\User::all();
        $establishments = \App\Models\Establishment::all();
        $reviews = \App\Models\Review::all();

        return view('admin', compact('users', 'establishments', 'reviews'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Ви вийшли з системи.');
    }


    public function approveEstablishment($id)
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);
        $establishment = Establishment::findOrFail($id);
        $establishment->update(['is_approved' => true]);
        return back()->with('success', "Заклад '{$establishment->name}' успішно додано на мапу!");
    }


    public function updateUserRole(Request $request, $id)
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);

        $user = \App\Models\User::findOrFail($id);


        $newRole = ucfirst(strtolower($request->role));


        $user->role = $newRole;
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', "Роль користувача {$user->name} успішно змінено на '{$newRole}'!");
    }


    public function deleteUser($id)
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return back()->with('success', "Користувача {$user->name} видалено з системи!");
    }


    public function deleteEstablishment($id)
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);
        $est = Establishment::findOrFail($id);
        $est->delete();
        return back()->with('success', "Заклад '{$est->name}' видалено!");
    }


    public function deleteReview($id)
    {
        if (strtolower(Auth::user()->role) !== 'admin') abort(403);
        $review = Review::findOrFail($id);
        $review->delete();
        return back()->with('success', "Відгук видалено!");
    }
}
