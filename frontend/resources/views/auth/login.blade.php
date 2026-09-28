@extends('layouts.app')

@section('title', 'Masuk Akun - Enchant AI Gateway')

@section('content')
<div style="max-width: 440px; margin: 3rem auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Masuk ke Gateway</h1>
            <span class="badge badge-free">Autentikasi</span>
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Akun</button>
            </div>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-light); font-size: 0.875rem; text-align: center;">
            <p style="color: var(--text-muted);">
                Belum punya akun? <a href="{{ route('register') }}" style="font-weight: 600;">Daftar akun baru</a>
            </p>
        </div>
    </div>
</div>
@endsection
