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
 * Submissão de Pedidos (Cliente)
 * ================================ */
const orderForm = document.getElementById('orderForm');
if (orderForm) {
  orderForm.addEventListener('submit', async e => {
    e.preventDefault();
    const f = new FormData(orderForm);
    const params = new URLSearchParams();
    params.append('name',     f.get('name'));
    params.append('company',  f.get('company'));
    params.append('email',    f.get('email'));
    params.append('phone',    f.get('phone'));
    params.append('vessel',   f.get('vessel'));
    params.append('port',     f.get('port'));
    params.append('date',     f.get('date'));
    f.getAll('services[]').forEach(s => params.append('services[]', s));
    params.append('notes',    f.get('notes') || '');

    const response = await fetch(API_BASE + '/submit_request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    });

    let json;
    try {
      json = await response.json();
    } catch {
      alert('Resposta inesperada do servidor.');
      return;
    }

    if (response.ok && json.message) {
      alert(json.message);
      orderForm.reset();
      loadOrderHistory();
    } else {
      alert(json.error || 'Erro ao submeter pedido');
    }
  });
}

/** ================================
 * Histórico de pedidos (Cliente)
 * ================================ */
async function loadOrderHistory() {
  const orders = await api('/get_requests.php');

  // DEBUG: mostra o JSON cru no <pre id="debugOutput">
  const dbg = document.getElementById('debugOutput');
  if (dbg) dbg.textContent = JSON.stringify(orders, null, 2);

  const tbody  = document.querySelector('#historyTable tbody');
  if (!tbody || !Array.isArray(orders)) return;
  tbody.innerHTML = '';

  orders.forEach(o => {
    const d = o.details || o.detalhes || {};

    // Extrai serviços
    let servicesText = '';
    if (Array.isArray(d.services)) {
      servicesText = d.services.join(', ');
    } else if (Array.isArray(d.servicos)) {
      servicesText = d.servicos.join(', ');
    } else if (typeof d.services === 'string') {
      servicesText = d.services;
    } else if (typeof d.servicos === 'string') {
      servicesText = d.servicos;
    }

    // Extrai status
    const statusText = o.status || '';

    // Link de fatura
    const invoiceLink = `<a href="view_invoice.php?request_id=${o.id}">Ver Fatura</a>`;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(o.created_at).toLocaleString()}</td>
      <td>${servicesText}</td>
      <td>${statusText}</td>
      <td>${invoiceLink}</td>
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
  const json = await api('/get_all_requests.php');
  const tbody = document.querySelector('#adminTable tbody');
  if (!tbody || !Array.isArray(json)) return;
  tbody.innerHTML = '';

  json.forEach(r => {
    const d = r.details || r.detalhes || {};
    let servicesText = '';
    if (Array.isArray(d.services)) {
      servicesText = d.services.join(', ');
    } else if (Array.isArray(d.servicos)) {
      servicesText = d.servicos.join(', ');
    } else if (typeof d.services === 'string') {
      servicesText = d.services;
    } else if (typeof d.servicos === 'string') {
      servicesText = d.servicos;
    }

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.id}</td>
      <td>${r.user_name} (${r.email})</td>
      <td>${servicesText}</td>
      <td>${r.status}</td>
      <td>${new Date(r.created_at).toLocaleString()}</td>
      <td>
        <select data-id="${r.id}" class="status-select">
          <option value="pendente" ${r.status==='pendente'?'selected':''}>Pendente</option>
          <option value="em progresso" ${r.status==='em progresso'?'selected':''}>Em Progresso</option>
          <option value="concluido" ${r.status==='concluido'?'selected':''}>Concluído</option>
        </select>
      </td>
    `;
    tbody.appendChild(tr);
  });

  document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id     = sel.dataset.id;
      const status = sel.value;
      const res    = await api('/update_status.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id, status })
      });
      if (!res.message) alert(res.error || 'Erro ao atualizar status');
    });
  });
}

if (document.getElementById('adminTable')) {
  loadAllRequests();
}
