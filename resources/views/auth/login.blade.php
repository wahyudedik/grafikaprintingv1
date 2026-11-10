<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Login - {{ config('app.name') }}</title>
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-[#2196F3] via-[#00BCD4] to-[#4CAF50]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <!-- Logo/Brand -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-[#212121]">Welcome Back</h2>
                <p class="text-[#607D8B] mt-2">Sign in to your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email Input -->
                <div class="mb-6">
                    <label class="block text-[#212121] text-sm font-semibold mb-2">Email address</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-[#9E9E9E]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        <input type="email" name="email"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-[#212121] text-sm font-semibold">Password</label>
                        @if (\Illuminate\Support\Facades\Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-[#2196F3] hover:text-[#1976D2] transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-[#9E9E9E]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        <input type="password" name="password"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            placeholder="••••••••" required>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember"
                            class="rounded border-[#E0E0E0] text-[#2196F3] focus:ring-[#2196F3]">
                        <span class="ml-2 text-sm text-[#607D8B]">Remember me</span>
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-[#2196F3] hover:bg-[#1976D2] text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                    Sign in
                </button>
            </form>

            <!-- Sign Up Link -->
            {{-- <p class="text-center mt-6 text-sm text-[#607D8B]">
                Don't have an account?
                <a href="{{ route('register') }}"
                    class="text-[#2196F3] hover:text-[#1976D2] font-semibold transition-colors">
                    Sign up
                </a>
            </p> --}}
        </div>
    </div>
</body>

</html>
