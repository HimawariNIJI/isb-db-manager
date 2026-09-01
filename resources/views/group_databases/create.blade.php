@extends('layouts.app')

@section('title', 'Group Database - ISB DB Manager')

@section('content')

    <div class="dashboard-wrapper">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo small">ISB</div>
                <div>
                    <h5>ISB DB Manager</h5>
                    <span>Database Management</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item"><span>▦</span> Dashboard</a>
                <a href="{{ route('students.create') }}" class="nav-item"><span>＋</span> Tambah Mahasiswa</a>
                <a href="{{ route('group-databases.create') }}" class="nav-item active"><span>🗄</span> Group Database</a>
                <a href="{{ route('students.import') }}" class="nav-item"><span>⇧</span> Import CSV</a>
                <a href="{{ route('students.index') }}" class="nav-item"><span>☷</span> Daftar Mahasiswa</a>
                <a href="{{ route('databases.index') }}" class="nav-item"><span>🗄</span> Manage Database</a>
            </nav>

            <div class="sidebar-bottom">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item logout border-0 bg-transparent w-100 text-start">
                        <span>↪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Header -->
            <header class="top-header">
                <div>
                    <h4>Group Databases</h4>
                    <p>Buat dan kelola database kelompok mahasiswa</p>
                </div>

                <div class="admin-profile">
                    <div class="admin-avatar">A</div>
                    <div>
                        <strong>Administrator</strong>
                        <small>Dosen</small>
                    </div>
                </div>
            </header>

            <!-- Content Container -->
            <div class="content-container">

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">

                        <h5 class="alert-heading fw-bold">
                            ✓ {{ session('success') }}
                        </h5>

                        @if (session('group_created'))

                            <p class="mb-1">
                                Mahasiswa berikut dapat login melalui MySQL Workbench:
                            </p>

                            <ul class="mb-2">
                                <li>
                                    <strong>Host:</strong>
                                    100.81.151.126
                                </li>

                                <li>
                                    <strong>Port:</strong>
                                    3306
                                </li>

                                <li>
                                    <strong>Database:</strong>
                                    {{ session('db_name') }}
                                </li>
                            </ul>

                            @if (session('credentials'))

                                <div class="table-responsive">

                                    <table class="table table-sm table-bordered bg-white mb-0">

                                        <thead class="table-light">
                                            <tr>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Username DB</th>
                                                <th>Password MySQL (Generated)</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach (session('credentials') as $cred)

                                                <tr>

                                                    <td>
                                                        {{ $cred['nim'] }}
                                                    </td>

                                                    <td>
                                                        {{ $cred['nama'] }}
                                                    </td>

                                                    <td>
                                                        <code class="text-danger font-monospace">
                                                            {{ $cred['username'] }}
                                                        </code>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-dark text-warning fs-6 font-monospace">
                                                            {{ $cred['password'] }}
                                                        </span>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @endif

                        @endif

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>

                    </div>
                @endif

                <!-- FORM PEMBUATAN GROUP DATABASE -->
                <form action="{{ route('group-databases.store') }}" method="POST" class="mb-5">
                    @csrf
                    <div class="row g-4">
                        <!-- Tabel Pilih Mahasiswa -->
                        <div class="col-lg-7">
                            <div class="section-card h-100">
                                <div class="section-header">
                                    <div>
                                        <h5>Pilih User / Mahasiswa</h5>
                                        <p>Centang user yang akan dimasukkan ke dalam kelompok database</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white">🔍</span>
                                        <input type="text" id="searchStudent" class="form-control custom-input"
                                            placeholder="Cari NIM, Nama, atau Username DB...">
                                    </div>
                                </div>

                                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                    <table class="table custom-table align-middle" id="studentTable">
                                        <thead class="sticky-top bg-white">
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                </th>
                                                <th>NIM</th>
                                                <th>NAMA MAHASISWA</th>
                                                <th>USERNAME DB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($students as $student)
                                                <tr>
                                                    <td class="text-center">
                                                        <input class="form-check-input student-checkbox" type="checkbox"
                                                            name="students[]" value="{{ $student->id }}"
                                                            {{ is_array(old('students')) && in_array($student->id, old('students')) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>{{ $student->nim }}</td>
                                                    <td class="fw-bold">{{ strtoupper($student->nama) }}</td>
                                                    <td class="text-danger font-monospace">{{ $student->mysql_username }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">Tidak ada data
                                                        mahasiswa.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Form Parameter Database -->
                        <div class="col-lg-5">
                            <div class="section-card h-100">
                                <div class="section-header">
                                    <div>
                                        <h5>Group Databases</h5>
                                        <p>Konfigurasi database baru untuk kelompok</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="database_name" class="form-label fw-bold">Nama Database Kelompok</label>
                                    <input type="text" name="database_name" id="database_name"
                                        class="form-control custom-input @error('database_name') is-invalid @enderror"
                                        placeholder="Contoh: kelompok_proyek_a" value="{{ old('database_name') }}"
                                        required>
                                    @error('database_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="p-3 bg-light rounded mb-4 small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Host:</span>
                                        <strong class="font-monospace">100.81.151.126</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Port:</span>
                                        <strong class="font-monospace">3306</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Akses Privilege:</span>
                                        <span class="badge bg-success">ALL PRIVILEGES</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                                    Buat Database Kelompok
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- TABEL DAFTAR DATABASE KELOMPOK -->
                <div class="section-card">

                    <div class="section-header">
                        <div>
                            <h5>Daftar Database Kelompok</h5>
                            <p>Semua database kelompok yang telah dibuat beserta daftar anggotanya</p>
                        </div>
                    </div>

                    <!-- Filter Search -->
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white">🔍</span>
                                <input type="text" id="searchGroupDb" class="form-control custom-input"
                                    placeholder="Cari nama database kelompok atau nama anggota...">
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Utama -->
                    <div class="table-responsive">
                        <table class="table custom-table align-middle" id="groupDbTable">
                            <thead>
                                <tr>
                                    <th>NAMA DATABASE KELOMPOK</th>
                                    <th>JUMLAH ANGGOTA</th>
                                    <th>ANGGOTA (NIM & NAMA)</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupDatabases as $gDb)
                                    <!-- Baris Utama Database -->
                                    <tr class="group-db-row">
                                        <td>
                                            <strong class="text-primary font-monospace fs-6">🗄
                                                {{ $gDb->database_name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold">
                                                {{ $gDb->students->count() }} Mahasiswa
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($gDb->students as $st)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $st->nim }} - {{ strtoupper($st->nama) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <!-- Tombol Detail (Toggle Collapse) -->
                                                <button class="btn btn-sm btn-outline-primary fw-bold" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#detail-row-{{ $gDb->id }}"
                                                    aria-expanded="false">
                                                    Detail
                                                </button>

                                                <!-- Tombol Delete -->
                                                <form action="{{ route('group-databases.destroy', $gDb->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus database {{ $gDb->database_name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- BARIS DETAIL (DENGAN INFORMASI HOST & PORT) -->
                                    <tr class="collapse" id="detail-row-{{ $gDb->id }}">
                                        <td colspan="4" class="p-0 border-0">
                                            <div class="p-3 bg-light border-bottom">

                                                <!-- Card Info Host & Port Connection -->
                                                <div
                                                    class="p-2 px-3 bg-white rounded border mb-3 d-flex align-items-center gap-4 flex-wrap shadow-sm">
                                                    <div>
                                                        <small class="text-muted d-block">Host Server:</small>
                                                        <strong
                                                            class="font-monospace text-dark fs-6">100.81.151.126</strong>
                                                    </div>
                                                    <div class="border-start ps-4">
                                                        <small class="text-muted d-block">Port:</small>
                                                        <strong class="font-monospace text-dark fs-6">3306</strong>
                                                    </div>
                                                    <div class="border-start ps-4">
                                                        <small class="text-muted d-block">Database Name:</small>
                                                        <strong
                                                            class="font-monospace text-primary fs-6">{{ $gDb->database_name }}</strong>
                                                    </div>
                                                </div>

                                                <!-- Tabel Anggota Kelompok -->
                                                <div class="table-responsive bg-white rounded border">
                                                    <table class="table table-bordered align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="fw-bold">NIM</th>
                                                                <th class="fw-bold">Nama</th>
                                                                <th class="fw-bold">Username DB</th>
                                                                <th class="fw-bold">Password MySQL (Generated)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($gDb->students as $student)
                                                                <tr>
                                                                    <td>{{ $student->nim }}</td>
                                                                    <td class="fw-bold">{{ strtoupper($student->nama) }}
                                                                    </td>
                                                                    <td>
                                                                        <code class="text-danger font-monospace fs-6">
                                                                            {{ $student->mysql_username }}
                                                                        </code>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-dark text-warning fs-6 font-monospace px-3 py-1">
                                                                            {{ $gDb->password ?? 'N/A' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <div class="empty-icon">🗄</div>
                                                <strong>Belum Ada Database Kelompok</strong>
                                                <p>Gunakan form di atas untuk membuat database kelompok baru.</p>
                                            </div>
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

    <!-- Live Search Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const searchStudentInput = document.getElementById('searchStudent');
            const selectAllCheckbox = document.getElementById('selectAll');
            const studentTableRows = document.querySelectorAll('#studentTable tbody tr');

            // Ambil semua checkbox mahasiswa yang sedang terlihat
            function getVisibleStudentCheckboxes() {
                return Array.from(
                    document.querySelectorAll('#studentTable tbody .student-checkbox')
                ).filter(checkbox => {
                    const row = checkbox.closest('tr');
                    return row && row.style.display !== 'none';
                });
            }

            // Update checkbox "Select All"
            function updateSelectAllState() {
                const checkboxes = getVisibleStudentCheckboxes();

                if (checkboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    return;
                }

                // TRUE hanya jika SEMUA checkbox dicentang
                const allChecked = checkboxes.every(
                    checkbox => checkbox.checked
                );

                selectAllCheckbox.checked = allChecked;
            }

            // ==========================
            // SEARCH MAHASISWA
            // ==========================
            if (searchStudentInput) {
                searchStudentInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();

                    studentTableRows.forEach(row => {
                        const text = row.innerText.toLowerCase();

                        row.style.display = text.includes(filter)
                            ? ''
                            : 'none';
                    });

                    updateSelectAllState();
                });
            }

            // ==========================
            // SELECT ALL
            // ==========================
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;

                    getVisibleStudentCheckboxes().forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });
            }

            // ==========================
            // CHECKBOX INDIVIDUAL
            // ==========================
            const studentCheckboxes = document.querySelectorAll(
                '#studentTable tbody .student-checkbox'
            );

            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                });
            });

            // ==========================
            // INITIAL STATE
            // ==========================
            updateSelectAllState();


            // ==========================
            // SEARCH GROUP DATABASE
            // ==========================
            const searchGroupDbInput = document.getElementById('searchGroupDb');
            const groupDbRows = document.querySelectorAll(
                '#groupDbTable tbody tr.group-db-row'
            );

            if (searchGroupDbInput) {
                searchGroupDbInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();

                    groupDbRows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        const nextCollapseRow = row.nextElementSibling;

                        if (text.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';

                            if (
                                nextCollapseRow &&
                                nextCollapseRow.classList.contains('collapse')
                            ) {
                                nextCollapseRow.classList.remove('show');
                            }
                        }
                    });
                });
            }

        });
    </script>

@endsection
