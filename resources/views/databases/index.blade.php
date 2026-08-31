@extends('layouts.app')

@section('title', 'Databases - ISB DB Manager')

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

                <a href="{{ route('group-databases.create') }}" class="nav-item">
                    <span>👥</span>
                    Group Database
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
                    <h4>Manage Database</h4>
                    <p>Daftar semua database yang sudah dibuat</p>
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

                <div class="section-card">

                    <div class="section-header">
                        <div>
                            <h5>Daftar Database</h5>
                            <p>Semua database terdaftar dalam sistem</p>
                        </div>
                    </div>

                    <div class="row mb-4">

                        <div class="col-md-5">

                            <form action="{{ route('databases.index') }}" method="GET">

                                <div class="input-group">

                                    <span class="input-group-text bg-white">
                                        🔍
                                    </span>

                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control custom-input"
                                        placeholder="Cari nama database atau NIM - Mahasiswa...">

                                    @if (request('search'))
                                        <a href="{{ route('databases.index') }}" class="btn btn-outline-secondary">
                                            ✕
                                        </a>
                                    @endif

                                </div>

                            </form>

                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Database</th>
                                    <th>NIM - Mahasiswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($databases as $db)
                                    <tr>
                                        <td>{{ $db->mysql_database }}</td>
                                        <td>{{ $db->nim }} — {{ $db->nama }}</td>
                                        <td>
                                            <a href="{{ route('databases.show', $db->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <div class="empty-icon">🗄</div>
                                                <strong>Tidak ada database</strong>
                                                <p>Belum ada database yang dibuat.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($databases->hasPages())

                        <div class="student-pagination-wrapper">

                            <div class="student-pagination">

                                {{-- Previous --}}
                                @if ($databases->onFirstPage())
                                    <span class="student-pagination-box disabled">
                                        ‹
                                    </span>
                                @else
                                    <a href="{{ $databases->previousPageUrl() }}" class="student-pagination-box">
                                        ‹
                                    </a>
                                @endif


                                {{-- Page Numbers --}}
                                @foreach ($databases->getUrlRange(1, $databases->lastPage()) as $page => $url)
                                    @if ($page == $databases->currentPage())
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
                                @if ($databases->hasMorePages())
                                    <a href="{{ $databases->nextPageUrl() }}" class="student-pagination-box">
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
