// script.js

const API_BASE = '/backend'; // ajusta se necessário

// Lê JSON e faz fetch, lançando erro se status != 2xx
async function api(path, opts = {}) {
  const headers = { 'Content-Type': 'application/json', ...(opts.headers||{}) };
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

// — LOGOUT (chama endpoint e limpa sessão) —
async function logout() {
  await api('/logout.php', { method: 'GET' });
  window.location.href = 'login.php';
}

// — FORM HANDLERS —
document.addEventListener('DOMContentLoaded', () => {
  // Login
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      login(loginForm.email.value, loginForm.password.value)
        .catch(err => alert(err.message));
    });
  }

  // Registo
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', e => {
      e.preventDefault();
      register(
        registerForm.name.value,
        registerForm.email.value,
        registerForm.password.value
      ).catch(err => alert(err.message));
    });
  }

  // Logout (botão com id="logoutBtn")
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      logout().catch(err => alert(err.message));
    });
  }
});
