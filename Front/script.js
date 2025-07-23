// script.js

const API_BASE = '/backend'; // ajusta se necessário

// Helper para chamadas à API
async function api(path, opts = {}) {
  const headers = opts.headers || {};
  const token = localStorage.getItem('phil_token');
  if (token) headers['Authorization'] = 'Bearer ' + token;
  headers['Content-Type'] = 'application/json';

  const res = await fetch(`${API_BASE}${path}`, {
    ...opts,
    headers
  });

  if (!res.ok) {
    const text = await res.text();
    throw new Error(text || 'Erro na API');
  }
  return res.json();
}

// ------ Autenticação ------

// Login
async function login(email, password) {
  const data = await api('/login.php', {
    method: 'POST',
    body: JSON.stringify({ email, password })
  });
  alert(data.message || 'Login efetuado');
  window.location.href = 'index.php';
}

// Registro
async function register(name, email, password) {
  const data = await api('/register.php', {
    method: 'POST',
    body: JSON.stringify({ name, email, password })
  });
  alert(data.message || 'Registo efetuado');
  window.location.href = 'login.php';
}

// Logout
async function logout() {
  await api('/logout.php', { method: 'GET' });
  localStorage.removeItem('phil_token');
  window.location.href = 'login.php';
}

// ------ Pedidos de Serviço ------

// Submeter novo pedido
async function submitRequest(serviceType, description) {
  const data = await api('/submit_request.php', {
    method: 'POST',
    body: JSON.stringify({ service_type: serviceType, description })
  });
  alert(data.message || 'Pedido submetido');
  loadOrderHistory();
}

// Carregar histórico de pedidos do cliente
async function loadOrderHistory() {
  const requests = await api('/get_requests.php', { method: 'GET' });
  const table = document.getElementById('historyTable');
  table.innerHTML = `
    <tr>
      <th>ID</th>
      <th>Serviço</th>
      <th>Descrição</th>
      <th>Status</th>
      <th>Data</th>
    </tr>
  `;
  requests.forEach(r => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${r.id}</td>
      <td>${r.service_type}</td>
      <td>${r.description}</td>
      <td>${r.status}</td>
      <td>${new Date(r.created_at).toLocaleString('pt-PT')}</td>
    `;
    table.appendChild(row);
  });
}

// ------ Administração ------

// Carregar todos os pedidos (admin)
async function loadAllRequests() {
  const requests = await api('/get_all_requests.php', { method: 'GET' });
  const table = document.getElementById('adminTable');
  table.innerHTML = `
    <tr>
      <th>ID</th>
      <th>Cliente</th>
      <th>Email</th>
      <th>Serviço</th>
      <th>Descrição</th>
      <th>Status</th>
      <th>Data</th>
      <th>Ações</th>
    </tr>
  `;
  requests.forEach(r => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${r.id}</td>
      <td>${r.user_name}</td>
      <td>${r.email}</td>
      <td>${r.service_type}</td>
      <td>${r.description}</td>
      <td>${r.status}</td>
      <td>${new Date(r.created_at).toLocaleString('pt-PT')}</td>
      <td>
        <select onchange="updateStatus(${r.id}, this.value)">
          <option value="pendente"${r.status==='pendente'?' selected':''}>Pendente</option>
          <option value="em progresso"${r.status==='em progresso'?' selected':''}>Em Progresso</option>
          <option value="concluido"${r.status==='concluido'?' selected':''}>Concluído</option>
        </select>
      </td>
    `;
    table.appendChild(row);
  });
}

// Atualizar status de um pedido (admin)
async function updateStatus(id, status) {
  const data = await api('/update_status.php', {
    method: 'POST',
    body: JSON.stringify({ id, status })
  });
  alert(data.message || 'Status atualizado');
  loadAllRequests();
}

// ------ Inicialização ------

document.addEventListener('DOMContentLoaded', () => {
  // Bind de formulários
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const email = loginForm.email.value;
      const password = loginForm.password.value;
      login(email, password).catch(err => alert(err.message));
    });
  }

  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', e => {
      e.preventDefault();
      const name = registerForm.name.value;
      const email = registerForm.email.value;
      const password = registerForm.password.value;
      register(name, email, password).catch(err => alert(err.message));
    });
  }

  const orderForm = document.getElementById('orderForm');
  if (orderForm) {
    orderForm.addEventListener('submit', e => {
      e.preventDefault();
      const serviceType = orderForm.service_type.value;
      const description = orderForm.description.value;
      submitRequest(serviceType, description).catch(err => alert(err.message));
    });
  }

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      logout().catch(err => alert(err.message));
    });
  }

  // Carregar histórico ou admin
  if (document.getElementById('historyTable')) {
    loadOrderHistory().catch(err => console.error(err));
  }
  if (document.getElementById('adminTable')) {
    loadAllRequests().catch(err => console.error(err));
  }
});
