<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower($request->email),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                'unique:'.User::class,
                'regex:/^[a-zA-Z0-9._%+-]+\.(sales|scm)@scg\.com$/'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.regex' => 'The name may only contain letters and spaces.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name must not exceed 50 characters.',
            'email.regex' => 'The email must be a valid official corporate email ending with .sales@scg.com or .scm@scg.com.',
        ]);

        $role = str_ends_with($request->email, '.scm@scg.com') ? 'admin_scm' : 'pic_sales';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        event(new Registered($user));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Registration successful!'),
                'redirect' => route('login', absolute: false)
            ]);
        }

        return redirect(route('login', absolute: false))->with('success', __('Registration successful!'));
    }
}
