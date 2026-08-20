@extends('layouts.app')

@section('title', 'Detail Database - ISB DB Manager')

@section('content')

    <div class="dashboard-wrapper">

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

                <a href="{{ route('students.create') }}" class="nav-item">
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

                <a href="{{ route('databases.index') }}" class="nav-item active">
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

            <!-- Header -->
            <header class="top-header">
                <div>
                    <h4>{{ $database->mysql_database ?? 'db_sample' }}</h4>
                    <p>Atur hak akses pengguna dan izin tabel secara spesifik</p>
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

            <!-- Content Container -->
            <div class="content-container">

                <div class="mb-3">
                    <a href="{{ route('databases.index') }}" class="btn btn-sm btn-outline-secondary">
                        ← Kembali ke Daftar Database
                    </a>
                </div>
                <!-- Alert Pesan Sukses / Error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                        <strong>Periksa kembali inputan Anda:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('databases.grant', $database->id) }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <!-- Kolom Kiri: Pilih User & Pilih Tabel -->
                        <div class="col-lg-7">

                            <!-- 1. Pilih User / Mahasiswa -->
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <div>
                                        <h5>Pilih User / Mahasiswa</h5>
                                        <p>Centang user yang akan diberikan hak akses ke database ini</p>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table custom-table align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                                </th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Username DB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($students as $student)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input user-checkbox" type="checkbox"
                                                            name="users[]" value="{{ $student->id }}">
                                                    </td>
                                                    <td>{{ $student->nim }}</td>
                                                    <td>{{ $student->nama }}</td>
                                                    <td><code>{{ $student->mysql_username }}</code></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">
                                                        Belum ada data mahasiswa / user terdaftar.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. Pilih Tabel Target -->
                            <div class="section-card">
                                <div class="section-header">
                                    <div>
                                        <h5>Pilih Tabel Target</h5>
                                        <p>Pilih tabel dalam database yang boleh diakses</p>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="allTables" name="all_tables"
                                            value="1">
                                        <label class="form-check-label fw-bold" for="allTables">
                                            Semua Tabel (*)
                                        </label>
                                        <div class="form-text">Memberikan akses ke seluruh tabel dalam database ini.</div>
                                    </div>

                                    <div class="row g-2">
                                        @forelse($tables as $table)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input table-checkbox" type="checkbox"
                                                        name="tables[]" value="{{ $table }}"
                                                        id="tbl_{{ $table }}">
                                                    <label class="form-check-label" for="tbl_{{ $table }}">
                                                        <code>{{ $table }}</code>
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-2 text-muted">
                                                <small>⚠️ Belum ada tabel yang dibuat di dalam database ini.</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Kolom Kanan: Hak Akses (Privileges) & Submit -->
                        <div class="col-lg-5">

                            <div class="section-card position-sticky" style="top: 20px;">
                                <div class="section-header">
                                    <div>
                                        <h5>Hak Akses (Privileges)</h5>
                                        <p>Pilih jenis operasi yang diizinkan</p>
                                    </div>
                                </div>

                                <div class="p-3">

                                    <div class="form-check mb-3 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="grantAll" name="permissions[]"
                                            value="ALL">
                                        <label class="form-check-label fw-bold text-primary" for="grantAll">
                                            ALL PRIVILEGES
                                        </label>
                                        <div class="form-text">Memberikan semua izin operasi secara penuh.</div>
                                    </div>

                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="SELECT" id="perm_select">
                                            <label class="form-check-label" for="perm_select">
                                                <span class="badge bg-info text-dark me-1">SELECT</span> — Membaca /
                                                melihat data
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="INSERT" id="perm_insert">
                                            <label class="form-check-label" for="perm_insert">
                                                <span class="badge bg-success me-1">INSERT</span> — Menambahkan data baru
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="UPDATE" id="perm_update">
                                            <label class="form-check-label" for="perm_update">
                                                <span class="badge bg-warning text-dark me-1">UPDATE</span> — Mengubah /
                                                memperbarui data
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="DELETE" id="perm_delete">
                                            <label class="form-check-label" for="perm_delete">
                                                <span class="badge bg-danger me-1">DELETE</span> — Menghapus data
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="EXECUTE" id="perm_execute">
                                            <label class="form-check-label" for="perm_execute">
                                                <span class="badge bg-secondary me-1">EXECUTE</span> — Menjalankan Stored
                                                Procedure
                                            </label>
                                        </div>
                                    </div>

                                    <hr>

                                    <button type="submit" class="btn btn-primary custom-btn w-100 py-2">
                                        Simpan & Terapkan Hak Akses
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                </form>

                <!-- 4. Tabel Mahasiswa yang Sudah Memiliki Akses -->
                <div class="section-card mt-4">
                    <div class="section-header border-bottom pb-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-2">
                            <div>
                                <h5 class="mb-1">Hak Akses yang Sedang Aktif</h5>
                                <p class="text-muted small mb-0">Daftar mahasiswa yang memiliki akses beserta detail izin
                                    tabelnya</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Sorting Controls Bar -->
                    <div class="p-3 bg-light border-bottom">
                        <form action="{{ route('databases.show', $database->id) }}" method="GET"
                            class="row g-2 align-items-center">

                            <!-- Searching -->
                            <div class="col-md-4">
                                <input type="text" name="search_active" class="form-control form-control-sm"
                                    placeholder="🔍 Cari NIM, Nama, Username..." value="{{ request('search_active') }}">
                            </div>

                            <!-- Filter Table -->
                            <div class="col-md-3">
                                <select name="table_filter" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    <option value="all">-- Semua Tabel Target --</option>
                                    <option value="*" {{ request('table_filter') === '*' ? 'selected' : '' }}>Akses
                                        Global (*)</option>
                                    @foreach ($tables as $tbl)
                                        <option value="{{ $tbl }}"
                                            {{ request('table_filter') === $tbl ? 'selected' : '' }}>
                                            {{ $tbl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sorting Column -->
                            <div class="col-md-3">
                                <select name="sort_by" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="student_name"
                                        {{ request('sort_by', 'student_name') === 'student_name' ? 'selected' : '' }}>
                                        Urutkan: Nama</option>
                                    <option value="nim" {{ request('sort_by') === 'nim' ? 'selected' : '' }}>Urutkan:
                                        NIM</option>
                                    <option value="username" {{ request('sort_by') === 'username' ? 'selected' : '' }}>
                                        Urutkan: Username</option>
                                    <option value="table" {{ request('sort_by') === 'table' ? 'selected' : '' }}>Urutkan:
                                        Tabel Target</option>
                                </select>
                            </div>

                            <!-- Sort Direction & Reset -->
                            <div class="col-md-2 d-flex gap-1">
                                <select name="sort_dir" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="asc" {{ request('sort_dir', 'asc') === 'asc' ? 'selected' : '' }}>
                                        ASC ↑</option>
                                    <option value="desc" {{ request('sort_dir') === 'desc' ? 'selected' : '' }}>DESC ↓
                                    </option>
                                </select>

                                @if (request()->hasAny(['search_active', 'table_filter', 'sort_by', 'sort_dir']))
                                    <a href="{{ route('databases.show', $database->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                                        ✕
                                    </a>
                                @endif
                            </div>

                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Username DB</th>
                                    <th>Tabel Target</th>
                                    <th>Hak Akses (Privileges)</th>
                                    <th class="text-center" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grantedAccess as $access)
                                    <tr>
                                        <td>{{ $access['nim'] }}</td>
                                        <td><strong>{{ $access['student_name'] }}</strong></td>
                                        <td><code>{{ $access['username'] }}</code></td>
                                        <td>
                                            <span
                                                class="badge {{ $access['table'] === '*' ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $access['table'] === '*' ? 'Semua Tabel (*)' : $access['table'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="fw-semibold text-dark">{{ $access['privileges'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('databases.revoke', $database->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses ini?')">
                                                @csrf
                                                <input type="hidden" name="username" value="{{ $access['username'] }}">
                                                <input type="hidden" name="host" value="{{ $access['host'] }}">
                                                <input type="hidden" name="table" value="{{ $access['table'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Cabut Akses
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Data tidak ditemukan atau belum ada hak akses yang aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Check All Users
            const selectAllUsers = document.getElementById('selectAllUsers');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');

            if (selectAllUsers) {
                selectAllUsers.addEventListener('change', function() {
                    userCheckboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            // Toggle All Tables Checkbox behavior
            const allTablesCb = document.getElementById('allTables');
            const tableCheckboxes = document.querySelectorAll('.table-checkbox');

            if (allTablesCb) {
                allTablesCb.addEventListener('change', function() {
                    tableCheckboxes.forEach(cb => {
                        cb.disabled = this.checked;
                        if (this.checked) cb.checked = false;
                    });
                });
            }
        });
    </script>
@endsection
