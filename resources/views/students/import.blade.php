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

                <a href="{{ route('group-databases.create') }}" class="nav-item">
                    <span>👥</span>
                    Group Database
                </a>

                <a href="{{ route('students.import') }}" class="nav-item active">
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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong>⚠ Import Tidak Berhasil</strong>
                        <p class="mb-0 mt-1">{{ session('error') }}</p>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <strong>✓ Import Berhasil</strong>
                        <p class="mb-0 mt-1">{{ session('success') }}</p>

                        @if (session('skipped_count', 0) > 0)
                            <p class="mb-0 mt-1">
                                <strong>{{ session('skipped_count') }}</strong>
                                data dilewati karena sudah terdaftar atau bukan mahasiswa.
                            </p>
                        @endif

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif


                @if (session('import_errors') && count(session('import_errors')) > 0)
                    <div class="alert alert-warning mb-4" role="alert">

                        <h6 class="fw-bold mb-2">
                            ⚠ Detail Data yang Dilewati
                        </h6>

                        <ul class="mb-0">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                @endif

                <form action="{{ route('students.import.store') }}" method="POST" enctype="multipart/form-data"
                    id="importForm">
                    @csrf

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

                        <div class="upload-area" id="uploadArea">

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

                                <input type="file" id="csvFile" name="csv_file" accept=".csv">

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
                                nim,nama,email,kelas
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

                            <a href="{{ route('students.template') }}" class="btn btn-outline-primary custom-btn">
                                Download Template CSV
                            </a>

                            <button type="button" id="previewButton" class="btn btn-primary custom-btn">
                                Preview Data
                            </button>

                        </div>

                    </div>


                    <!-- Preview -->
                    <div class="section-card mt-4" id="previewSection" style="display: none;">

                        <div class="section-header">

                            <div>

                                <h5>Preview Data</h5>

                                <p>
                                    Periksa data mahasiswa sebelum melakukan import
                                </p>

                            </div>

                            <span class="student-count" id="studentCount">
                                0 Mahasiswa
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

                                <tbody id="previewTableBody">

                                </tbody>

                            </table>

                        </div>


                        <!-- Submit / Import -->

                        <div class="form-actions">

                            <button type="button" id="cancelPreviewButton" class="btn btn-outline-primary custom-btn">
                                Batal
                            </button>

                            <button type="submit" id="importButton" class="btn btn-primary custom-btn" disabled>
                                Import Mahasiswa
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </main>

    </div>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const csvFile = document.getElementById('csvFile');
        const fileName = document.getElementById('fileName');

        const previewButton =
            document.getElementById('previewButton');

        const previewSection =
            document.getElementById('previewSection');

        const previewTableBody =
            document.getElementById('previewTableBody');

        const studentCount =
            document.getElementById('studentCount');

        const cancelPreviewButton =
            document.getElementById('cancelPreviewButton');

        const importButton =
            document.getElementById('importButton');

        /*
        |--------------------------------------------------------------------------
        | FILE INPUT
        |--------------------------------------------------------------------------
        */

        csvFile.addEventListener('change', function() {

            if (this.files.length > 0) {

                fileName.textContent =
                    this.files[0].name;

                fileName.style.color =
                    'var(--text)';

                // Sembunyikan preview ketika file diganti
                previewSection.style.display =
                    'none';

                previewTableBody.innerHTML =
                    '';

                handleFile(this.files[0]);

            } else {

                fileName.textContent =
                    'No file chosen';

                fileName.style.color =
                    'var(--muted)';

                previewSection.style.display =
                    'none';

                resetFile();

            }

        });

        // =========================
        // Handle File
        // =========================

        function handleFile(file) {

            fileName.textContent = file.name;

            fileName.style.color = 'var(--text)';

        }


        // =========================
        // Reset File
        // =========================

        function resetFile() {

            fileName.textContent = 'No file chosen';

            fileName.style.color = 'var(--muted)';

            csvFile.value = '';

        }

        // =========================
        // Drag Over
        // =========================

        uploadArea.addEventListener('dragover', function(e) {

            e.preventDefault();

            uploadArea.classList.add('drag-over');

        });


        // =========================
        // Drag Leave
        // =========================

        uploadArea.addEventListener('dragleave', function(e) {

            e.preventDefault();

            uploadArea.classList.remove('drag-over');

        });


        // =========================
        // Drop
        // =========================

        uploadArea.addEventListener('drop', function(e) {

            e.preventDefault();

            uploadArea.classList.remove('drag-over');

            const files = e.dataTransfer.files;

            if (files.length > 0) {

                const file = files[0];

                // Pastikan CSV
                if (
                    file.type === 'text/csv' ||
                    file.name.toLowerCase().endsWith('.csv')
                ) {

                    // Masukkan file ke input
                    const dataTransfer = new DataTransfer();

                    dataTransfer.items.add(file);

                    csvFile.files = dataTransfer.files;

                    // Update nama file
                    handleFile(file);

                } else {

                    alert('Silakan pilih file CSV.');

                }

            }

        });

        /*
        |--------------------------------------------------------------------------
        | PREVIEW BUTTON
        |--------------------------------------------------------------------------
        */

        previewButton.addEventListener('click', function() {

            /*
            |--------------------------------------------------------------------------
            | CHECK FILE
            |--------------------------------------------------------------------------
            */

            if (!csvFile.files.length) {

                previewSection.style.display =
                    'none';

                alert(
                    'Silakan pilih file CSV terlebih dahulu.'
                );

                return;
            }


            const file =
                csvFile.files[0];


            /*
            |--------------------------------------------------------------------------
            | CHECK FILE TYPE
            |--------------------------------------------------------------------------
            */

            if (
                !file.name
                .toLowerCase()
                .endsWith('.csv')
            ) {

                previewSection.style.display =
                    'none';

                alert(
                    'File harus berformat CSV.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | READ CSV
            |--------------------------------------------------------------------------
            */

            const reader =
                new FileReader();

            reader.onload = function(event) {

                const text = event.target.result;

                const rows = parseCSV(text);

                /*
                |--------------------------------------------------------------------------
                | CHECK CSV
                |--------------------------------------------------------------------------
                */

                if (rows.length <= 1) {

                    previewSection.style.display = 'none';

                    alert(
                        'File CSV tidak memiliki data mahasiswa.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | DETEKSI FORMAT CSV
                |--------------------------------------------------------------------------
                */

                const header = rows[0].map(function(value) {

                    return value
                        .replace(/^\uFEFF/, '')
                        .trim()
                        .toLowerCase();

                });


                const webHeader = [
                    'nim',
                    'nama',
                    'email',
                    'kelas'
                ];


                const elearnHeader = [
                    'first name',
                    'last name',
                    'email address',
                    'groups'
                ];


                let csvFormat = null;


                if (
                    JSON.stringify(header) ===
                    JSON.stringify(webHeader)
                ) {

                    csvFormat = 'web';

                } else if (
                    JSON.stringify(header) ===
                    JSON.stringify(elearnHeader)
                ) {

                    csvFormat = 'elearn';

                } else {

                    previewSection.style.display = 'none';

                    alert(
                        'Format CSV tidak sesuai. Gunakan CSV dari Web atau eLearn.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | CLEAR PREVIOUS DATA
                |--------------------------------------------------------------------------
                */

                previewTableBody.innerHTML = '';


                /*
                |--------------------------------------------------------------------------
                | REMOVE HEADER
                |--------------------------------------------------------------------------
                */

                const dataRows = rows.slice(1);


                let validRows = 0;
                let skippedLecturers = 0;


                /*
                |--------------------------------------------------------------------------
                | PROCESS ROW
                |--------------------------------------------------------------------------
                */

                dataRows.forEach(function(row) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lewati baris kosong
                    |--------------------------------------------------------------------------
                    */

                    if (
                        row.length === 1 &&
                        row[0].trim() === ''
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Pastikan minimal 4 kolom
                    |--------------------------------------------------------------------------
                    */

                    if (row.length < 4) {
                        return;
                    }


                    let nim;
                    let nama;
                    let email;
                    let kelas;


                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT WEB
                    |--------------------------------------------------------------------------
                    */

                    if (csvFormat === 'web') {

                        nim =
                            row[0]?.trim() || '';

                        nama =
                            row[1]?.trim() || '';

                        email =
                            row[2]?.trim() || '';

                        kelas =
                            row[3]?.trim() || '';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT ELEARN
                    |--------------------------------------------------------------------------
                    */

                    else {

                        nim =
                            row[0]?.trim() || '';

                        nama =
                            row[1]?.trim() || '';

                        email =
                            row[2]?.trim() || '';

                        /*
                        |--------------------------------------------------------------------------
                        | CEK DOSEN
                        |
                        | Mahasiswa:
                        | First name = NIM berupa angka
                        |
                        | Dosen:
                        | First name = Nama
                        |--------------------------------------------------------------------------
                        */

                        if (!/^\d+$/.test(nim)) {

                            skippedLecturers++;

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Groups DIABAIKAN
                        |--------------------------------------------------------------------------
                        |
                        | eLearn tidak digunakan untuk menentukan kelas.
                        |
                        */

                        kelas = '';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI NIM & NAMA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        nim === '' ||
                        nama === ''
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE TABLE ROW
                    |--------------------------------------------------------------------------
                    */

                    const tr =
                        document.createElement('tr');


                    /*
                    |--------------------------------------------------------------------------
                    | NO
                    |--------------------------------------------------------------------------
                    */

                    const no =
                        document.createElement('td');

                    no.textContent =
                        validRows + 1;


                    /*
                    |--------------------------------------------------------------------------
                    | NIM
                    |--------------------------------------------------------------------------
                    */

                    const nimCell =
                        document.createElement('td');

                    nimCell.textContent =
                        nim;


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA
                    |--------------------------------------------------------------------------
                    */

                    const namaCell =
                        document.createElement('td');

                    namaCell.textContent =
                        nama;


                    /*
                    |--------------------------------------------------------------------------
                    | EMAIL
                    |--------------------------------------------------------------------------
                    */

                    const emailCell =
                        document.createElement('td');

                    emailCell.textContent =
                        email || '-';


                    /*
                    |--------------------------------------------------------------------------
                    | KELAS
                    |--------------------------------------------------------------------------
                    */

                    const kelasCell =
                        document.createElement('td');

                    kelasCell.textContent =
                        kelas || '-';


                    /*
                    |--------------------------------------------------------------------------
                    | APPEND CELLS
                    |--------------------------------------------------------------------------
                    */

                    tr.appendChild(no);

                    tr.appendChild(nimCell);

                    tr.appendChild(namaCell);

                    tr.appendChild(emailCell);

                    tr.appendChild(kelasCell);


                    /*
                    |--------------------------------------------------------------------------
                    | APPEND ROW
                    |--------------------------------------------------------------------------
                    */

                    previewTableBody.appendChild(tr);


                    validRows++;

                });


                /*
                |--------------------------------------------------------------------------
                | UPDATE STUDENT COUNT
                |--------------------------------------------------------------------------
                */

                studentCount.textContent =
                    validRows + ' Mahasiswa';


                /*
                |--------------------------------------------------------------------------
                | SHOW PREVIEW
                |--------------------------------------------------------------------------
                */

                if (validRows > 0) {

                    previewSection.style.display =
                        'block';

                    importButton.disabled =
                        false;

                } else {

                    previewSection.style.display =
                        'none';

                    importButton.disabled =
                        true;

                    alert(
                        'Tidak ditemukan data mahasiswa.'
                    );

                }

            };

            reader.onerror = function() {

                previewSection.style.display =
                    'none';

                alert(
                    'Gagal membaca file CSV.'
                );

            };


            reader.readAsText(file);

        });

        cancelPreviewButton.addEventListener('click', function() {

            // Sembunyikan preview
            previewSection.style.display = 'none';

            // Kosongkan tabel
            previewTableBody.innerHTML = '';

            // Reset jumlah mahasiswa
            studentCount.textContent = '0 Mahasiswa';

            // Hapus file yang dipilih
            csvFile.value = '';

            // Reset nama file
            fileName.textContent = 'No file chosen';

            fileName.style.color = 'var(--muted)';

            importButton.disabled = true;

        });


        /*
        |--------------------------------------------------------------------------
        | CSV PARSER
        |--------------------------------------------------------------------------
        |
        | Mendukung:
        |
        | nim,nama,email,kelas
        |
        | 070...,Budi,budi@email.com,"Database A"
        |
        |--------------------------------------------------------------------------
        */

        function parseCSV(text) {

            const rows = [];

            let row = [];

            let value = '';

            let insideQuotes = false;


            for (
                let i = 0; i < text.length; i++
            ) {

                const char =
                    text[i];

                const nextChar =
                    text[i + 1];


                /*
                | Double quote di dalam quote
                */

                if (
                    char === '"' &&
                    insideQuotes &&
                    nextChar === '"'
                ) {

                    value += '"';

                    i++;

                }


                /*
                | Mulai / selesai quote
                */
                else if (
                    char === '"'
                ) {

                    insideQuotes = !insideQuotes;

                }


                /*
                | Comma
                */
                else if (
                    char === ',' &&
                    !insideQuotes
                ) {

                    row.push(value);

                    value = '';

                }


                /*
                | New line
                */
                else if (
                    (char === '\n' ||
                        char === '\r') &&
                    !insideQuotes
                ) {

                    // Windows CRLF
                    if (
                        char === '\r' &&
                        nextChar === '\n'
                    ) {

                        i++;

                    }


                    row.push(value);

                    rows.push(row);

                    row = [];

                    value = '';

                }


                /*
                | Character biasa
                */
                else {

                    value += char;

                }

            }


            /*
            | Tambahkan row terakhir
            */

            if (
                value !== '' ||
                row.length > 0
            ) {

                row.push(value);

                rows.push(row);

            }


            return rows;

        }
    </script>
@endsection
