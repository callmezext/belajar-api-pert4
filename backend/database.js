const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const bcrypt = require('bcryptjs');

const dbPath = path.join(__dirname, 'data.db');
const db = new sqlite3.Database(dbPath);

function run(sql, params = []) {
  return new Promise((resolve, reject) => {
    db.run(sql, params, function (err) {
      if (err) return reject(err);
      resolve({ id: this.lastID, changes: this.changes });
    });
  });
}

function get(sql, params = []) {
  return new Promise((resolve, reject) => {
    db.get(sql, params, (err, row) => {
      if (err) return reject(err);
      resolve(row);
    });
  });
}

function all(sql, params = []) {
  return new Promise((resolve, reject) => {
    db.all(sql, params, (err, rows) => {
      if (err) return reject(err);
      resolve(rows);
    });
  });
}

async function initDb() {
  // Users table
  await run(`
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      email TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      role TEXT DEFAULT 'user',
      tier TEXT DEFAULT 'FREE',
      token_limit INTEGER DEFAULT 1000000,
      tokens_used INTEGER DEFAULT 0,
      api_key TEXT UNIQUE,
      status TEXT DEFAULT 'active',
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Tier configs table
  await run(`
    CREATE TABLE IF NOT EXISTS tier_configs (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      tier_name TEXT UNIQUE NOT NULL,
      display_name TEXT NOT NULL,
      default_token_limit INTEGER NOT NULL,
      description TEXT,
      allowed_models TEXT
    )
  `);

  // Usage logs table
  await run(`
    CREATE TABLE IF NOT EXISTS usage_logs (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      user_id INTEGER,
      model TEXT,
      prompt_tokens INTEGER,
      completion_tokens INTEGER,
      total_tokens INTEGER,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Seed default tiers
  const defaultTiers = [
    {
      name: 'FREE',
      displayName: 'Member Biasa (Free)',
      limit: 1000000, // 1M tokens
      desc: 'Tier default tanpa status berbayar. Kuota 1,000,000 token.',
      models: JSON.stringify(['ag/gemini-3.7-flash-low', 'ag/gemini-3.6-flash-low'])
    },
    {
      name: 'STARTER',
      displayName: 'STARTER Plan',
      limit: 5000000, // 5M tokens
      desc: 'Paket langganan Starter. Kuota 5,000,000 token dengan akses model Flash lebih cepat.',
      models: JSON.stringify(['ag/gemini-3.8-flash', 'ag/gemini-3.7-flash-medium', 'ag/gemini-3.6-flash-medium', 'ag/gpt-oss-120b-medium'])
    },
    {
      name: 'SUPER',
      displayName: 'SUPER VIP Plan',
      limit: 20000000, // 20M tokens
      desc: 'Paket langganan Super tertinggi. Kuota 20,000,000 token dengan akses Claude Sonnet & High Thinking.',
      models: JSON.stringify(['ag/claude-sonnet-4-6', 'ag/gemini-3.8-flash-high', 'ag/gemini-3.8-flash', 'ag/gemini-3.7-flash-high', 'ag/gemini-3.1-pro-low', 'ag/gpt-oss-120b-medium'])
    }
  ];

  for (const t of defaultTiers) {
    const existing = await get('SELECT id FROM tier_configs WHERE tier_name = ?', [t.name]);
    if (!existing) {
      await run(
        'INSERT INTO tier_configs (tier_name, display_name, default_token_limit, description, allowed_models) VALUES (?, ?, ?, ?, ?)',
        [t.name, t.displayName, t.limit, t.desc, t.models]
      );
    }
  }

  // Seed default admin user & demo regular user
  const adminExists = await get('SELECT id FROM users WHERE email = ?', ['admin@enchant.id']);
  if (!adminExists) {
    const hashed = bcrypt.hashSync('admin123', 10);
    const adminKey = 'sk-admin-' + Math.random().toString(36).substring(2, 12);
    await run(
      `INSERT INTO users (name, email, password, role, tier, token_limit, tokens_used, api_key, status)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      ['Administrator', 'admin@enchant.id', hashed, 'admin', 'SUPER', 100000000, 0, adminKey, 'active']
    );
  }

  const demoUserExists = await get('SELECT id FROM users WHERE email = ?', ['user@enchant.id']);
  if (!demoUserExists) {
    const hashed = bcrypt.hashSync('user123', 10);
    const userKey = 'sk-user-' + Math.random().toString(36).substring(2, 12);
    await run(
      `INSERT INTO users (name, email, password, role, tier, token_limit, tokens_used, api_key, status)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      ['Demo Member', 'user@enchant.id', hashed, 'user', 'FREE', 1000000, 14250, userKey, 'active']
    );
  }
}

module.exports = {
  db,
  run,
  get,
  all,
  initDb
};
