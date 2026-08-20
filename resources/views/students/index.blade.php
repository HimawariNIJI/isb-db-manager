@extends('layouts.app')

@section('title', 'Daftar Mahasiswa - ISB DB Manager')

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

                <a href="{{ route('dashboard') }}" class="nav-item active">
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

                    <h4>Daftar Mahasiswa</h4>

                    <p>
                        Kelola akun database mahasiswa
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

                            <h5>Mahasiswa Terdaftar</h5>

                            <p>
                                Daftar mahasiswa dan akun database mereka
                            </p>

                        </div>


                        <div class="d-flex gap-2">

                            <a href="{{ route('students.export') }}" class="btn btn-outline-primary custom-btn">
                                Export CSV
                            </a>

                            <a href="{{ route('students.import') }}" class="btn btn-outline-primary custom-btn">
                                Import CSV
                            </a>

                            <a href="{{ route('students.create') }}" class="btn btn-primary custom-btn">
                                + Tambah Mahasiswa
                            </a>

                        </div>

                    </div>


                    <!-- Search -->

                    <div class="row mb-4">

                        <div class="col-md-5">

                            <form action="{{ route('students.index') }}" method="GET">

                                <div class="input-group">

                                    <span class="input-group-text bg-white">
                                        🔍
                                    </span>

                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control custom-input" placeholder="Cari NIM atau nama mahasiswa...">

                                    @if (request('search'))
                                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                            ✕
                                        </a>
                                    @endif

                                </div>

                            </form>

                        </div>

                    </div>


                    <!-- Table -->

                    <div class="table-responsive">

                        <table class="table custom-table align-middle">

                            <thead>

                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Kelas</th>
                                    <th>Database MySQL</th>
                                    <th>Username MySQL</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>


                            <tbody>

                                @forelse($students as $student)
                                    <tr>

                                        <td>
                                            {{ $student->nim }}
                                        </td>

                                        <td>
                                            {{ $student->nama }}
                                        </td>

                                        <td>
                                            {{ $student->email ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $student->kelas ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $student->mysql_database }}
                                        </td>

                                        <td>
                                            {{ $student->mysql_username }}
                                        </td>

                                        <td>

                                            <a href="{{ route('students.show', $student->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7">

                                            <div class="empty-state">

                                                <div class="empty-icon">
                                                    👨‍🎓
                                                </div>

                                                <strong>
                                                    Belum ada mahasiswa
                                                </strong>

                                                <p>
                                                    Data mahasiswa yang sudah dibuat akan muncul di sini.
                                                </p>

                                            </div>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    @if ($students->hasPages())

                        <div class="student-pagination-wrapper">

                            <div class="student-pagination">

                                {{-- Previous --}}
                                @if ($students->onFirstPage())
                                    <span class="student-pagination-box disabled">
                                        ‹
                                    </span>
                                @else
                                    <a href="{{ $students->previousPageUrl() }}" class="student-pagination-box">
                                        ‹
                                    </a>
                                @endif


                                {{-- Page Numbers --}}
                                @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                    @if ($page == $students->currentPage())
                                        <span class="student-pagination-box active">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="student-pagination-box">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach


                                {{-- Next --}}
                                @if ($students->hasMorePages())
                                    <a href="{{ $students->nextPageUrl() }}" class="student-pagination-box">
                                        ›
                                    </a>
                                @else
                                    <span class="student-pagination-box disabled">
                                        ›
                                    </span>
                                @endif

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </main>

    </div>

@endsection
