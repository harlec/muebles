/**
 * Dashboard Muebles & Estilo — wiring del front contra la API PHP.
 * Fórmulas tomadas de design_handoff_dashboard_multilocal/README.md ("Fórmulas clave").
 * Nota: "índice de rotación" y "días de inventario" requieren una tabla de
 * stock que todavía no existe en el esquema (ver database/schema.sql) — se
 * muestran como N/D hasta que se agregue.
 */

const PAY_LABELS = {
  tarjeta_credito: 'Tarjeta crédito',
  credito_directo: 'Crédito directo',
  efectivo: 'Efectivo',
  transferencia: 'Transferencia',
};

const state = {
  tab: 'hoy',
  local: 'global',
};

const currency = window.APP_CURRENCY || '$ ';

function money(n) {
  const v = Math.round(Number(n) || 0);
  return currency + v.toLocaleString('es', { maximumFractionDigits: 0 });
}

function pct(n) {
  return Math.round(Number(n) || 0) + '%';
}

async function getJSON(url) {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`${url} -> ${res.status}`);
  return res.json();
}

// ---------- reloj ----------
function tickClock() {
  const el = document.getElementById('clock');
  el.textContent = new Date().toLocaleTimeString('es-PE', { hour12: false });
}
setInterval(tickClock, 1000);
tickClock();

// ---------- tema ----------
function applyTheme(theme) {
  document.documentElement.classList.toggle('light', theme === 'light');
  // Se muestra el ícono del tema al que se cambiará al hacer clic.
  document.getElementById('icon-sun').classList.toggle('hidden', theme !== 'dark');
  document.getElementById('icon-moon').classList.toggle('hidden', theme !== 'light');
}

(function initTheme() {
  let theme = 'dark';
  try { theme = localStorage.getItem('theme') || 'dark'; } catch (e) { /* ignore */ }
  applyTheme(theme);
})();

document.getElementById('theme-toggle').addEventListener('click', () => {
  const isLight = document.documentElement.classList.contains('light');
  const next = isLight ? 'dark' : 'light';
  applyTheme(next);
  try { localStorage.setItem('theme', next); } catch (e) { /* ignore */ }
});

// ---------- tabs ----------
function setTab(tab) {
  state.tab = tab;
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.tab === tab);
  });
  document.querySelectorAll('.view').forEach(section => {
    section.classList.toggle('hidden', section.id !== `view-${tab}`);
  });
  loadTab(tab);
}

document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => setTab(btn.dataset.tab));
});

// ---------- filtro de local ----------
function setLocal(local) {
  state.local = local;
  document.querySelectorAll('.chip-local').forEach(chip => {
    chip.classList.toggle('active', chip.dataset.local === local);
  });
  loadTab(state.tab);
}

document.querySelectorAll('.chip-local').forEach(chip => {
  chip.addEventListener('click', () => setLocal(chip.dataset.local));
});

function loadTab(tab) {
  if (tab === 'hoy') return loadHoy();
  if (tab === 'mes') return loadMes();
  if (tab === 'rotacion') return loadRotacion();
}

// ---------- VISTA HOY ----------
async function loadHoy() {
  const data = await getJSON(`/api/today?local=${state.local}`);

  renderChipsAmounts(data.ranking);
  renderKpisHoy(data);
  renderRanking(data.ranking, data.fecha);
  renderHoras(data.porHora);
  renderFeed(data.feed);

  const pagos = await getJSON(`/api/payments?scope=today&local=${state.local}`);
  renderPagos(pagos.mix, 'pagos-hoy-list');

  const rot = await getJSON(`/api/rotation?dias=30&local=${state.local}`);
  renderCategorias(rot.categorias);
}

function renderChipsAmounts(ranking) {
  const globalTotal = ranking.reduce((sum, r) => sum + Number(r.monto), 0);
  document.querySelector('.chip-local[data-local="global"] .chip-amount').textContent = money(globalTotal);
  ranking.forEach(r => {
    const el = document.querySelector(`.chip-local[data-local="${r.id}"] .chip-amount`);
    if (el) el.textContent = money(r.monto);
  });
}

function renderKpisHoy(data) {
  const hora = new Date().getHours();
  const abierto = hora >= 10 && hora < 20;

  document.getElementById('kpi-hoy-ventas').textContent = money(data.hoy.monto);
  document.getElementById('kpi-hoy-tickets').textContent = data.hoy.tickets;
  document.getElementById('kpi-hoy-tickets-delta').textContent =
    'promedio ' + money(data.hoy.tickets ? data.hoy.monto / data.hoy.tickets : 0);

  const ayerRef = Number(data.ayerHastaAhora.monto);
  const deltaPct = ayerRef ? ((data.hoy.monto - ayerRef) / ayerRef) * 100 : 0;
  const flecha = deltaPct >= 0 ? '▲' : '▼';
  document.getElementById('kpi-hoy-delta').textContent =
    `${flecha} ${Math.abs(Math.round(deltaPct))}% vs ayer a esta hora`;

  const metaPct = data.metaDiaria ? (data.hoy.monto / data.metaDiaria) * 100 : 0;
  document.getElementById('kpi-hoy-meta').textContent = Math.round(metaPct);
  const faltan = Math.max(data.metaDiaria - data.hoy.monto, 0);
  document.getElementById('kpi-hoy-meta-delta').textContent = money(faltan) + ' faltan';

  const label = document.getElementById('kpi-hoy-4-label');
  const valor = document.getElementById('kpi-hoy-4-valor');
  if (abierto) {
    // avanceDelDía = clamp((hora-10)/11, .08, 1) — proyección lineal simple.
    const avance = Math.min(1, Math.max((hora - 10) / 11, 0.08));
    label.textContent = 'Cierre estimado';
    valor.textContent = money(data.hoy.monto / avance);
  } else {
    label.textContent = 'Último día de venta';
    valor.textContent = money(data.ayerCompleto.monto);
  }
}

function renderRanking(ranking, fecha) {
  const list = document.getElementById('ranking-list');
  list.innerHTML = '';
  ranking.forEach((r, i) => {
    const metaPct = r.meta_diaria ? (r.monto / r.meta_diaria) * 100 : 0;
    const li = document.createElement('li');
    li.className = 'py-1.5 cursor-pointer';
    li.dataset.local = r.id;
    li.innerHTML = `
      <div class="flex items-center justify-between">
        <span class="font-medium">${String(i + 1).padStart(2, '0')} · ${r.nombre}</span>
        <span class="font-bold">${money(r.monto)}</span>
      </div>
      <div class="h-2 rounded mt-1" style="background:var(--track)">
        <div class="h-2 rounded" style="width:${Math.min(metaPct, 100)}%; background:${i === 0 ? '#ED0B4C' : 'var(--bar2)'}"></div>
      </div>
      <div class="text-[12px] mt-0.5" style="color:var(--mut)">${r.tickets} tickets · ${pct(metaPct)} de meta</div>
    `;
    li.addEventListener('click', () => setLocal(state.local === r.id ? 'global' : r.id));
    list.appendChild(li);
  });
}

function renderHoras(porHora) {
  const cont = document.getElementById('horas-bars');
  cont.innerHTML = '';
  const horaActual = new Date().getHours();
  const entries = Object.entries(porHora);
  const max = Math.max(...entries.map(([, v]) => v.monto), 1);
  let pico = entries[0];
  entries.forEach(e => { if (e[1].monto > pico[1].monto) pico = e; });

  entries.forEach(([hora, v]) => {
    const h = Number(hora);
    const bar = document.createElement('div');
    const alturaPct = Math.max((v.monto / max) * 100, 2);
    let color = 'var(--bar3)';
    if (h === Number(pico[0])) color = '#ED0B4C';
    else if (h > horaActual) color = 'var(--ghost)';
    bar.className = 'flex-1 rounded-t-[5px] rounded-b-[2px]';
    bar.style.height = alturaPct + '%';
    bar.style.background = color;
    bar.title = `${h}:00 — ${money(v.monto)}`;
    cont.appendChild(bar);
  });

  document.getElementById('horas-insight').textContent =
    `Hora pico: ${pico[0]}:00–${Number(pico[0]) + 1}:00 con ${money(pico[1].monto)} en ventas.`;
}

function renderPagos(mix, targetId) {
  const list = document.getElementById(targetId);
  list.innerHTML = '';
  const total = mix.reduce((s, m) => s + Number(m.monto), 0) || 1;
  mix.forEach(m => {
    const p = (Number(m.monto) / total) * 100;
    const li = document.createElement('li');
    li.className = 'py-1.5';
    li.innerHTML = `
      <div class="flex items-center justify-between">
        <span class="font-medium">${PAY_LABELS[m.metodo_pago] || m.metodo_pago}</span>
        <span class="font-bold">${money(m.monto)} · ${pct(p)}</span>
      </div>
      <div class="h-1.5 rounded mt-1" style="background:var(--track)">
        <div class="h-1.5 rounded" style="width:${p}%; background:#ED0B4C"></div>
      </div>
      <div class="text-[12px] mt-0.5" style="color:var(--faint)">${m.tickets} tickets · ticket prom ${money(m.tickets ? m.monto / m.tickets : 0)}</div>
    `;
    list.appendChild(li);
  });
}

function renderCategorias(categorias) {
  const list = document.getElementById('categorias-list');
  list.innerHTML = '';
  const total = categorias.reduce((s, c) => s + Number(c.monto), 0) || 1;
  categorias.forEach((c, i) => {
    const p = (Number(c.monto) / total) * 100;
    const li = document.createElement('li');
    li.className = 'py-1.5';
    li.innerHTML = `
      <div class="flex items-center justify-between">
        <span class="font-bold" style="${i === 0 ? 'color:var(--acc-txt)' : ''}">${c.categoria}</span>
        <span class="font-bold">${money(c.monto)} · ${pct(p)}</span>
      </div>
      <div class="h-2 rounded mt-1" style="background:var(--track)">
        <div class="h-2 rounded" style="width:${p}%; background:${i === 0 ? '#ED0B4C' : 'var(--bar5)'}"></div>
      </div>
    `;
    list.appendChild(li);
  });
}

function renderFeed(ventas) {
  const list = document.getElementById('feed-list');
  list.innerHTML = '';
  ventas.forEach(v => {
    const li = document.createElement('li');
    li.className = 'feed-row animate-fadeUp';
    const hora = new Date(v.creado_en).toTimeString().slice(0, 5);
    li.innerHTML = `
      <span class="w-10 font-bold" style="color:#ED0B4C">${hora}</span>
      <span class="flex-1 truncate font-semibold">${v.producto_nombre}</span>
      <span class="text-[13px]" style="color:var(--mut)">${v.local_nombre} · ${PAY_LABELS[v.metodo_pago] || v.metodo_pago}</span>
      <span class="font-bold">${money(v.monto)}</span>
    `;
    list.appendChild(li);
  });
  window._lastFeedId = ventas.length ? Math.max(...ventas.map(v => v.id)) : window._lastFeedId;
}

async function pollFeed() {
  if (state.tab !== 'hoy') return;
  try {
    const data = await getJSON(`/api/feed?since=${window._lastFeedId || 0}`);
    if (data.ventas.length) renderFeed(data.ventas.concat().reverse());
  } catch (e) { /* silencioso: no interrumpe la demo si la API falla puntualmente */ }
}
setInterval(pollFeed, 9000);

// ---------- VISTA MES ----------
async function loadMes() {
  const data = await getJSON(`/api/month?local=${state.local}`);

  const totalMes = Object.values(data.serieMes).reduce((s, v) => s + v, 0);
  const promedioDiario = data.diaActual ? totalMes / data.diaActual : 0;
  const proyeccion = promedioDiario * data.diasDelMes;
  const metaPct = data.metaMensual ? (totalMes / data.metaMensual) * 100 : 0;

  document.getElementById('kpi-mes-ventas').textContent = money(totalMes);
  document.getElementById('kpi-mes-meta').textContent = Math.round(metaPct);
  document.getElementById('kpi-mes-proyeccion').textContent = money(proyeccion);
  document.getElementById('kpi-mes-promedio').textContent = money(promedioDiario);

  document.getElementById('mes-insight').textContent = proyeccion >= data.metaMensual
    ? `En ruta para superar la meta del mes por ${money(proyeccion - data.metaMensual)}.`
    : `Proyección ${money(data.metaMensual - proyeccion)} por debajo de la meta del mes.`;

  renderProyeccionLocal(data.porLocal, data.diaActual, data.diasDelMes);

  const pagos = await getJSON(`/api/payments?scope=month&local=${state.local}`);
  renderPagos(pagos.mix, 'pagos-mes-list');
}

function renderProyeccionLocal(porLocal, diaActual, diasDelMes) {
  const list = document.getElementById('proyeccion-local-list');
  list.innerHTML = '';
  porLocal.forEach(l => {
    const acumulado = Number(l.acumulado.monto);
    const proyectado = diaActual ? (acumulado / diaActual) * diasDelMes : 0;
    const pctMeta = l.meta_mensual ? ((proyectado - l.meta_mensual) / l.meta_mensual) * 100 : 0;
    const enRuta = pctMeta >= 0;
    const li = document.createElement('li');
    li.className = 'py-1.5';
    li.innerHTML = `
      <div class="flex items-center justify-between">
        <span class="font-medium">${l.nombre}</span>
        <span style="color:var(--mut)">${money(acumulado)} → <b style="color:var(--ink)">${money(proyectado)}</b></span>
      </div>
      <div class="text-[12px] mt-0.5" style="color:${enRuta ? 'var(--ok)' : '#ED0B4C'}">
        ${enRuta ? '▲' : '▼'} ${Math.abs(Math.round(pctMeta))}% vs meta · ${enRuta ? 'en ruta' : 'requiere empuje'}
      </div>
    `;
    list.appendChild(li);
  });
}

// ---------- VISTA ROTACIÓN ----------
async function loadRotacion() {
  const data = await getJSON(`/api/rotation?dias=30&local=${state.local}`);

  const unidades = data.productos.reduce((s, p) => s + Number(p.unidades), 0);
  document.getElementById('kpi-rot-unidades').textContent = unidades;
  document.getElementById('kpi-rot-indice').textContent = 'N/D';
  document.getElementById('kpi-rot-dias').textContent = 'N/D';

  const list = document.getElementById('top-productos-list');
  list.innerHTML = `<li class="text-[12.5px] mb-2" style="color:var(--faint)">
    Rotación (×) y días de stock: pendiente — falta tabla de inventario en el esquema.
  </li>`;
  data.productos.forEach((p, i) => {
    const li = document.createElement('li');
    li.className = 'py-1.5 flex items-center justify-between';
    li.innerHTML = `
      <span class="font-bold">${String(i + 1).padStart(2, '0')} · ${p.nombre}</span>
      <span class="font-bold">${p.unidades} u</span>
    `;
    list.appendChild(li);
  });

  document.getElementById('alertas-rotacion').innerHTML =
    `<div class="text-[13px] p-3 rounded-[14px]" style="background:var(--inset);color:var(--body2)">
      Alertas de quiebre/baja rotación/oportunidad: pendiente hasta tener stock por producto.
    </div>`;
}

// ---------- inicio ----------
setLocal('global');
setTab('hoy');
