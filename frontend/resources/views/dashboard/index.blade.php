@extends('layouts.app')

@section('title', 'Dashboard Member - Enchant AI Gateway')

@section('content')
<!-- Header & Actions -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.25rem;">
            <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-heading); letter-spacing: -0.02em;">
                Dashboard Member
            </h1>
            <span class="badge {{ $user['tier'] === 'FREE' ? 'badge-free' : ($user['tier'] === 'STARTER' ? 'badge-starter' : 'badge-super') }}" style="font-size: 0.8rem;">
                {{ $user['tier'] === 'FREE' ? 'Member Biasa' : $user['tier'] }}
            </span>
        </div>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Selamat datang, <strong>{{ $user['name'] }}</strong> ({{ $user['email'] }}). Pantau penggunaan token dan gunakan API key Anda di bawah ini.
        </p>
    </div>

    <div>
        <button type="button" class="btn btn-primary" onclick="openTierModal()">
            Ubah / Upgrade Status Member
        </button>
    </div>
</div>

<!-- Overview Stats Cards -->
@php
    $limit = max(1, $user['token_limit']);
    $used = $user['tokens_used'];
    $percentage = min(100, round(($used / $limit) * 100, 1));
    $remaining = max(0, $limit - $used);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <!-- Card 1: Tier Status -->
    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.5rem;">
                Status Akun &amp; Langganan
            </div>

            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.6rem;">
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--text-heading);">
                    {{ $tierConfig['display_name'] ?? $user['tier'] }}
                </span>
            </div>

            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem;">
                {{ $tierConfig['description'] ?? 'Status tier pengguna aktif.' }}
            </p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--border-light); font-size: 0.85rem;">
            <span style="color: var(--text-muted);">Akses Model:</span>
            <span style="font-weight: 700; color: var(--text-heading);">{{ count($tierConfig['allowed_models'] ?? []) }} Model Terbuka</span>
        </div>
    </div>

    <!-- Card 2: Token Quota -->
    <div class="card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
                    Alokasi &amp; Pemakaian Token
                </span>
                <span style="font-size: 0.85rem; font-weight: 800; color: {{ $percentage > 85 ? '#dc2626' : 'var(--primary)' }};">
                    {{ $percentage }}% Terpakai
                </span>
            </div>

            <div class="progress-track" style="height: 12px;">
                <div class="progress-fill" style="width: {{ $percentage }}%; background-color: {{ $percentage > 85 ? '#dc2626' : 'var(--primary)' }};"></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 0.75rem;">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Token Terpakai</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-heading);">
                        {{ number_format($used, 0, ',', '.') }}
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Batas Limit Maksimal</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">
                        {{ number_format($limit, 0, ',', '.') }} Token
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--border-light); font-size: 0.85rem;">
            <span style="color: var(--text-muted);">Sisa Kuota:</span>
            <span style="font-weight: 700; color: {{ $remaining < 50000 ? '#dc2626' : '#059669' }};">
                {{ number_format($remaining, 0, ',', '.') }} Token
            </span>
        </div>
    </div>
</div>

<!-- API Key Credentials Section -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Kunci Otentikasi API Gateway Anda</h2>
        <span class="badge badge-success">Status: Aktif</span>
    </div>

    <div style="margin-bottom: 1.25rem;">
        <label for="apiKeyInput" class="form-label">API Secret Key (Bearer Token)</label>
        <div style="display: flex; gap: 0.5rem; align-items: center; max-width: 680px; flex-wrap: wrap;">
            <input type="password" id="apiKeyInput" class="form-control" value="{{ $user['api_key'] }}" readonly style="font-family: var(--font-mono); background: var(--bg-muted); letter-spacing: 0.05em; font-size: 0.95rem; flex: 1; min-width: 250px;">
            <button type="button" class="btn btn-secondary" onclick="toggleKeyVisibility()" id="toggleKeyBtn">
                Lihat
            </button>
            <button type="button" class="btn btn-primary" onclick="copyApiKey()" id="copyKeyBtn">
                Salin Kunci
            </button>
        </div>
        <div class="form-hint">
            Kunci ini digunakan untuk mengakses endpoint proxy: <code style="font-family: var(--font-mono);">{{ $expressUrl }}/v1/chat/completions</code>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border-light); flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-heading);">Perlu mengganti API Key?</div>
            <div style="font-size: 0.825rem; color: var(--text-muted);">Jika kunci bocor atau ingin di-reset, buat kunci baru dan kunci lama langsung dinonaktifkan.</div>
        </div>

        <form action="{{ route('dashboard.regenerate-key') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membuat API Key baru? Kunci lama Anda tidak akan dapat digunakan lagi.');">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="color:#b91c1c; border-color:#fecaca;">
                Regenerate Kunci Baru
            </button>
        </form>
    </div>
</div>

<!-- Interactive Live AI Console (Real-time Streaming Playground) -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Konsol Pengujian Interaktif (Live AI Playground)</h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                Uji langsung model AI yang didukung tier Anda dengan streaming real-time
            </div>
        </div>
        <span class="badge badge-success">Gateway Port 5000</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; align-items: start;">
        <!-- Left: Input Control -->
        <div>
            <div class="form-group">
                <label for="consoleModel" class="form-label">Model AI Sesuai Tier {{ $user['tier'] }}</label>
                <select id="consoleModel" class="form-control" style="font-family: var(--font-mono); font-size: 0.9rem;">
                    @foreach($tierConfig['allowed_models'] ?? [] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
                <div class="form-hint">
                    Daftar model dibatasi oleh status keanggotaan aktif Anda.
                </div>
            </div>

            <div class="form-group">
                <label for="consolePrompt" class="form-label">Pesan / Prompt Pengguna</label>
                <textarea id="consolePrompt" class="form-control" rows="5" placeholder="Ketik pertanyaan atau tugas di sini...">Halo! Berikan 3 poin penting dalam merancang API gateway yang skalabel dan aman.</textarea>
            </div>

            <!-- Quick Template Prompt Pills -->
            <div style="margin-bottom: 1.25rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem; text-transform: uppercase;">
                    Contoh Prompt Cepat:
                </div>
                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setPrompt('Jelaskan perbedaan REST API dan GraphQL dalam 2 paragraf padat.')">
                        REST vs GraphQL
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setPrompt('Buatkan fungsi JavaScript untuk menghitung token estimasi dari teks.')">
                        Kode Tokenizer
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="setPrompt('Halo! Tes respon kilat 1 kalimat.')">
                        Ping Singkat
                    </button>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="button" id="sendBtn" class="btn btn-primary" onclick="sendLivePrompt()" style="flex: 1;">
                    Kirim Request (Stream)
                </button>
                <button type="button" class="btn btn-secondary" onclick="clearConsole()">
                    Bersihkan
                </button>
            </div>
        </div>

        <!-- Right: Output Stream Box -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                <span class="form-label" style="margin-bottom: 0;">Output Respon Streaming AI</span>
                <span id="streamStatus" style="font-weight: 600; font-size: 0.8rem; color: var(--text-muted);">Siap</span>
            </div>

            <div id="consoleOutput" class="code-snippet" style="min-height: 260px; max-height: 380px; overflow-y: auto; white-space: pre-wrap; font-size: 0.875rem;">Menunggu request... Klik tombol "Kirim Request (Stream)" untuk memulai panggilan ke model AI.</div>
        </div>
    </div>
</div>

<!-- Recent Usage Logs -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Riwayat Aktivitas Pemakaian Token</h2>
        <span class="brand-badge">Audit Log</span>
    </div>

    @if(count($recentLogs) > 0)
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu Request</th>
                        <th>Model AI Digunakan</th>
                        <th>Prompt Tokens</th>
                        <th>Completion Tokens</th>
                        <th>Total Tokens</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $log['created_at'] }}</td>
                            <td><code style="font-family: var(--font-mono); font-size: 0.85rem;">{{ $log['model'] }}</code></td>
                            <td>{{ number_format($log['prompt_tokens'], 0, ',', '.') }}</td>
                            <td>{{ number_format($log['completion_tokens'], 0, ',', '.') }}</td>
                            <td><strong style="color: var(--primary);">{{ number_format($log['total_tokens'], 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
            <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.3rem;">Belum ada catatan pemakaian</div>
            <p style="font-size: 0.9rem;">Lakukan tes di konsol playground interaktif di atas untuk mencatat pemakaian pertama Anda.</p>
        </div>
    @endif
</div>

<!-- Modal: Upgrade / Change Member Tier -->
<div id="tierModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 640px;">
        <div class="modal-head">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-heading);">
                Pilih Status Keanggotaan &amp; Kuota
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeTierModal()">&times;</button>
        </div>

        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
            Pilih status tier baru. Sistem akan langsung menyesuaikan batas limit token dan daftar model AI yang dapat Anda akses:
        </p>

        <form action="{{ route('dashboard.upgrade-tier') }}" method="POST">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.75rem;">
                @foreach($allTiers as $t)
                    @php $isCurrent = ($user['tier'] === $t['tier_name']); @endphp
                    <label style="border: 2px solid {{ $isCurrent ? 'var(--primary)' : 'var(--border-light)' }}; background: {{ $isCurrent ? 'var(--primary-light)' : 'var(--bg-surface)' }}; border-radius: var(--radius-sm); padding: 1.1rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.85rem; transition: all 0.15s ease;">
                        <input type="radio" name="target_tier" value="{{ $t['tier_name'] }}" {{ $isCurrent ? 'checked' : '' }} style="margin-top: 0.3rem;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                                <strong style="font-size: 1rem; color: var(--text-heading);">{{ $t['display_name'] }}</strong>
                                <span style="font-weight: 800; color: var(--primary); font-size: 0.95rem;">
                                    {{ number_format($t['default_token_limit'], 0, ',', '.') }} Token
                                </span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; line-height: 1.45;">
                                {{ $t['description'] }}
                            </div>
                            <div style="font-size: 0.775rem; color: var(--text-muted);">
                                Model yang didukung: <code style="font-family:var(--font-mono); font-size:0.75rem;">{{ implode(', ', $t['allowed_models']) }}</code>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeTierModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Status Member</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const USER_API_KEY = "{{ $user['api_key'] }}";
const EXPRESS_BASE_URL = "{{ $expressUrl }}";

function toggleKeyVisibility() {
    const input = document.getElementById('apiKeyInput');
    const btn = document.getElementById('toggleKeyBtn');
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerText = 'Sembunyikan';
    } else {
        input.type = 'password';
        btn.innerText = 'Lihat';
    }
}

function copyApiKey() {
    const input = document.getElementById('apiKeyInput');
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('copyKeyBtn');
        const origText = btn.innerText;
        btn.innerText = 'Tersalin!';
        setTimeout(() => { btn.innerText = origText; }, 2000);
    }).catch(err => {
        alert('Gagal menyalin key: ' + err);
    });
}

function openTierModal() {
    document.getElementById('tierModal').classList.add('active');
}

function closeTierModal() {
    document.getElementById('tierModal').classList.remove('active');
}

function setPrompt(text) {
    document.getElementById('consolePrompt').value = text;
}

function clearConsole() {
    document.getElementById('consoleOutput').innerText = 'Menunggu request... Klik tombol "Kirim Request (Stream)" untuk memulai.';
    document.getElementById('streamStatus').innerText = 'Siap';
    document.getElementById('streamStatus').style.color = 'var(--text-muted)';
}

async function sendLivePrompt() {
    const model = document.getElementById('consoleModel').value;
    const prompt = document.getElementById('consolePrompt').value.trim();
    const outputBox = document.getElementById('consoleOutput');
    const statusLabel = document.getElementById('streamStatus');
    const sendBtn = document.getElementById('sendBtn');

    if (!prompt) {
        alert('Teks prompt tidak boleh kosong.');
        return;
    }

    outputBox.innerText = '';
    statusLabel.innerText = 'Menghubungkan ke gateway port 5000...';
    statusLabel.style.color = 'var(--primary)';
    sendBtn.disabled = true;
    sendBtn.innerText = 'Streaming...';

    try {
        const response = await fetch(`${EXPRESS_BASE_URL}/v1/chat/completions`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${USER_API_KEY}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model: model,
                messages: [{ role: 'user', content: prompt }],
                stream: true
            })
        });

        if (!response.ok) {
            let errorText = await response.text();
            try {
                const errObj = JSON.parse(errorText);
                errorText = errObj.error?.message || errorText;
            } catch(e) {}
            outputBox.innerText = `[Error ${response.status}]: ${errorText}`;
            statusLabel.innerText = `Gagal (${response.status})`;
            statusLabel.style.color = '#dc2626';
            sendBtn.disabled = false;
            sendBtn.innerText = 'Kirim Request (Stream)';
            return;
        }

        statusLabel.innerText = 'Menerima streaming...';
        const reader = response.body.getReader();
        const decoder = new TextDecoder('utf-8');
        let done = false;
        let accumulated = '';

        while (!done) {
            const { value, done: readerDone } = await reader.read();
            done = readerDone;
            if (value) {
                const chunk = decoder.decode(value, { stream: true });
                const lines = chunk.split('\n');
                for (const line of lines) {
                    const trimmed = line.trim();
                    if (trimmed.startsWith('data: ') && trimmed !== 'data: [DONE]') {
                        try {
                            const parsed = JSON.parse(trimmed.slice(6));
                            const text = parsed.choices?.[0]?.delta?.content || '';
                            if (text) {
                                accumulated += text;
                                outputBox.innerText = accumulated;
                                outputBox.scrollTop = outputBox.scrollHeight;
                            }
                        } catch(e) {}
                    }
                }
            }
        }

        statusLabel.innerText = 'Selesai (200 OK)';
        statusLabel.style.color = '#059669';
    } catch (err) {
        outputBox.innerText = `[Koneksi Terputus]: ${err.message}\nPastikan backend Express berjalan di port 5000.`;
        statusLabel.innerText = 'Koneksi gagal';
        statusLabel.style.color = '#dc2626';
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerText = 'Kirim Request (Stream)';
    }
}
</script>
@endsection
