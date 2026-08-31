@extends('layouts.app')

@section('title', 'Login - ISB DB Manager')

@section('content')

    <div class="login-page">

        <div class="login-card">

            <div class="text-center mb-4">

                <div class="brand-logo">
                    ISB
                </div>

                <h3 class="login-title">
                    ISB DB Manager
                </h3>

                <p class="login-subtitle">
                    Login untuk mengelola akun database mahasiswa
                </p>

            </div>

            @if (session('error'))
                <div class="alert alert-danger login-error">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success login-error">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Username
                    </label>

                    <input type="text" name="username" class="form-control custom-input" placeholder="Masukkan username"
                        required>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Password
                    </label>

                    <input type="password" name="password" class="form-control custom-input" placeholder="Masukkan password"
                        required>

                </div>

                <button type="submit" class="btn btn-primary custom-button w-100">
                    Login
                </button>

            </form>

            <!-- Pemisah garis halus -->
            <div class="d-flex align-items-center my-3 text-muted">
                <hr class="flex-grow-1 m-0">
                <span class="px-2 small text-uppercase fw-semibold">atau</span>
                <hr class="flex-grow-1 m-0">
            </div>

            <!-- Tombol Login dengan Google -->
            <a href="{{ route('auth.google') }}"
                class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold">
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                    <path fill="#FFC107"
                        d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
                    <path fill="#FF3D00"
                        d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" />
                    <path fill="#4CAF50"
                        d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" />
                    <path fill="#1976D2"
                        d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
                </svg>
                <span>Login dengan Google</span>
            </a>

        </div>

    </div>

@endsection
