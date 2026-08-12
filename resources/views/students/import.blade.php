@extends('layouts.app')

@section('title', 'Import CSV - ISB DB Manager')

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

            <a href="{{ route('students.import') }}" class="nav-item active">
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

                <h4>Import CSV</h4>

                <p>
                    Tambahkan banyak mahasiswa sekaligus
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

                        <h5>Import Data Mahasiswa</h5>

                        <p>
                            Upload file CSV yang berisi data mahasiswa
                        </p>

                    </div>

                </div>


                <!-- Upload Area -->

                <div class="upload-area">

                    <div class="upload-icon">
                        ⇧
                    </div>

                    <h6>
                        Upload file CSV
                    </h6>

                    <p>
                        Drag & drop file CSV di sini atau pilih file dari komputer
                    </p>

                    <input
                        type="file"
                        class="form-control custom-input mt-3"
                        accept=".csv"
                    >

                    <small>
                        Format yang didukung: .csv
                    </small>

                </div>


                <!-- CSV Format -->

                <div class="csv-format">

                    <div>

                        <strong>
                            Format CSV
                        </strong>

                        <p>
                            Pastikan file CSV memiliki kolom berikut:
                        </p>

                    </div>


                    <code>
                        nim,nama,email,kelas
                    </code>


                    <div class="csv-example">

                        <code>
                            22101001,Budi,budi@student.uc.ac.id,ISB-4
                        </code>

                        <code>
                            22101002,Andi,andi@student.uc.ac.id,ISB-4
                        </code>

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="button"
                        class="btn btn-outline-primary"
                    >
                        Download Template CSV
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                    >
                        Preview Data
                    </button>

                </div>

            </div>


            <!-- Preview -->

            <div class="section-card mt-4">

                <div class="section-header">

                    <div>

                        <h5>Preview Data</h5>

                        <p>
                            Data mahasiswa dari file CSV akan muncul di sini
                        </p>

                    </div>

                </div>


                <div class="empty-state">

                    <div class="empty-icon">
                        📄
                    </div>

                    <strong>
                        Belum ada file
                    </strong>

                    <p>
                        Upload file CSV untuk melihat data mahasiswa.
                    </p>

                </div>

            </div>

        </div>

    </main>

</div>

@endsection