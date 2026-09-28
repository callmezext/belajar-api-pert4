@extends('layouts.app')

@section('title', 'Enchant AI Gateway - Platform Penyedia API Model AI')

@section('content')
<!-- Hero Section -->
<div style="padding: 2.5rem 0 3rem; text-align: center; max-width: 860px; margin: 0 auto;">
    <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--primary-light); color: var(--primary); padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.825rem; font-weight: 700; margin-bottom: 1.25rem; border: 1px solid var(--primary-border);">
        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--primary);"></span>
        GATEWAY API MODEL AI RESMI &bull; PORT 5000
    </div>

    <h1 style="font-size: 2.65rem; font-weight: 800; color: var(--text-heading); letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 1.25rem;">
        Satu Endpoint Terpadu untuk Model AI Terbaik Dunia
    </h1>

    <p style="font-size: 1.15rem; color: var(--text-muted); line-height: 1.65; margin-bottom: 2rem;">
        Integrasikan model Claude Sonnet, Gemini Flash, dan GPT-OSS ke dalam aplikasi Anda dengan format standar OpenAI SDK. Dilengkapi pembagian kuota token bertingkat (Member Biasa 1M, STARTER 5M, dan SUPER 20M Token).
    </p>

    <div style="display: flex; gap: 1rem; justify-content: center; align-items: center; flex-wrap: wrap;">
        @if(session()->has('jwt_token'))
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                Buka Dashboard Saya &rarr;
            </a>
            @if((session('user')['role'] ?? '') === 'admin')
                <a href="{{ route('admin.index') }}" class="btn btn-secondary btn-lg">
                    Buka Panel Admin
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                Daftar Member Biasa (Dapat 1 Juta Token)
            </a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                Masuk ke Akun
            </a>
        @endif
    </div>
</div>

<!-- Key Highlights -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 3rem;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-heading); margin-bottom: 0.4rem;">
            1. Kompatibilitas OpenAI Penuh
        </div>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Cukup ganti <code style="font-family:var(--font-mono); background:var(--bg-muted); padding:0.1rem 0.3rem;">baseURL</code> di library OpenAI Anda ke <code style="font-family:var(--font-mono); font-size:0.85rem;">http://127.0.0.1:5000/v1</code>. Tanpa perlu merombak kode aplikasi Anda.
        </p>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-heading); margin-bottom: 0.4rem;">
            2. Streaming Respons Real-Time
        </div>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Dukungan penuh Server-Sent Events (SSE). Chunk teks mengalir seketika dengan latensi minimal langsung dari upstream model router.
        </p>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-heading); margin-bottom: 0.4rem;">
            3. Kontrol Kuota &amp; Tingkatan Tier
        </div>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Alokasi token transparan dengan proteksi over-limit otomatis. Admin dapat mengubah status member dan menambah kuota kapan saja.
        </p>
    </div>
</div>

<!-- Tier Pricing & Status Cards -->
<div style="margin-bottom: 3.5rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--text-heading); letter-spacing: -0.02em;">
            Pilihan Status Keanggotaan &amp; Alokasi Token
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem;">
            Setiap akun baru otomatis dimulai dari Member Biasa dengan 1.000.000 token gratis.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 1.5rem; align-items: stretch;">
        <!-- Card 1: Member Biasa -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--tier-free-border);">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="badge badge-free">Tier Dasar</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Gratis</span>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-heading); margin-bottom: 0.5rem;">
                    Member Biasa
                </h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.25rem;">
                    Akun default untuk mencoba API, eksplorasi developer, dan pengujian aplikasi.
                </p>

                <div style="padding: 1rem; background: var(--bg-muted); border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">Alokasi Kuota Token</div>
                    <div style="font-size: 1.65rem; font-weight: 800; color: var(--text-heading); margin-top: 0.2rem;">
                        1.000.000 <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Token</span>
                    </div>
                </div>

                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 0.5rem;">
                    Model yang Dapat Diakses:
                </div>
                <ul style="list-style: none; font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.4rem; font-family: var(--font-mono);">
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.7-flash-low
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.6-flash-low
                    </li>
                </ul>
            </div>

            <div style="margin-top: 1.75rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                @if(session()->has('jwt_token'))
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="width: 100%;">Lihat Status Saya</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-secondary" style="width: 100%;">Daftar Gratis</a>
                @endif
            </div>
        </div>

        <!-- Card 2: STARTER -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--primary); position: relative; box-shadow: var(--shadow-md);">
            <div style="position: absolute; top: -12px; right: 20px; background: var(--primary); color: #ffffff; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.725rem; font-weight: 700; letter-spacing: 0.04em;">
                POPULER
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="badge badge-starter">Langganan</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary);">Paket Menengah</span>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-heading); margin-bottom: 0.5rem;">
                    STARTER Plan
                </h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.25rem;">
                    Cocok untuk developer aktif, aplikasi chatbot, dan integrasi backend intensif.
                </p>

                <div style="padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--primary);">Alokasi Kuota Token</div>
                    <div style="font-size: 1.65rem; font-weight: 800; color: var(--primary); margin-top: 0.2rem;">
                        5.000.000 <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Token</span>
                    </div>
                </div>

                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 0.5rem;">
                    Model yang Dapat Diakses:
                </div>
                <ul style="list-style: none; font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.4rem; font-family: var(--font-mono);">
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.8-flash
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.7-flash-medium
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gpt-oss-120b-medium
                    </li>
                </ul>
            </div>

            <div style="margin-top: 1.75rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                @if(session()->has('jwt_token'))
                    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width: 100%;">Upgrade ke STARTER</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">Mulai Paket STARTER</a>
                @endif
            </div>
        </div>

        <!-- Card 3: SUPER VIP -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid #7e22ce;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="badge badge-super">Tertinggi</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: #7e22ce;">VIP Developer</span>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-heading); margin-bottom: 0.5rem;">
                    SUPER VIP Plan
                </h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.25rem;">
                    Kapasitas kuota token maksimal dengan akses ke model flagship Claude dan reasoning tingkat tinggi.
                </p>

                <div style="padding: 1rem; background: var(--tier-super-bg); border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #7e22ce;">Alokasi Kuota Token</div>
                    <div style="font-size: 1.65rem; font-weight: 800; color: #7e22ce; margin-top: 0.2rem;">
                        20.000.000 <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Token</span>
                    </div>
                </div>

                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 0.5rem;">
                    Model yang Dapat Diakses:
                </div>
                <ul style="list-style: none; font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.4rem; font-family: var(--font-mono);">
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/claude-sonnet-4-6
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.8-flash-high
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> ag/gemini-3.1-pro-low
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #10b981; font-weight: 700;">&check;</span> Akses semua model tier bawah
                    </li>
                </ul>
            </div>

            <div style="margin-top: 1.75rem; padding-top: 1rem; border-top: 1px solid var(--border-light);">
                @if(session()->has('jwt_token'))
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="width: 100%; border-color:#d8b4fe; color:#6b21a8;">Upgrade ke SUPER</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-secondary" style="width: 100%; border-color:#d8b4fe; color:#6b21a8;">Daftar SUPER VIP</a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Code Example Section -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Contoh Pemanggilan API ke Gateway</h2>
        <span class="badge badge-success">Port 5000 Siap Dipakai</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <div>
            <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-heading); margin-bottom: 0.4rem;">
                Request cURL (Terminal / Bash)
            </div>
            <div class="code-snippet">curl -X POST http://127.0.0.1:5000/v1/chat/completions \
  -H "Authorization: Bearer sk-enc-YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "ag/claude-sonnet-4-6",
    "messages": [{"role": "user", "content": "Halo AI!"}],
    "stream": true
  }'</div>
        </div>

        <div>
            <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-heading); margin-bottom: 0.4rem;">
                Integrasi Python (OpenAI SDK Resmi)
            </div>
            <div class="code-snippet">from openai import OpenAI

client = OpenAI(
    api_key="sk-enc-YOUR_API_KEY",
    base_url="http://127.0.0.1:5000/v1"
)

response = client.chat.completions.create(
    model="ag/gemini-3.8-flash",
    messages=[{"role": "user", "content": "Hai!"}],
    stream=True
)

for chunk in response:
    print(chunk.choices[0].delta.content or "", end="")</div>
        </div>
    </div>
</div>
@endsection
