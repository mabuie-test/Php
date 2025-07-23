// admin.js

const API_BASE = '/backend'; // ajusta se necessário

// Helper para chamadas à API (mesma função do script.js)
async function api(path, opts = {}) {
  const headers = opts.headers || {};
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

// Inicialização ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      fetch(`${API_BASE}/logout.php`, { method: 'GET' })
        .then(() => { window.location.href = 'login.php'; })
        .catch(err => alert(err.message));
    });
  }

  // Se estamos na página admin, carrega os pedidos
  if (document.getElementById('adminTable')) {
    loadAllRequests().catch(err => console.error(err));
  }
});
