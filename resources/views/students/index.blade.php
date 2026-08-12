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

            <a href="{{ route('students.index') }}" class="nav-item active">
                <span>☷</span>
                Daftar Mahasiswa
            </a>

        </nav>


        <div class="sidebar-bottom">

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="nav-item logout border-0 bg-transparent w-100 text-start"
                >
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

                        <a
                            href="{{ route('students.import') }}"
                            class="btn btn-outline-primary"
                        >
                            Import CSV
                        </a>

                        <a
                            href="{{ route('students.create') }}"
                            class="btn btn-primary"
                        >
                            + Tambah Mahasiswa
                        </a>

                    </div>

                </div>


                <!-- Search -->

                <div class="row mb-4">

                    <div class="col-md-5">

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                🔍
                            </span>

                            <input
                                type="text"
                                class="form-control custom-input"
                                placeholder="Cari NIM atau nama mahasiswa..."
                            >

                        </div>

                    </div>

                    <div class="col-md-3">

                        <select class="form-select custom-input">

                            <option selected>
                                Semua Status
                            </option>

                            <option>
                                Aktif
                            </option>

                            <option>
                                Pending
                            </option>

                        </select>

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
                                        Data mahasiswa yang sudah dibuat akan muncul di sini.
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