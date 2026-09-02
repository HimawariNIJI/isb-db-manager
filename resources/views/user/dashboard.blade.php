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


                <!-- DAFTAR DATABASE (PRIBADI & KELOMPOK) -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <div>
                            <h5>Daftar Database</h5>
                            <p>Daftar database pribadi dan kelompok yang terhubung dengan akun Anda</p>
                        </div>
                    </div>

                    <!-- SEARCH BAR & FILTER -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0">🔍</span>
                                <input type="text" id="dbSearchInput"
                                    class="form-control border-start-0 ps-0 custom-input"
                                    placeholder="Cari nama database atau nama/NIM anggota...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="dbTypeFilter" class="form-select custom-input fw-medium">
                                <option value="all">Semua Tipe Database</option>
                                <option value="Pribadi">Database Pribadi</option>
                                <option value="Kelompok">Database Kelompok</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle custom-table mb-0">
                            <thead class="table-light text-uppercase small font-monospace">
                                <tr>
                                    <th>Nama Database</th>
                                    <th>Tipe</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Anggota (NIM & Nama)</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dbTableBody">
                                <!-- 1. DATABASE PRIBADI -->
                                <tr class="db-row" data-type="Pribadi">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fs-5">🗄️</span>
                                            <strong
                                                class="text-primary font-monospace">{{ $student->mysql_database ?? '-' }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border fw-medium px-2 py-1">Pribadi</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark rounded-pill px-3">1 Mahasiswa</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            {{ $student->nim }} - {{ $student->nama }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3"
                                            data-bs-toggle="modal" data-bs-target="#modalDetailPribadi">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- 2. DATABASE KELOMPOK (LOOPING) -->
                                @if (isset($student->groups) && $student->groups->count() > 0)
                                    @foreach ($student->groups as $group)
                                        <tr class="db-row" data-type="Kelompok">
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fs-5">🗄️</span>
                                                    <!-- Menggunakan kolom 'database_name' sesuai tabel group_databases -->
                                                    <strong
                                                        class="text-primary font-monospace">{{ $group->database_name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-primary-subtle text-primary border fw-medium px-2 py-1">Kelompok</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-info text-dark rounded-pill px-3">{{ $group->members->count() }}
                                                    Mahasiswa</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($group->members as $member)
                                                        <span class="badge bg-light text-dark border font-monospace">
                                                            {{ $member->nim }} - {{ $member->nama }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDetailGroup{{ $group->id }}">
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                <!-- BARIS JIKA HASIL FILTER KOSONG -->
                                <tr id="noDbDataRow" style="display: none;">
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        🔍 Database atau anggota tidak ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MODAL DETAIL DATABASE PRIBADI -->
                <div class="modal fade" id="modalDetailPribadi" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Detail Database Pribadi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">IP Server</label>
                                        <div class="form-control bg-light font-monospace">100.81.151.126</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Port</label>
                                        <div class="form-control bg-light font-monospace">3306</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Username Database</label>
                                        <div class="form-control bg-light font-monospace">
                                            {{ $student->mysql_username ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Nama Database</label>
                                        <div class="form-control bg-light font-monospace">
                                            {{ $student->mysql_database ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-muted">Password Database</label>
                                        <div class="input-group">
                                            <input type="password" id="modalPasswordPribadi"
                                                class="form-control bg-light font-monospace"
                                                value="{{ $student->mysql_password ?? '' }}" readonly>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="toggleModalPassword('modalPasswordPribadi', this)">👁</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary fw-bold"
                                    data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL DETAIL DATABASE KELOMPOK (LOOPING) -->
                @if (isset($student->groups) && $student->groups->count() > 0)
                    @foreach ($student->groups as $group)
                        <div class="modal fade" id="modalDetailGroup{{ $group->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Detail Database Kelompok:
                                            {{ $group->database_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">IP Server</label>
                                                <div class="form-control bg-light font-monospace">100.81.151.126</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">Port</label>
                                                <div class="form-control bg-light font-monospace">3306</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">Username Database</label>
                                                <!-- Menggunakan username mahasiswa yang sedang login -->
                                                <div class="form-control bg-light font-monospace">
                                                    {{ $student->mysql_username ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">Nama Database</label>
                                                <div class="form-control bg-light font-monospace">
                                                    {{ $group->database_name }}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label text-muted">Password Database</label>
                                                <div class="input-group">
                                                    <input type="password" id="modalPasswordGroup{{ $group->id }}"
                                                        class="form-control bg-light font-monospace"
                                                        value="{{ $student->mysql_password ?? '' }}" readonly>
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="toggleModalPassword('modalPasswordGroup{{ $group->id }}', this)">👁</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif


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
        const dbSearchInput = document.getElementById('dbSearchInput');
        const dbTypeFilter = document.getElementById('dbTypeFilter');
        const dbRows = document.querySelectorAll('.db-row');
        const noDbDataRow = document.getElementById('noDbDataRow');

        function filterDatabases() {
            const searchQuery = dbSearchInput.value.toLowerCase().trim();
            const selectedType = dbTypeFilter.value;
            let visibleCount = 0;

            dbRows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                const rowType = row.getAttribute('data-type');

                const matchesSearch = rowText.includes(searchQuery);
                const matchesFilter = (selectedType === 'all' || rowType === selectedType);

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noDbDataRow) {
                noDbDataRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        if (dbSearchInput && dbTypeFilter) {
            dbSearchInput.addEventListener('input', filterDatabases);
            dbTypeFilter.addEventListener('change', filterDatabases);
        }

        // Toggle Modal Password Visibility
        function toggleModalPassword(inputId, button) {
            const passwordInput = document.getElementById(inputId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                button.classList.add('active');
            } else {
                passwordInput.type = 'password';
                button.classList.remove('active');
            }
        }

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
