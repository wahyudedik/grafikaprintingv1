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
                <h2 class="text-3xl font-bold text-[#212121]">Set New Password</h2>
                <p class="text-[#607D8B] mt-2">Create a strong password for your account</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Input -->
                <div class="mb-6">
                    <label class="block text-[#212121] text-sm font-semibold mb-2">Email address</label>
                    <div class="relative">
                        <input type="email" name="email"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            value="{{ old('email', $request->email) }}" required readonly>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- New Password Input -->
                <div class="mb-6">
                    <label class="block text-[#212121] text-sm font-semibold mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" name="password"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            required>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password Input -->
                <div class="mb-6">
                    <label class="block text-[#212121] text-sm font-semibold mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation"
                            class="pl-10 w-full border-2 border-[#E0E0E0] rounded-lg py-2 px-3 focus:outline-none focus:border-[#2196F3] transition-colors"
                            required>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-[#2196F3] hover:bg-[#1976D2] text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</body>

</html>
