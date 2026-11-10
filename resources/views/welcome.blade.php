<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">
    <title>Grafika Printing - Smart Printing Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F5F5F5]">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-[#2196F3] via-[#00BCD4] to-[#4CAF50]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1 class="text-4xl font-bold text-white sm:text-5xl md:text-6xl">
                        <span class="block">Grafika</span>
                        <span class="block text-[#FFC107]">Printing System</span>
                    </h1>
                    <p class="mt-3 text-base text-white sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                        Streamline your printing business with our comprehensive POS system. Manage orders, track
                        inventory, and boost productivity.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-full text-white bg-[#FF4081] hover:bg-[#F44336] transform hover:scale-105 transition duration-300 shadow-lg">
                            Get Started
                        </a>
                    </div>
                </div>
                <div class="mt-12 lg:mt-0 lg:col-span-6">
                    <div class="bg-white rounded-2xl shadow-2xl p-6 transform hover:rotate-2 transition duration-300">
                        <img src="{{ asset('dist/img/hero.jpg') }}" alt="POS System Preview" class="rounded-xl">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-extrabold text-center text-[#212121] mb-16">
                Features That Power Your Printing Business
            </h2>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Feature Cards -->
                <div
                    class="bg-gradient-to-br from-[#2196F3] to-[#00BCD4] rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg text-white">
                    <div class="text-[#FFC107] text-3xl mb-6">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold">Order Management</h3>
                    <p class="mt-4 text-gray-100">Track and manage printing orders efficiently with real-time status
                        updates.</p>
                </div>

                <!-- Add similar styling for other feature cards -->
                <div
                    class="bg-gradient-to-br from-[#2196F3] to-[#00BCD4] rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg text-white">
                    <div class="text-[#FFC107] text-3xl mb-6">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold">Inventory Control</h3>
                    <p class="mt-4 text-gray-100">Keep track of your materials and supplies with automated inventory
                        management.</p>
                </div>

                <div
                    class="bg-gradient-to-br from-[#2196F3] to-[#00BCD4] rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg text-white">
                    <div class="text-[#FFC107] text-3xl mb-6">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold">Analytics & Reports</h3>
                    <p class="mt-4 text-gray-100">Generate detailed reports and insights to make data-driven decisions.
                    </p>
                </div>

                <div
                    class="bg-gradient-to-br from-[#2196F3] to-[#00BCD4] rounded-2xl p-8 transform hover:-translate-y-2 transition duration-300 shadow-lg text-white">
                    <div class="text-[#FFC107] text-3xl mb-6">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold">Customer Management</h3>
                    <p class="mt-4 text-gray-100">Build and maintain customer relationships with integrated CRM
                        features.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section with new colors -->
    <div class="bg-[#F5F5F5] py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-[#2196F3]">
                    <p class="text-[#607D8B] mb-4">"Grafika Printing System has revolutionized our printing business.
                        It's user-friendly and incredibly efficient."</p>
                    <p class="font-bold text-[#2196F3]">- John Doe, CEO of PrintMaster</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-[#2196F3]">
                    <p class="text-[#607D8B] mb-4">"Grafika Printing System has revolutionized our printing business.
                        It's user-friendly and incredibly efficient."</p>
                    <p class="font-bold text-[#2196F3]">- John Doe, CEO of PrintMaster</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-[#2196F3]">
                    <p class="text-[#607D8B] mb-4">"Grafika Printing System has revolutionized our printing business.
                        It's user-friendly and incredibly efficient."</p>
                    <p class="font-bold text-[#2196F3]">- John Doe, CEO of PrintMaster</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-4xl font-extrabold text-center text-[#212121] mb-12">Choose Your Plan</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-b from-[#2196F3] to-[#00BCD4] rounded-xl p-8 shadow-lg text-white">
                    <h3 class="text-2xl font-bold mb-4">Starter</h3>
                    <p class="text-4xl font-bold text-[#FFC107] mb-6">$49<span class="text-lg text-white">/month</span>
                    </p>
                    <!-- Add pricing content -->
                    <ul class="list-disc list-inside text-white">
                        <li>All Starter Features</li>
                        <li>Advanced Reporting</li>
                        <li>Priority Support</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-b from-[#2196F3] to-[#00BCD4] rounded-xl p-8 shadow-lg text-white">
                    <h3 class="text-2xl font-bold mb-4">Pro</h3>
                    <p class="text-4xl font-bold text-[#FFC107] mb-6">$99<span class="text-lg text-white">/month</span>
                    </p>
                    <!-- Add pricing content -->
                    <ul class="list-disc list-inside text-white">
                        <li>All Starter Features</li>
                        <li>Advanced Reporting</li>
                        <li>Priority Support</li>
                    </ul>
                </div>
                <div class="bg-gradient-to-b from-[#2196F3] to-[#00BCD4] rounded-xl p-8 shadow-lg text-white">
                    <h3 class="text-2xl font-bold mb-4">Enterprise</h3>
                    <p class="text-4xl font-bold text-[#FFC107] mb-6">$199<span
                            class="text-lg text-white">/month</span>
                    </p>
                    <!-- Add pricing content -->
                    <ul class="list-disc list-inside text-white">
                        <li>All Pro Features</li>
                        <li>Custom Reporting</li>
                        <li>Dedicated Success Manager</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
