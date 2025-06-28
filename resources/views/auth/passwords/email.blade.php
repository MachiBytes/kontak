@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#181820]">
    <div class="flex flex-col md:flex-row w-full max-w-3xl shadow-lg rounded-lg overflow-hidden">
        <!-- Logo Section -->
        <div class="md:w-1/2 bg-[#23232e] flex flex-col items-center justify-center p-8">
            <img src="/logo/kontak_logo.png" alt="Kontak Logo" class="w-40 h-40 mb-4">
            <h1 class="text-3xl font-bold text-white tracking-widest mt-2">KONTAK</h1>
        </div>
        <!-- Forgot Password Form Section -->
        <div class="md:w-1/2 bg-[#23232e] flex flex-col justify-center p-8">
            <h2 class="text-2xl font-bold text-white mb-2 text-center">Forgot Password</h2>
            <p class="text-gray-400 text-center mb-6">Enter your email to reset your password</p>
            @if (session('status'))
                <div class="mb-4 text-green-400 text-center text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-gray-300 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 rounded-md bg-[#292938] text-white border-none focus:ring-2 focus:ring-green-400">
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col items-center mt-4">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded-md transition">Send Password Reset Link</button>
                </div>
                <div class="flex justify-center mt-4 text-sm">
                    <a href="{{ route('login') }}" class="text-green-400 hover:underline">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
