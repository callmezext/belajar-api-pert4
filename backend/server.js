require('dotenv').config();
const express = require('express');
const cors = require('cors');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');
const { db, run, get, all, initDb } = require('./database');
const { forwardChatCompletion } = require('./upstream');

const app = express();
const PORT = process.env.PORT || 5000;
const JWT_SECRET = process.env.JWT_SECRET || 'enchant_secret_jwt_key_2026';

app.use(cors());
app.use(express.json());

// Initialize DB tables and seed data
initDb().then(() => {
  console.log('[DB] SQLite database initialized and ready.');
}).catch(err => {
  console.error('[DB] Failed to initialize SQLite:', err);
});

// Helper: Generate API key
function generateApiKey() {
  return 'sk-enc-' + crypto.randomBytes(16).toString('hex');
}

// Middleware: Authenticate JWT (for Web Dashboard)
async function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    return res.status(401).json({ error: 'Akses ditolak. Token autentikasi tidak ditemukan.' });
  }

  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    const user = await get('SELECT id, name, email, role, tier, token_limit, tokens_used, api_key, status FROM users WHERE id = ?', [decoded.id]);
    if (!user) {
      return res.status(401).json({ error: 'User tidak ditemukan.' });
    }
    if (user.status !== 'active') {
      return res.status(403).json({ error: 'Akun Anda dinonaktifkan oleh administrator.' });
    }
    req.user = user;
    next();
  } catch (err) {
    return res.status(403).json({ error: 'Sesi kedaluwarsa atau token tidak valid.' });
  }
}

// Middleware: Require Admin Role
function requireAdmin(req, res, next) {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ error: 'Akses khusus Administrator.' });
  }
  next();
}

// ==========================================
// 1. AUTH ROUTES
// ==========================================

app.post('/api/auth/register', async (req, res) => {
  try {
    const { name, email, password } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ error: 'Semua kolom (nama, email, password) wajib diisi.' });
    }

    const existing = await get('SELECT id FROM users WHERE email = ?', [email]);
    if (existing) {
      return res.status(400).json({ error: 'Email sudah terdaftar. Silakan gunakan email lain.' });
    }

    // Default: Member Biasa (Free) 1,000,000 tokens
    const freeTierConfig = await get('SELECT default_token_limit FROM tier_configs WHERE tier_name = "FREE"');
    const defaultLimit = freeTierConfig ? freeTierConfig.default_token_limit : 1000000;

    const hashedPassword = bcrypt.hashSync(password, 10);
    const apiKey = generateApiKey();

    const result = await run(
      `INSERT INTO users (name, email, password, role, tier, token_limit, tokens_used, api_key, status)
       VALUES (?, ?, ?, 'user', 'FREE', ?, 0, ?, 'active')`,
      [name, email, hashedPassword, defaultLimit, apiKey]
    );

    const token = jwt.sign({ id: result.id, email, role: 'user' }, JWT_SECRET, { expiresIn: '7d' });

    res.status(201).json({
      message: 'Registrasi berhasil.',
      token,
      user: {
        id: result.id,
        name,
        email,
        role: 'user',
        tier: 'FREE',
        token_limit: defaultLimit,
        tokens_used: 0,
        api_key: apiKey
      }
    });
  } catch (err) {
    res.status(500).json({ error: 'Terjadi kesalahan server saat registrasi: ' + err.message });
  }
});

app.post('/api/auth/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    if (!email || !password) {
      return res.status(400).json({ error: 'Email dan password wajib diisi.' });
    }

    const user = await get('SELECT * FROM users WHERE email = ?', [email]);
    if (!user) {
      return res.status(401).json({ error: 'Email atau password salah.' });
    }

    const validPass = bcrypt.compareSync(password, user.password);
    if (!validPass) {
      return res.status(401).json({ error: 'Email atau password salah.' });
    }

    if (user.status !== 'active') {
      return res.status(403).json({ error: 'Akun Anda dinonaktifkan. Hubungi administrator.' });
    }

    const token = jwt.sign({ id: user.id, email: user.email, role: user.role }, JWT_SECRET, { expiresIn: '7d' });

    res.json({
      message: 'Login berhasil.',
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        tier: user.tier,
        token_limit: user.token_limit,
        tokens_used: user.tokens_used,
        api_key: user.api_key
      }
    });
  } catch (err) {
    res.status(500).json({ error: 'Terjadi kesalahan server saat login: ' + err.message });
  }
});

app.get('/api/auth/me', authenticateToken, (req, res) => {
  res.json({ user: req.user });
});

// ==========================================
// 2. USER DASHBOARD ROUTES
// ==========================================

app.get('/api/user/overview', authenticateToken, async (req, res) => {
  try {
    const user = await get('SELECT id, name, email, role, tier, token_limit, tokens_used, api_key, status, created_at FROM users WHERE id = ?', [req.user.id]);
    const tierConfig = await get('SELECT * FROM tier_configs WHERE tier_name = ?', [user.tier]);
    const recentLogs = await all('SELECT model, prompt_tokens, completion_tokens, total_tokens, created_at FROM usage_logs WHERE user_id = ? ORDER BY id DESC LIMIT 10', [user.id]);
    const allTiers = await all('SELECT tier_name, display_name, default_token_limit, description, allowed_models FROM tier_configs');

    res.json({
      user,
      tierConfig: {
        ...tierConfig,
        allowed_models: tierConfig ? JSON.parse(tierConfig.allowed_models || '[]') : []
      },
      allTiers: allTiers.map(t => ({
        ...t,
        allowed_models: JSON.parse(t.allowed_models || '[]')
      })),
      recentLogs
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// User: Regenerate API Key
app.post('/api/user/regenerate-key', authenticateToken, async (req, res) => {
  try {
    const newKey = generateApiKey();
    await run('UPDATE users SET api_key = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [newKey, req.user.id]);
    res.json({ message: 'API Key baru berhasil dibuat.', api_key: newKey });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// User: Upgrade / Change Member Tier
app.post('/api/user/upgrade-tier', authenticateToken, async (req, res) => {
  try {
    const { target_tier } = req.body;
    if (!target_tier) {
      return res.status(400).json({ error: 'Pilih target status tier.' });
    }

    const tierConfig = await get('SELECT * FROM tier_configs WHERE tier_name = ?', [target_tier]);
    if (!tierConfig) {
      return res.status(400).json({ error: 'Tier tidak valid.' });
    }

    // Update user tier and adjust token limit according to the selected tier
    await run(
      'UPDATE users SET tier = ?, token_limit = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
      [target_tier, tierConfig.default_token_limit, req.user.id]
    );

    res.json({
      message: `Status member berhasil diperbarui menjadi ${tierConfig.display_name}. Kuota Anda kini ${tierConfig.default_token_limit.toLocaleString()} token.`,
      new_tier: target_tier,
      new_limit: tierConfig.default_token_limit
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// ==========================================
// 3. ADMIN DASHBOARD ROUTES (CRUD)
// ==========================================

// Admin: System Stats
app.get('/api/admin/stats', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const totalUsers = await get('SELECT COUNT(*) as count FROM users');
    const totalTokensUsed = await get('SELECT SUM(tokens_used) as total FROM users');
    const tierCounts = await all('SELECT tier, COUNT(*) as count FROM users GROUP BY tier');
    const recentRequests = await all(`
      SELECT ul.*, u.name as user_name, u.email as user_email
      FROM usage_logs ul
      JOIN users u ON ul.user_id = u.id
      ORDER BY ul.id DESC LIMIT 15
    `);

    res.json({
      totalUsers: totalUsers.count,
      totalTokensUsed: totalTokensUsed.total || 0,
      tierBreakdown: tierCounts,
      recentRequests
    });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin: List all users
app.get('/api/admin/users', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const users = await all(`
      SELECT id, name, email, role, tier, token_limit, tokens_used, api_key, status, created_at, updated_at
      FROM users
      ORDER BY id DESC
    `);
    const tiers = await all('SELECT * FROM tier_configs');
    res.json({ users, tiers });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin: Create User
app.post('/api/admin/users', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const { name, email, password, role, tier, token_limit } = req.body;
    if (!name || !email || !password) {
      return res.status(400).json({ error: 'Nama, email, dan password wajib diisi.' });
    }

    const existing = await get('SELECT id FROM users WHERE email = ?', [email]);
    if (existing) {
      return res.status(400).json({ error: 'Email sudah terdaftar.' });
    }

    const selectedTier = tier || 'FREE';
    let limit = token_limit;
    if (!limit) {
      const tc = await get('SELECT default_token_limit FROM tier_configs WHERE tier_name = ?', [selectedTier]);
      limit = tc ? tc.default_token_limit : 1000000;
    }

    const hashedPassword = bcrypt.hashSync(password, 10);
    const apiKey = generateApiKey();

    const result = await run(
      `INSERT INTO users (name, email, password, role, tier, token_limit, tokens_used, api_key, status)
       VALUES (?, ?, ?, ?, ?, ?, 0, ?, 'active')`,
      [name, email, hashedPassword, role || 'user', selectedTier, limit, apiKey]
    );

    res.status(201).json({ message: 'User baru berhasil ditambahkan.', id: result.id });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin: Update User (CRUD detail status member, limit, status)
app.put('/api/admin/users/:id', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const { id } = req.params;
    const { name, email, role, tier, token_limit, status, reset_tokens } = req.body;

    const user = await get('SELECT * FROM users WHERE id = ?', [id]);
    if (!user) {
      return res.status(404).json({ error: 'User tidak ditemukan.' });
    }

    const newName = name || user.name;
    const newEmail = email || user.email;
    const newRole = role || user.role;
    const newTier = tier || user.tier;
    const newLimit = token_limit !== undefined ? parseInt(token_limit, 10) : user.token_limit;
    const newStatus = status || user.status;
    const newUsed = reset_tokens ? 0 : user.tokens_used;

    await run(
      `UPDATE users
       SET name = ?, email = ?, role = ?, tier = ?, token_limit = ?, tokens_used = ?, status = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`,
      [newName, newEmail, newRole, newTier, newLimit, newUsed, newStatus, id]
    );

    res.json({ message: 'Data user berhasil diperbarui.' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin: Delete User
app.delete('/api/admin/users/:id', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const { id } = req.params;
    if (parseInt(id, 10) === req.user.id) {
      return res.status(400).json({ error: 'Anda tidak dapat menghapus akun Anda sendiri.' });
    }

    await run('DELETE FROM users WHERE id = ?', [id]);
    await run('DELETE FROM usage_logs WHERE user_id = ?', [id]);

    res.json({ message: 'User berhasil dihapus.' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Admin: Tier Config Management
app.put('/api/admin/tiers/:tier_name', authenticateToken, requireAdmin, async (req, res) => {
  try {
    const { tier_name } = req.params;
    const { display_name, default_token_limit, description, allowed_models } = req.body;

    const existing = await get('SELECT * FROM tier_configs WHERE tier_name = ?', [tier_name]);
    if (!existing) {
      return res.status(404).json({ error: 'Tier tidak ditemukan.' });
    }

    const modelsJson = Array.isArray(allowed_models) ? JSON.stringify(allowed_models) : (allowed_models || existing.allowed_models);

    await run(
      `UPDATE tier_configs
       SET display_name = ?, default_token_limit = ?, description = ?, allowed_models = ?
       WHERE tier_name = ?`,
      [
        display_name || existing.display_name,
        default_token_limit !== undefined ? parseInt(default_token_limit, 10) : existing.default_token_limit,
        description || existing.description,
        modelsJson,
        tier_name
      ]
    );

    res.json({ message: `Konfigurasi tier ${tier_name} berhasil disimpan.` });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// ==========================================
// 4. OPENAI-COMPATIBLE AI GATEWAY PROXY
// ==========================================

// Authenticate via API Key for /v1 calls
async function authenticateApiKey(req, res, next) {
  const authHeader = req.headers['authorization'];
  const apiKey = authHeader && authHeader.split(' ')[1];

  if (!apiKey) {
    return res.status(401).json({
      error: {
        message: 'Kunci otorisasi tidak ditemukan. Sertakan header: Authorization: Bearer <api_key>',
        type: 'invalid_request_error',
        code: 'unauthorized'
      }
    });
  }

  const user = await get('SELECT * FROM users WHERE api_key = ?', [apiKey]);
  if (!user) {
    return res.status(401).json({
      error: {
        message: 'API Key tidak valid atau telah dicabut.',
        type: 'invalid_request_error',
        code: 'invalid_api_key'
      }
    });
  }

  if (user.status !== 'active') {
    return res.status(403).json({
      error: {
        message: 'Akun Anda berstatus nonaktif. Silakan hubungi administrator.',
        type: 'account_deactivated',
        code: 'forbidden'
      }
    });
  }

  if (user.tokens_used >= user.token_limit) {
    return res.status(429).json({
      error: {
        message: `Batas kuota token (${user.token_limit.toLocaleString()} token) untuk tier ${user.tier} telah habis. Silakan upgrade ke STARTER atau SUPER untuk menambah kuota.`,
        type: 'insufficient_quota',
        code: 'quota_exceeded'
      }
    });
  }

  req.apiUser = user;
  next();
}

// GET /v1/models
app.get('/v1/models', authenticateApiKey, async (req, res) => {
  try {
    const tierConfig = await get('SELECT allowed_models FROM tier_configs WHERE tier_name = ?', [req.apiUser.tier]);
    const allowed = tierConfig ? JSON.parse(tierConfig.allowed_models || '[]') : [];

    const modelsData = allowed.map(m => ({
      id: m,
      object: 'model',
      created: 1790576000,
      owned_by: 'enchant-gateway'
    }));

    res.json({
      object: 'list',
      data: modelsData
    });
  } catch (err) {
    res.status(500).json({ error: { message: err.message } });
  }
});

// POST /v1/chat/completions (Proxy to upstream AI router)
app.post('/v1/chat/completions', authenticateApiKey, async (req, res) => {
  const user = req.apiUser;
  const requestedModel = req.body.model || 'ag/gemini-3.7-flash-medium';

  // Check tier allowed models
  const tierConfig = await get('SELECT allowed_models FROM tier_configs WHERE tier_name = ?', [user.tier]);
  const allowed = tierConfig ? JSON.parse(tierConfig.allowed_models || '[]') : [];

  if (allowed.length > 0 && !allowed.includes(requestedModel)) {
    return res.status(403).json({
      error: {
        message: `Model '${requestedModel}' tidak tersedia untuk tier ${user.tier}. Model yang diizinkan: ${allowed.join(', ')}. Upgrade tier Anda untuk membuka model ini.`,
        type: 'model_not_allowed',
        code: 'tier_restricted'
      }
    });
  }

  // Set streaming headers
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');

  forwardChatCompletion(
    req.body,
    (chunk) => {
      res.write(chunk);
    },
    async ({ totalTokens }) => {
      res.end();
      // Record usage and update user
      try {
        await run('UPDATE users SET tokens_used = tokens_used + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [totalTokens, user.id]);
        await run(
          'INSERT INTO usage_logs (user_id, model, prompt_tokens, completion_tokens, total_tokens) VALUES (?, ?, ?, ?, ?)',
          [user.id, requestedModel, Math.round(totalTokens * 0.4), Math.round(totalTokens * 0.6), totalTokens]
        );
      } catch (e) {
        console.error('[USAGE UPDATE ERROR]', e);
      }
    },
    (err) => {
      console.error('[UPSTREAM ERROR]', err);
      if (!res.headersSent) {
        res.status(502).json({
          error: {
            message: 'Gagal menghubungi upstream server AI: ' + err.message,
            type: 'bad_gateway',
            code: 'upstream_failed'
          }
        });
      } else {
        res.end();
      }
    }
  );
});

// Health check
app.get('/health', (req, res) => {
  res.json({ status: 'ok', service: 'AI Provider Gateway & Express API', timestamp: new Date() });
});

app.listen(PORT, () => {
  console.log(`[SERVER] Express AI Gateway running on port ${PORT}`);
});
