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

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control custom-input"
                    placeholder="Masukkan username"
                    required
                >

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control custom-input"
                    placeholder="Masukkan password"
                    required
                >

            </div>

            <button type="submit" class="btn btn-primary custom-button w-100">
                Login
            </button>

        </form>

    </div>

</div>

@endsection