// public/script.js

const API_BASE = '/backend'; // <<— o teu backend local

async function api(path, opts = {}) {
  const headers = { 'Content-Type': 'application/json' };
  const res = await fetch(`${API_BASE}${path}`, {
    ...opts,
    headers,
    body: opts.body ? JSON.stringify(opts.body) : undefined
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(text || 'Erro na API');
  }
  return res.json();
}

// — LOGIN —
async function login(email, password) {
  const data = await api('/login.php', {
    method: 'POST',
    body: { email, password }
  });
  alert(data.message || 'Login efetuado');
  window.location.href = 'index.php';
}

// — REGISTO —
async function register(name, email, password) {
  const data = await api('/register.php', {
    method: 'POST',
    body: { name, email, password }
  });
  alert(data.message || 'Registo efetuado');
  window.location.href = 'login.php';
}

// — LOGOUT —
async function logout() {
  await api('/logout.php', { method: 'GET' });
  window.location.href = 'login.php';
}

// — HANDLERS —
document.addEventListener('DOMContentLoaded', () => {
  const lf = document.getElementById('loginForm');
  if (lf) lf.addEventListener('submit', e => {
    e.preventDefault();
    login(lf.email.value, lf.password.value).catch(err => alert(err.message));
  });

  const rf = document.getElementById('registerForm');
  if (rf) rf.addEventListener('submit', e => {
    e.preventDefault();
    register(rf.name.value, rf.email.value, rf.password.value)
      .catch(err => alert(err.message));
  });

  const ob = document.getElementById('logoutBtn');
  if (ob) ob.addEventListener('click', () => logout().catch(err => alert(err.message)));
});
