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

                    <div class="custom-file-input mt-3">

                        <label for="csvFile" class="file-button">
                            Choose File
                        </label>

                        <span id="fileName">
                            No file chosen
                        </span>

                        <input
                            type="file"
                            id="csvFile"
                            accept=".csv"
                        >

                    </div>

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
                        NIM,Nama,Email,Kelas
                    </code>


                    <div class="csv-example">

                        <code>
                            0706022410098,Budi,budi@student.uc.ac.id,"Database A"
                        </code>

                        <code>
                            0706022410099,Andi,andi@student.uc.ac.id,"Database B"
                        </code>

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="button"
                        class="btn btn-outline-primary custom-btn"
                    >
                        Download Template CSV
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary custom-btn"
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
                            Periksa data mahasiswa sebelum melakukan import
                        </p>

                    </div>

                    <span class="student-count">
                        2 Mahasiswa
                    </span>

                </div>


                <div class="table-responsive">

                    <table class="table custom-table align-middle">

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Kelas</th>
                            </tr>

                        </thead>

                        <tbody>

                            <!-- Contoh data preview -->

                            <tr>
                                <td>1</td>
                                <td>22101001</td>
                                <td>Budi</td>
                                <td>budi@student.uc.ac.id</td>
                                <td>ISB-4</td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>22101002</td>
                                <td>Andi</td>
                                <td>andi@student.uc.ac.id</td>
                                <td>ISB-4</td>
                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- Submit / Import -->

                <div class="form-actions">

                    <button
                        type="button"
                        class="btn btn-outline-primary custom-btn"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary custom-btn"
                    >
                        Import Mahasiswa
                    </button>

                </div>

            </div>

        </div>

    </main>

</div>
<script>
    const csvFile = document.getElementById('csvFile');
    const fileName = document.getElementById('fileName');

    csvFile.addEventListener('change', function () {

        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileName.style.color = 'var(--text)';
        } else {
            fileName.textContent = 'No file chosen';
            fileName.style.color = 'var(--muted)';
        }

    });
</script>
@endsection