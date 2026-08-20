@extends('layouts.app')

@section('title', 'Dashboard - ISB DB Manager')

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

            <!-- Header -->
            <header class="top-header">

                <div>

                    <h4>Dashboard</h4>

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


            <!-- Content -->
            <div class="content-container">

                <!-- Statistics -->
                <div class="row g-4 mb-4">

                    <div class="col-md-4">

                        <div class="stat-card">

                            <div class="stat-icon blue">
                                👨‍🎓
                            </div>

                            <div>
                                <span>Total Mahasiswa</span>
                                <h3>{{ $totalStudents }}</h3>
                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="stat-card">

                            <div class="stat-icon orange">
                                🗄
                            </div>

                            <div>
                                <span>Total Database</span>

                                <h3>
                                    {{ $totalDatabases }}
                                </h3>
                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="stat-card">

                            <div class="stat-icon green">
                                +
                            </div>

                            <div>
                                <span>Ditambahkan Hari Ini</span>
                                <h3>{{ $todayStudents }}</h3>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Student List -->
                <div class="section-card">

                    <div class="section-header">

                        <div>

                            <h5>Daftar Mahasiswa</h5>

                            <p>
                                Mahasiswa yang Baru Ditambahkan
                            </p>

                        </div>

                        <div class="d-flex gap-2">

                            <a href="{{ route('students.index') }}" class="btn btn-primary custom-btn">
                                Lihat Semua
                            </a>

                        </div>

                    </div>


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

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="6">

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

                </div>

            </div>

        </main>

    </div>

@endsection
