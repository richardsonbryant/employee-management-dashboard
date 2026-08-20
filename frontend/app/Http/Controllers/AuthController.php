<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\UserAPIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{

    protected $userService;

    public function __construct(

        UserAPIService $userService
    ) {

        $this->userService = $userService;
    }


    public function login()
    {
        return view("auth.login");
    }

    function loginPost(Request $request)
    {
        $request->validate([
            "email" => ["required", "max : 255"],
            "password" => ["required"]
        ]);
        $credentials = $request->only("email", "password");
        if (Auth::attempt($credentials)) {
            // Get the authenticated user
            $user = Auth::user();

            // Check the user's role and redirect accordingly
            if ($user->role == 'admin') {
                return redirect()->route('admin-dashboard');
            } else {
                return redirect()->route('employee-dashboard');
            }
        }
        return redirect(route("login"))->with("error", "login failed");
    }




    // public function loginPost(Request $request)
    // {
    //     $request->validate([
    //         "email" => ["required", "max:255"],
    //         "password" => ["required"]
    //     ]);

    //     Log::debug('Login attempt', [
    //         'email' => $request->email,
    //         'password' => $request->password
    //     ]);

    //     // Panggil service untuk login
    //     $user = $this->userService->attemptLogin($request->email, $request->password);

    //     Log::debug('Login API response', [
    //         'status' => $user ? 'success' : 'failed',
    //         'user' => $user
    //     ]);

    //     if ($user) {
    //         // Simpan user ke session manual
    //         session(['user' => $user]);

    //         // Arahkan ke dashboard berdasarkan role
    //         if ($user['role'] === 'admin') {
    //             return redirect()->route('admin-dashboard');
    //         } else {
    //             return redirect()->route('employee-dashboard');
    //         }
    //     }

    //     return redirect()->route('login')->with('error', 'Login gagal, cek kembali email atau password.');
    // }

    function register()
    {
        return view("register");
    }
    public function registerPost(Request $request, UserAPIService $userService)
    {
        try {
            $request->validate([
                "fullname" => ["required", "max:255"],
                "email" => ["required", "email", "max:255"],
                "password" => ["required"],
                "role" => ["required", "in:admin,employee"],
            ]);

            $data = [
                'name' => $request->fullname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ];

            Log::debug('Sending data to API:', $data);

            $createdUser = $userService->createUser($data);

            Log::debug('Response from API:', ['response' => $createdUser]);

            if ($createdUser && !isset($createdUser['error'])) {
                return redirect(route("admin-dashboard"))
                    ->with("success", "User Created Successfully");
            }

            Log::error('Failed to create user. API Response:', ['response' => $createdUser]);

            return redirect(route("register"))
                ->with("error", "Failed Create Account");
        } catch (\Exception $e) {
            Log::error('Exception during user registration', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(route("register"))
                ->with("error", "Something went wrong: " . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
