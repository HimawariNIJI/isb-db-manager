@extends('layouts.app')

@section('title', 'Tambah Mahasiswa - ISB DB Manager')

@section('content')

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-logo small">
                ISB
            </div>

            <div>
                <h5>ISB DB Manager</h5>
                <span>Database Management</span>
            </div>

        </div>

        <nav class="sidebar-nav">

            <a href="{{ route('dashboard') }}" class="nav-item">
                <span>▦</span>
                Dashboard
            </a>

            <a href="{{ route('students.create') }}" class="nav-item active">
                <span>＋</span>
                Tambah Mahasiswa
            </a>

            <a href="{{ route('students.import') }}" class="nav-item">
                <span>⇧</span>
                Import CSV
            </a>

            <a href="{{ route('students.index') }}" class="nav-item">
                <span>☷</span>
                Daftar Mahasiswa
            </a>

            <a href="{{ route('databases.index') }}" class="nav-item">
                <span>🗄</span>
                Manage Database
            </a>

        </nav>

        <div class="sidebar-bottom">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item logout border-0 bg-transparent w-100 text-start">
                    <span>↪</span>
                    Logout
                </button>
            </form>
        </div>

    </aside>


    <!-- Main Content -->
    <main class="main-content">

        <header class="top-header">

            <div>
                <h4>Tambah Mahasiswa</h4>

                <p>
                    Tambahkan mahasiswa secara manual
                </p>
            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    A
                </div>

                <div>
                    <strong>Administrator</strong>
                    <small>Dosen</small>
                </div>

            </div>

        </header>


        <div class="content-container">

            <div class="section-card">

                <div class="section-header">

                    <div>
                        <h5>Data Mahasiswa</h5>

                        <p>
                            Masukkan informasi mahasiswa yang akan dibuatkan akun database
                        </p>
                    </div>

                </div>

                @if (session('error'))
                    <div class="alert alert-danger login-error">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger login-error">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="form-label">
                                NIM
                            </label>

                            <input type="text" name="nim" class="form-control custom-input"
                                placeholder="Contoh: 0706022410099" value="{{ old('nim') }}" required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Mahasiswa
                            </label>

                            <input type="text" name="nama" class="form-control custom-input"
                                placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Email
                            </label>

                            <input type="email" name="email" class="form-control custom-input"
                                placeholder="nama@student.uc.ac.id" value="{{ old('email') }}">

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Kelas
                            </label>

                            <input type="text" name="kelas" class="form-control custom-input"
                                placeholder="Masukkan kelas" value="{{ old('kelas') }}">

                        </div>

                    </div>


                    <hr class="my-4">


                    <div class="database-info">

                        <div class="database-info-icon">
                            DB
                        </div>

                        <div>

                            <strong>
                                Akun database akan dibuat otomatis
                            </strong>

                            <p>
                                Username dan password MySQL akan dibuat setelah data mahasiswa disimpan.
                            </p>

                        </div>

                    </div>


                    <div class="form-actions">

                        <button type="submit" class="btn btn-primary custom-btn">
                            Tambahkan Mahasiswa
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

    </div>

@endsection
