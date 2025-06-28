@extends('layouts.bare_app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#181820]">
    <div class="flex flex-col md:flex-row w-full max-w-3xl shadow-lg rounded-lg overflow-hidden">
        <!-- Logo Section -->
        <div class="md:w-1/2 bg-[#23232e] flex flex-col items-center justify-center p-8">
            <img src="/logo/kontak_logo.png" alt="Kontak Logo" class="w-40 h-40 mb-4">
            <h1 class="text-3xl font-bold text-white tracking-widest mt-2">KONTAK</h1>
        </div>
        <!-- Login Form Section -->
        <div class="md:w-1/2 bg-[#23232e] flex flex-col justify-center p-8">
            <h2 class="text-2xl font-bold text-white mb-2 text-center">Login</h2>
            <p class="text-gray-400 text-center mb-6">Welcome To Kontak</p>
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-gray-300 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 rounded-md bg-[#292938] text-white border-none focus:ring-2 focus:ring-green-400">
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-gray-300 mb-1">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-4 py-2 rounded-md bg-[#292938] text-white border-none focus:ring-2 focus:ring-green-400">
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col items-center mt-4">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded-md transition">LOGIN</button>
                </div>
                <div class="flex justify-between items-center mt-4 text-sm">
                    <a href="{{ route('register') }}" class="text-green-400 hover:underline">Don't have an account ?</a>
                    <a href="{{ route('password.request') }}" class="text-green-400 hover:underline">Forgot Password ?</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
