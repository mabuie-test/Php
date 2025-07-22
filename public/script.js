// public/script.js

// Base URL para o backend
const API_BASE = '/backend';

// Função genérica para chamadas à API
async function api(path, { method = 'GET', body = null } = {}) {
  const opts = { method };
  opts.headers = { 'Content-Type': 'application/json' };
  if (body) opts.body = JSON.stringify(body);

  const res = await fetch(API_BASE + path, opts);
  const text = await res.text();
  try {
    return JSON.parse(text);
  } catch {
    return text;
  }
}

/** ================================
 * Funções de Autenticação
 * ================================ */

// Registo de cliente
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(registerForm);
    const body = {
      name:     f.get('name'),
      email:    f.get('email'),
      password: f.get('password')
    };
    const json = await api('/register.php', { method: 'POST', body });
    if (json && json.includes('sucesso')) {
      alert('Registo efetuado! Faz login para continuar.');
      window.location.href = 'login.php';
    } else {
      alert(json || 'Erro no registo');
    }
  });
}

// Login de cliente/admin
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(loginForm);
    const body = {
      email:    f.get('email'),
      password: f.get('password')
    };
    const json = await api('/login.php', { method: 'POST', body });
    if (json && json.includes('bem‑sucedido')) {
      // Redireciona para a home
      window.location.href = 'index.php';
    } else {
      alert(json || 'Erro no login');
    }
  });
}

// Logout
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', async () => {
    await api('/logout.php', { method: 'POST' });
    window.location.href = 'login.php';
  });
}

/** ================================
 * Submissão de Pedidos (Cliente)
 * ================================ */

const orderForm = document.getElementById('orderForm');
if (orderForm) {
  orderForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(orderForm);
    const body = {
      service_type: f.get('service_type'),
      description:  f.get('description')
    };
    const json = await api('/submit_request.php', { method: 'POST', body });
    if (json.message) {
      alert(json.message);
      orderForm.reset();
      loadOrderHistory();
    } else {
      alert(json.error || 'Erro ao submeter pedido');
    }
  });
}

// Histórico de pedidos (Cliente)
async function loadOrderHistory() {
  const json = await api('/get_requests.php');
  const tbody = document.querySelector('#historyTable tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  json.forEach(o => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(o.created_at).toLocaleString()}</td>
      <td>${o.service_type}</td>
      <td>${o.description}</td>
      <td>${o.status}</td>
    `;
    tbody.appendChild(tr);
  });
}
if (document.getElementById('historyTable')) {
  loadOrderHistory();
}

/** ================================
 * Área Admin
 * ================================ */

// Carrega todos os pedidos (Admin)
async function loadAllRequests() {
  const json = await api('/get_all_requests.php');
  const tbody = document.querySelector('#adminTable tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  json.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.id}</td>
      <td>${r.user_name} (${r.email})</td>
      <td>${r.service_type}</td>
      <td>${r.description}</td>
      <td>
        <select data-id="${r.id}" class="status-select">
          <option value="pendente" ${r.status==='pendente'?'selected':''}>Pendente</option>
          <option value="em progresso" ${r.status==='em progresso'?'selected':''}>Em Progresso</option>
          <option value="concluido" ${r.status==='concluido'?'selected':''}>Concluído</option>
        </select>
      </td>
      <td>${new Date(r.created_at).toLocaleString()}</td>
    `;
    tbody.appendChild(tr);
  });

  // Adiciona listener aos selects
  document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id = sel.dataset.id;
      const status = sel.value;
      const res = await api('/update_status.php', {
        method: 'POST',
        body: { id, status }
      });
      if (!res.message) {
        alert(res.error || 'Erro ao atualizar status');
      }
    });
  });
}

if (document.getElementById('adminTable')) {
  loadAllRequests();
}
