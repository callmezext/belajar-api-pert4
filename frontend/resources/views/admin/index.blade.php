@extends('layouts.app')

@section('title', 'Panel Administrator - Enchant AI Gateway')

@section('content')
<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.25rem;">
            <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-heading); letter-spacing: -0.02em;">
                Panel Pengelolaan Administrator
            </h1>
            <span class="badge badge-admin">Master Control</span>
        </div>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            CRUD Manajemen status member, kustomisasi limit kuota token, serta konfigurasi standar tier gateway.
        </p>
    </div>

    <div>
        <button type="button" class="btn btn-primary" onclick="openCreateUserModal()">
            + Tambah Member Baru
        </button>
    </div>
</div>

<!-- Stats Metric Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
            Total Member Terdaftar
        </div>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-heading); margin-top: 0.25rem;">
            {{ number_format($stats['totalUsers'] ?? 0, 0, ',', '.') }}
        </div>
        <div class="form-hint">Semua akun member biasa, starter, super, &amp; admin</div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
            Total Token Terkonsumsi
        </div>
        <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary); margin-top: 0.25rem;">
            {{ number_format($stats['totalTokensUsed'] ?? 0, 0, ',', '.') }}
        </div>
        <div class="form-hint">Akumulasi pemakaian token seluruh pengguna</div>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.5rem;">
            Distribusi Status Keanggotaan
        </div>
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            @foreach($stats['tierBreakdown'] ?? [] as $tb)
                <span class="badge {{ $tb['tier'] === 'FREE' ? 'badge-free' : ($tb['tier'] === 'STARTER' ? 'badge-starter' : 'badge-super') }}">
                    {{ $tb['tier'] }}: {{ $tb['count'] }}
                </span>
            @endforeach
        </div>
        <div class="form-hint" style="margin-top: 0.5rem;">Berdasarkan akun aktif</div>
    </div>
</div>

<!-- Member Management CRUD Table -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Daftar Member &amp; Alokasi Kuota (CRUD Pengguna)</h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola status tier, alokasi batas kuota token, dan keaktifan akun masing-masing member
            </div>
        </div>
        <span class="brand-badge">{{ count($users) }} Member</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID &amp; Nama Member</th>
                    <th>Alamat Email</th>
                    <th>Role</th>
                    <th>Status Tier</th>
                    <th>Pemakaian / Batas Limit</th>
                    <th>Status</th>
                    <th>Aksi Pengelolaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    @php
                        $uLimit = max(1, $u['token_limit']);
                        $uUsed = $u['tokens_used'];
                        $uPct = min(100, round(($uUsed / $uLimit) * 100, 1));
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--text-heading);">#{{ $u['id'] }} {{ $u['name'] }}</strong>
                            <div class="form-hint" style="font-family: var(--font-mono); font-size: 0.775rem;">
                                Key: {{ substr($u['api_key'] ?? '', 0, 12) }}...
                            </div>
                        </td>
                        <td>{{ $u['email'] }}</td>
                        <td>
                            <span class="badge {{ $u['role'] === 'admin' ? 'badge-admin' : 'badge-free' }}">
                                {{ $u['role'] }}
                            </span>
                        </td>
                        <td>
                            @if($u['tier'] === 'FREE')
                                <span class="badge badge-free">Member Biasa (1M)</span>
                            @elseif($u['tier'] === 'STARTER')
                                <span class="badge badge-starter">STARTER (5M)</span>
                            @elseif($u['tier'] === 'SUPER')
                                <span class="badge badge-super">SUPER VIP (20M)</span>
                            @else
                                <span class="badge">{{ $u['tier'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 700; font-size: 0.875rem;">
                                {{ number_format($uUsed, 0, ',', '.') }} / {{ number_format($uLimit, 0, ',', '.') }}
                            </div>
                            <div class="progress-track" style="height: 6px; width: 140px; margin: 0.3rem 0;">
                                <div class="progress-fill" style="width: {{ $uPct }}%; background-color: {{ $uPct > 85 ? '#dc2626' : 'var(--primary)' }};"></div>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                Sisa: {{ number_format(max(0, $uLimit - $uUsed), 0, ',', '.') }} token
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $u['status'] === 'active' ? 'badge-success' : 'badge-danger' }}">
                                {{ $u['status'] }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem; align-items: center;">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openEditModalById({{ $u['id'] }})">
                                    Edit Status &amp; Limit
                                </button>

                                @if($u['id'] !== session('user')['id'])
                                    <form action="{{ route('admin.users.destroy', $u['id']) }}" method="POST" onsubmit="return confirm('Hapus member #{{ $u['id'] }} ({{ $u['name'] }}) secara permanen?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem 1rem;">
                            Tidak ada member terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Tier Configurations CRUD -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Standar Konfigurasi Tier Sistem</h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                Atur alokasi kuota token bawaan untuk tiap tingkatan status member
            </div>
        </div>
        <span class="brand-badge">Aturan Global</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode Tier</th>
                    <th>Nama Tampilan</th>
                    <th>Batas Kuota Standar</th>
                    <th>Deskripsi Layanan</th>
                    <th>Aksi Konfigurasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tiers as $t)
                    <tr>
                        <td><code style="font-family: var(--font-mono); font-weight: 700;">{{ $t['tier_name'] }}</code></td>
                        <td><strong>{{ $t['display_name'] }}</strong></td>
                        <td><strong style="color:var(--primary); font-size: 1rem;">{{ number_format($t['default_token_limit'], 0, ',', '.') }} Token</strong></td>
                        <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $t['description'] }}</td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm" onclick='openEditTierModal(@json($t))'>
                                Ubah Standar
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Create New User -->
<div id="createUserModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-heading);">
                Tambah Member Baru
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeCreateUserModal()">&times;</button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="createName" class="form-label">Nama Lengkap</label>
                <input type="text" id="createName" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
            </div>

            <div class="form-group">
                <label for="createEmail" class="form-label">Alamat Email</label>
                <input type="email" id="createEmail" name="email" class="form-control" required placeholder="budi@example.com">
            </div>

            <div class="form-group">
                <label for="createPassword" class="form-label">Kata Sandi Awal</label>
                <input type="password" id="createPassword" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="createRole" class="form-label">Role Akses</label>
                    <select id="createRole" name="role" class="form-control">
                        <option value="user">User Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="createTier" class="form-label">Status Member (Tier)</label>
                    <select id="createTier" name="tier" class="form-control" onchange="onTierSelectChange(this.value)">
                        <option value="FREE">FREE (Member Biasa 1M)</option>
                        <option value="STARTER">STARTER (5M)</option>
                        <option value="SUPER">SUPER (20M)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="createLimit" class="form-label">Kustomisasi Limit Token (Opsional)</label>
                <input type="number" id="createLimit" name="token_limit" class="form-control" placeholder="1000000">
                <div class="form-hint">Jika dikosongkan, kuota akan mengikuti nilai standar tier yang dipilih.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeCreateUserModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Member Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit User (CRUD Detail Status & Limit) -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-heading);">
                Edit Detail Status &amp; Kuota Member
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()">&times;</button>
        </div>

        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="editName" class="form-label">Nama Lengkap</label>
                <input type="text" id="editName" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="editEmail" class="form-label">Alamat Email</label>
                <input type="email" id="editEmail" name="email" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="editRole" class="form-label">Role Akses</label>
                    <select id="editRole" name="role" class="form-control">
                        <option value="user">User Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editTier" class="form-label">Status Member (Tier)</label>
                    <select id="editTier" name="tier" class="form-control" onchange="onEditTierChange(this.value)">
                        <option value="FREE">FREE (Member Biasa 1M)</option>
                        <option value="STARTER">STARTER (5M)</option>
                        <option value="SUPER">SUPER VIP (20M)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="editLimit" class="form-label">Batas Alokasi Token (Limit)</label>
                    <input type="number" id="editLimit" name="token_limit" class="form-control" required min="0">
                    <div class="form-hint">Atur angka kustom bebas</div>
                </div>

                <div class="form-group">
                    <label for="editStatus" class="form-label">Status Keaktifan Akun</label>
                    <select id="editStatus" name="status" class="form-control">
                        <option value="active">Active (Aktif)</option>
                        <option value="suspended">Suspended (Ditangguhkan)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="padding: 0.75rem 1rem; background: var(--bg-muted); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer; color: var(--text-heading);">
                    <input type="checkbox" name="reset_tokens" value="1">
                    <span><strong>Reset pemakaian token</strong> (Kembalikan token terpakai pengguna menjadi 0)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Tier Config -->
<div id="editTierModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-heading);">
                Ubah Standar Konfigurasi Tier
            </h3>
            <button type="button" class="modal-close-btn" onclick="closeEditTierModal()">&times;</button>
        </div>

        <form id="editTierForm" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Kode Tier</label>
                <input type="text" id="tierCode" class="form-control" readonly style="background:var(--bg-muted); font-family:var(--font-mono);">
            </div>

            <div class="form-group">
                <label for="tierDisplayName" class="form-label">Nama Tampilan</label>
                <input type="text" id="tierDisplayName" name="display_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="tierDefaultLimit" class="form-label">Batas Kuota Standar (Tokens)</label>
                <input type="number" id="tierDefaultLimit" name="default_token_limit" class="form-control" required min="1000">
                <div class="form-hint">Jumlah token bawaan untuk pengguna tier ini.</div>
            </div>

            <div class="form-group">
                <label for="tierDesc" class="form-label">Keterangan Singkat</label>
                <input type="text" id="tierDesc" name="description" class="form-control" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeEditTierModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const USERS_LIST = @json($users);

function openCreateUserModal() {
    document.getElementById('createUserModal').classList.add('active');
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').classList.remove('active');
}

function openEditModalById(userId) {
    const user = USERS_LIST.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('editName').value = user.name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editRole').value = user.role;
    document.getElementById('editTier').value = user.tier;
    document.getElementById('editLimit').value = user.token_limit;
    document.getElementById('editStatus').value = user.status;

    const form = document.getElementById('editUserForm');
    form.action = "{{ url('admin/users') }}/" + user.id;

    document.getElementById('editUserModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.remove('active');
}

function openEditTierModal(tier) {
    document.getElementById('tierCode').value = tier.tier_name;
    document.getElementById('tierDisplayName').value = tier.display_name;
    document.getElementById('tierDefaultLimit').value = tier.default_token_limit;
    document.getElementById('tierDesc').value = tier.description;

    const form = document.getElementById('editTierForm');
    form.action = "{{ url('admin/tiers') }}/" + tier.tier_name;

    document.getElementById('editTierModal').classList.add('active');
}

function closeEditTierModal() {
    document.getElementById('editTierModal').classList.remove('active');
}

function onTierSelectChange(tier) {
    const limitInput = document.getElementById('createLimit');
    if (tier === 'FREE') limitInput.value = 1000000;
    else if (tier === 'STARTER') limitInput.value = 5000000;
    else if (tier === 'SUPER') limitInput.value = 20000000;
}

function onEditTierChange(tier) {
    const limitInput = document.getElementById('editLimit');
    if (tier === 'FREE') limitInput.value = 1000000;
    else if (tier === 'STARTER') limitInput.value = 5000000;
    else if (tier === 'SUPER') limitInput.value = 20000000;
}
</script>
@endsection
