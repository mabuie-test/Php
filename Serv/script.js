// public/script.js

// Base URL para o backend
const API_BASE = '/backend';

// Função genérica para chamadas à API
async function api(path, { method = 'GET', body = null, headers = {} } = {}) {
  const opts = { method, headers };
  if (body) opts.body = body;
  const res = await fetch(API_BASE + path, opts);
  const ct  = res.headers.get('Content-Type') || '';
  if (ct.includes('application/json')) return res.json();
  return res.text();
}

/** ================================
 * Registo de Cliente
 * ================================ */
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(registerForm);
    const payload = JSON.stringify({
      name:     f.get('name'),
      email:    f.get('email'),
      password: f.get('password')
    });
    const res = await api('/register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: payload
    });
    if (typeof res === 'string' ? res.includes('sucesso') : res.message) {
      alert('Registo efetuado! Faz login para continuar.');
      window.location.href = 'login.php';
    } else {
      alert(res.error || res || 'Erro no registo');
    }
  });
}

/** ================================
 * Login de Cliente/Admin
 * ================================ */
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(loginForm);
    const payload = JSON.stringify({
      email:    f.get('email'),
      password: f.get('password')
    });
    const res = await api('/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: payload
    });
    if (typeof res === 'string' ? res.includes('bem‑sucedido') : res.message) {
      window.location.href = 'index.php';
    } else {
      alert(res.error || res || 'Erro no login');
    }
  });
}

/** ================================
 * Logout
 * ================================ */
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

    const params = new URLSearchParams();
    params.append('name',    f.get('name'));
    params.append('company', f.get('company'));
    params.append('email',   f.get('email'));
    params.append('phone',   f.get('phone'));
    params.append('vessel',  f.get('vessel'));
    params.append('port',    f.get('port'));
    params.append('date',    f.get('date'));
    f.getAll('services').forEach(s => params.append('services[]', s));
    params.append('notes',   f.get('notes') || '');

    const res = await fetch(API_BASE + '/submit_request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    });
    const json = await res.json();
    if (res.ok && json.message) {
      alert(json.message);
      orderForm.reset();
      loadOrderHistory();
    } else {
      alert(json.error || 'Erro ao submeter pedido');
    }
  });
}

/** ================================
 * Histórico de Pedidos (Cliente)
 * ================================ */
async function loadOrderHistory() {
  const rows = await api('/get_requests.php');
  const tbody = document.querySelector('#historyTable tbody');
  if (!tbody || !Array.isArray(rows)) return;
  tbody.innerHTML = '';

  rows.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(r.created_at).toLocaleString()}</td>
      <td>${r.servicos}</td>
      <td>${r.status}</td>
      <td><a href="view_invoice.php?request_id=${r.id}">Ver Fatura</a></td>
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
async function loadAllRequests() {
  const rows = await api('/get_all_requests.php');
  const tbody = document.querySelector('#adminTable tbody');
  if (!tbody || !Array.isArray(rows)) return;
  tbody.innerHTML = '';

  rows.forEach(r => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.id}</td>
      <td>${r.user_name} (${r.email})</td>
      <td>${r.servicos}</td>
      <td>${r.status}</td>
      <td>${new Date(r.created_at).toLocaleString()}</td>
      <td>
        <select data-id="${r.id}" class="status-select">
          <option value="pendente"  ${r.status==='pendente'?'selected':''}>Pendente</option>
          <option value="em progresso" ${r.status==='em progresso'?'selected':''}>Em Progresso</option>
          <option value="concluido"  ${r.status==='concluido'?'selected':''}>Concluído</option>
        </select>
      </td>
    `;
    tbody.appendChild(tr);
  });

  document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const res = await api('/update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: sel.dataset.id, status: sel.value })
      });
      if (!res.message) alert(res.error || 'Erro ao atualizar status');
    });
  });
}
if (document.getElementById('adminTable')) {
  loadAllRequests();
}
