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
                            <h3>23</h3>
                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="stat-card">

                        <div class="stat-icon green">
                            ✓
                        </div>

                        <div>
                            <span>Akun Aktif</span>
                            <h3>23</h3>
                        </div>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="stat-card">

                        <div class="stat-icon orange">
                            ⏳
                        </div>

                        <div>
                            <span>Pending</span>
                            <h3>10</h3>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Add Student -->
            <div class="section-card mb-4">

                <div class="section-header">

                    <div>
                        <h5>Tambah Mahasiswa</h5>

                        <p>
                            Buat akun database untuk mahasiswa
                        </p>
                    </div>

                </div>


                <form>

                    <div class="row g-3">

                        <div class="col-md-4">

                            <label class="form-label">
                                NIM
                            </label>

                            <input
                                type="text"
                                class="form-control custom-input"
                                placeholder="Contoh: 22101001"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Nama Mahasiswa
                            </label>

                            <input
                                type="text"
                                class="form-control custom-input"
                                placeholder="Masukkan nama"
                            >

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control custom-input"
                                placeholder="email@student.uc.ac.id"
                            >

                        </div>

                    </div>


                    <div class="form-actions">

                        <button type="submit" class="btn btn-primary custom-btn">
                            Tambahkan Mahasiswa
                        </button>

                    </div>

                </form>

            </div>


            <!-- Student List -->
            <div class="section-card">

                <div class="section-header">

                    <div>

                        <h5>Daftar Mahasiswa</h5>

                        <p>
                            Mahasiswa yang telah ditambahkan
                        </p>

                    </div>

                    <button type="button" class="btn btn-outline-primary custom-btn">
                        Export CSV
                    </button>

                </div>


                <div class="table-responsive">

                    <table class="table custom-table align-middle">

                        <thead>

                            <tr>

                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Username MySQL</th>
                                <th>Status</th>
                                <th>Aksi</th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td colspan="6" class="empty-state">

                                    <div class="empty-icon">
                                        👨‍🎓
                                    </div>

                                    <strong>
                                        Belum ada mahasiswa
                                    </strong>

                                    <p>
                                        Tambahkan mahasiswa untuk mulai membuat akun database.
                                    </p>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>

@endsection