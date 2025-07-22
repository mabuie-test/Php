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
    f.getAll('services').forEach(s => params.append('services[]', s));
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
  // debugOutput (já inserido no HTML) mostrará o JSON cru
  const dbg = document.getElementById('debugOutput');
  if (dbg) dbg.textContent = JSON.stringify(orders, null, 2);

  const tbody  = document.querySelector('#historyTable tbody');
  if (!tbody || !Array.isArray(orders)) return;
  tbody.innerHTML = '';

  orders.forEach(o => {
    const d = o.details || o.detalhes || {};

    // Monta string de serviços
    let servicesText = '';
    if (Array.isArray(d.services)) {
      servicesText = d.services.join(', ');
    }

    // Status
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
