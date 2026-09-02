@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - ISB DB Manager')

@section('content')

    <div class="dashboard-wrapper">

        <!-- Main Content -->
        <main class="w-100">

            <!-- Header -->
            <header class="top-header">

                <!-- LEFT -->
                <div class="header-title">
                    <h4>Student Dashboard</h4>
                    <p> Informasi akun dan database mahasiswa </p>
                </div>

                <!-- DESKTOP USER -->
                <div class="student-header-right desktop-user">
                    <div class="admin-profile">
                        <div class="admin-avatar">
                            {{ strtoupper(substr($student->nama, 0, 1)) }}
                        </div>
                        <div class="student-profile-info">
                            <strong title="{{ $student->nama }}">{{ $student->nama }}</strong>
                            <small>Mahasiswa</small>
                        </div>
                    </div>
                    <form action="{{ route('userlogout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger fw-bold logout-btn">
                            ↪ Logout
                        </button>
                    </form>
                </div>

                <!-- MOBILE BURGER -->
                <button type="button" class="mobile-menu-button" id="mobileMenuButton">
                    ☰
                </button>

                <!-- MOBILE MENU -->
                <div class="mobile-user-menu" id="mobileUserMenu">
                    <div class="mobile-user-info">
                        <div class="admin-avatar"> {{ strtoupper(substr($student->nama, 0, 1)) }} </div>
                        <div>
                            <strong> {{ $student->nama }} </strong>
                            <small> Mahasiswa </small>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"class="mobile-logout-btn">
                            ↪ Logout
                        </button>
                    </form>
                </div>
            </header>

            <div class="content-container">

                <!-- ALERT ERROR -->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">

                        <strong>✕ Gagal</strong>

                        <div>
                            {{ session('error') }}
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    </div>
                @endif


                <!-- ALERT SUCCESS -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">

                        <strong>✓ Berhasil</strong>

                        <div>
                            {{ session('success') }}
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    </div>
                @endif


                <!-- ERROR VALIDATION -->
                @if ($errors->any())

                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">

                        <strong>✕ Terdapat kesalahan:</strong>

                        <ul class="mb-0 mt-2">

                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach

                        </ul>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    </div>

                @endif


                <!-- INFORMASI MAHASISWA -->
                <div class="section-card mb-4">

                    <div class="section-header">

                        <div>

                            <h5>Informasi Mahasiswa</h5>

                            <p>
                                Informasi akun mahasiswa yang terdaftar
                            </p>

                        </div>

                    </div>


                    <div class="row g-4">

                        <!-- NIM -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                NIM
                            </label>

                            <div class="form-control bg-light student-data-value">
                                {{ $student->nim }}
                            </div>

                        </div>


                        <!-- NAMA -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Nama
                            </label>

                            <div class="form-control bg-light student-data-value">
                                {{ $student->nama }}
                            </div>

                        </div>


                        <!-- EMAIL -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Email
                            </label>

                            <div class="form-control bg-light student-data-value">
                                {{ $student->email }}
                            </div>

                        </div>


                        <!-- KELAS -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Kelas
                            </label>

                            <div class="form-control bg-light student-data-value">
                                {{ $student->kelas ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>


                <!-- INFORMASI DATABASE -->
                <div class="section-card mb-4">

                    <div class="section-header">

                        <div>

                            <h5>Informasi Database</h5>

                            <p>
                                Gunakan informasi berikut untuk terhubung
                                menggunakan MySQL Workbench
                            </p>

                        </div>

                    </div>


                    <div class="row g-4">

                        <!-- IP SERVER -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                IP Server
                            </label>

                            <div class="form-control bg-light font-monospace student-data-value">
                                100.81.151.126
                            </div>

                        </div>


                        <!-- PORT -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Port
                            </label>

                            <div class="form-control bg-light font-monospace student-data-value">
                                3306
                            </div>

                        </div>


                        <!-- USERNAME -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Username Database
                            </label>

                            <div class="form-control bg-light font-monospace student-data-value">
                                {{ $student->mysql_username ?? '-' }}
                            </div>

                        </div>


                        <!-- DATABASE -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Nama Database
                            </label>

                            <div class="form-control bg-light font-monospace student-data-value">
                                {{ $student->mysql_database ?? '-' }}
                            </div>

                        </div>


                        <!-- PASSWORD -->
                        <div class="col-md-6">

                            <label class="form-label text-muted">
                                Password Database
                            </label>

                            <div class="input-group">

                                <input type="password" id="databasePassword"
                                    class="form-control bg-light font-monospace student-data-value"
                                    value="{{ $student->mysql_password ?? '' }}" readonly>

                                <button type="button" id="togglePasswordButton"
                                    class="btn btn-outline-secondary password-toggle-btn" onclick="togglePassword()">
                                    👁
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- UBAH PASSWORD -->
                <div class="section-card">

                    <div class="section-header">

                        <div>

                            <h5>Ubah Password Database</h5>

                            <p>
                                Ubah password yang digunakan untuk login
                                ke MySQL Workbench
                            </p>

                        </div>

                    </div>


                    <form action="{{ route('user.password.update') }}" method="POST">

                        @csrf
                        @method('PUT')


                        <div class="row g-4">

                            <!-- PASSWORD BARU -->
                            <div class="col-md-6">

                                <label for="password" class="form-label fw-bold">
                                    Password Baru
                                </label>

                                <input type="password" id="password" name="password" class="form-control custom-input"
                                    placeholder="Minimal 8 karakter" required>

                            </div>


                            <!-- KONFIRMASI -->
                            <div class="col-md-6">

                                <label for="password_confirmation" class="form-label fw-bold">
                                    Konfirmasi Password
                                </label>

                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control custom-input" placeholder="Ulangi password baru" required>

                            </div>

                        </div>


                        <div class="mt-4">

                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                Ubah Password
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>

    </div>

    <script>
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileUserMenu = document.getElementById('mobileUserMenu');

        /*
        |--------------------------------------------------------------------------
        | OPEN / CLOSE MENU 
        |--------------------------------------------------------------------------
        */
        mobileMenuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            const isOpen = mobileUserMenu.classList.contains('show');
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | OPEN
        |--------------------------------------------------------------------------
        */
        function openMobileMenu() {
            mobileUserMenu.classList.add('show');
            mobileMenuButton.classList.add('active');
        }

        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */
        function closeMobileMenu() {
            mobileUserMenu.classList.remove('show');
            mobileMenuButton.classList.remove('active');
        }

        /*
        |--------------------------------------------------------------------------
        | CLICK OUTSIDE
        |--------------------------------------------------------------------------
        */
        document.addEventListener('click', function(event) {
            if (!mobileUserMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                closeMobileMenu();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | ESC KEY
        |--------------------------------------------------------------------------
        */
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMobileMenu();
            }
        });

        function togglePassword() {
            const password = document.getElementById('databasePassword');
            const button = document.getElementById('togglePasswordButton');
            if (password.type === 'password') {
                password.type = 'text';
                button.classList.add('active');
            } else {
                password.type = 'password';
                button.classList.remove('active');
            }
        }
    </script>

@endsection
