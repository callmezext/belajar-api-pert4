@extends('layouts.app')

@section('title', 'Daftar Akun Baru - Enchant AI Gateway')

@section('content')
<div style="max-width: 480px; margin: 2rem auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Registrasi Member Baru</h1>
            <span class="brand-badge">Gratis 1.000.000 Token</span>
        </div>

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi (Minimal 6 karakter)</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div class="form-hint">Kata sandi dienkripsi dengan standar bcrypt.</div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar &amp; Ambil API Key</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); font-size: 0.875rem;">
            <p style="color: var(--text-subtle);">
                Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a>.
            </p>
        </div>
    </div>
</div>
@endsection
