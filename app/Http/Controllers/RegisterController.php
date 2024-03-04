<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register.index',[
            'title' => 'Register',
            'active' => 'register'
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'username' => ['required', 'min:3', 'max:255', 'unique:users'],
            'email' => 'required|email:dns|unique:users',
            'password' => [ 'required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()]
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);
        // $request->session()->flash('success', 'Registration sucessfull! Please login.');
        return redirect('/login')->with('success', 'Registration sucessfull! Please login.');
    }
}
