<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Reset Password - {{ config('app.name') }}</title>
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-[#2196F3] via-[#00BCD4] to-[#4CAF50]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-[#212121]">Password Reset</h2>
                <p class="text-[#607D8B] mt-4">
                    {{ __('Forgot your password? No problem. Enter your email address and we will send you a password reset link.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-6">
                    <label class="block text-[#212121] text-sm font-semibold mb-2">Email address</label>
                    <div class="relative">
                        <input type="email" name="email"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('login') }}"
                        class="text-[#2196F3] hover:text-[#1976D2] text-sm font-semibold transition-colors">
                        Back to login
                    </a>

                    <button type="submit"
                        class="bg-[#2196F3] hover:bg-[#1976D2] text-white font-bold py-2 px-6 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                        Send Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
