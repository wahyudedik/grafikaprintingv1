<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Verify Email - {{ config('app.name') }}</title>
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-[#2196F3] via-[#00BCD4] to-[#4CAF50]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-[#212121]">Verify Your Email</h2>
                <p class="text-[#607D8B] mt-4">
                    {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-[#E8F5E9] text-[#4CAF50] rounded-lg">
                    {{ __('A new verification link has been sent to your email address.') }}
                </div>
            @endif

            <div class="flex flex-col space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-[#2196F3] hover:bg-[#1976D2] text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-105 shadow-lg">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-[#F5F5F5] hover:bg-[#E0E0E0] text-[#212121] font-bold py-3 px-4 rounded-lg transition duration-300">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
