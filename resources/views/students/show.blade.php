@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ISB DB Manager')

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

                <h4>Detail Mahasiswa</h4>

                <p>
                    Informasi mahasiswa dan database MySQL
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


            <!-- Back Button -->
            <div class="mb-3">

                <a
                    href="{{ route('students.index') }}"
                    class="btn btn-outline-primary custom-btn"
                >
                    ← Kembali ke Daftar Mahasiswa
                </a>

            </div>


            <!-- Student Information -->
            <div class="section-card">

                <div class="section-header">

                    <div>

                        <h5>Informasi Mahasiswa</h5>

                        <p>
                            Data pribadi mahasiswa
                        </p>

                    </div>

                </div>


                <div class="student-detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            NIM:
                        </span>

                        <strong>
                            {{ $student->nim }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Nama:
                        </span>

                        <strong>
                            {{ $student->nama }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Email:
                        </span>

                        <strong>
                            {{ $student->email ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Kelas:
                        </span>

                        <strong>
                            {{ $student->kelas ?? '-' }}
                        </strong>

                    </div>

                </div>

            </div>


            <!-- MySQL Information -->
            <div class="section-card mt-4">

                <div class="section-header">

                    <div>

                        <h5>Database MySQL</h5>

                        <p>
                            Informasi database dan akun mahasiswa
                        </p>

                    </div>

                </div>


                <div class="student-detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Database:
                        </span>

                        <strong>
                            {{ $student->mysql_database }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Username:
                        </span>

                        <strong>
                            {{ $student->mysql_username }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Host:
                        </span>

                        <strong>
                            {{ config('database.connections.mysql_lab.host') }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Port:
                        </span>

                        <strong>
                            {{ config('database.connections.mysql_lab.port') }}
                        </strong>

                    </div>

                </div>


                <!-- Credential -->
                <div class="credential-box mt-4">

                    <div>

                        <span class="detail-label">
                            Password MySQL:
                        </span>

                        <strong id="mysqlPassword">
                            ••••••••••••
                        </strong>

                    </div>


                    <div class="credential-actions">

                        <button
                            type="button"
                            id="togglePasswordButton"
                            class="btn btn-outline-primary custom-btn mt-2"
                            onclick="togglePassword()"
                        >
                            Tampilkan Password
                        </button>


                        <button
                            type="button" 
                            class="btn btn-primary custom-btn mt-2"
                            onclick="toggleChangePassword()"
                        >
                            Ubah Password
                        </button>

                    </div>

                </div>

                <div
                    id="changePasswordForm"
                    class="change-password-box mt-4"
                    style="display: none;"
                >

                    <div class="section-header">

                        <div>

                            <h6>Ubah Password MySQL</h6>

                            <p>
                                Masukkan password baru untuk akun mahasiswa.
                            </p>

                        </div>

                    </div>


                    <form
                        action="{{ route('students.updatePassword', $student) }}"
                        method="POST"
                    >

                        @csrf

                        @method('PUT')


                        <div class="form-group">

                            <label for="password">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control custom-input"
                                placeholder="Minimal 8 karakter"
                                required
                            >

                        </div>


                        <div class="form-group mt-3">

                            <label for="password_confirmation">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control custom-input"
                                placeholder="Masukkan ulang password"
                                required
                            >

                        </div>


                        <div class="form-actions mt-3">

                            <button
                                type="button"
                                class="btn btn-outline-secondary custom-btn"
                                onclick="toggleChangePassword()"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary custom-btn"
                            >
                                Simpan Password
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            <!-- Account Information -->
            <div class="section-card mt-4">

                <div class="section-header">

                    <div>

                        <h5>Informasi Akun</h5>

                        <p>
                            Informasi pembuatan akun mahasiswa
                        </p>

                    </div>

                </div>


                <div class="student-detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Dibuat:
                        </span>

                        <strong>
                            {{ $student->created_at?->format('d M Y, H:i') ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Terakhir diperbarui:
                        </span>

                        <strong>
                            {{ $student->updated_at?->format('d M Y, H:i') ?? '-' }}
                        </strong>

                    </div>

                </div>

            </div>
            
            <div class="section-card mt-4 delete-section">

            <div class="section-header">

                <div>

                    <h5>Hapus Mahasiswa</h5>

                    <p>
                        Menghapus mahasiswa beserta akun dan database MySQL.
                    </p>

                </div>

            </div>


            <div class="delete-warning">

                <div>
                    <strong>
                        Tindakan ini tidak dapat dibatalkan.
                    </strong>

                    <p>
                        Database
                        <strong>{{ $student->mysql_database }}</strong>
                        dan user MySQL
                        <strong>{{ $student->mysql_username }}</strong>
                        akan dihapus secara permanen.
                    </p>
                </div>


                <form
                    action="{{ route('students.destroy', $student) }}"
                    method="POST"
                    onsubmit="return confirmDelete()"
                >

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger custom-btn"
                    >
                        Hapus Mahasiswa
                    </button>

                </form>

            </div>

        </div>

        </div>

    </main>

</div>


<script>
    let passwordVisible = false;

    function togglePassword() {

        const passwordElement =
            document.getElementById('mysqlPassword');

        const button =
            document.getElementById('togglePasswordButton');

        if (!passwordVisible) {

            passwordElement.textContent =
                @json($student->mysql_password);

            button.textContent = 'Sembunyikan Password';

            passwordVisible = true;

        } else {

            passwordElement.textContent =
                '••••••••••••';

            button.textContent = 'Tampilkan Password';

            passwordVisible = false;
        }
    }

    function toggleChangePassword() {

        const form =
            document.getElementById('changePasswordForm');

        if (form.style.display === 'none') {

            form.style.display = 'block';

        } else {

            form.style.display = 'none';

        }
    }

    function confirmDelete() {

        return confirm(
            'Apakah kamu yakin ingin menghapus mahasiswa ini?\n\n' +
            'Data mahasiswa, user MySQL, dan database mahasiswa akan dihapus secara permanen.'
        );
    }
</script>

@endsection