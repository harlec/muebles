<?php
$maxWidths = ['compacto' => '1180px', 'amplio' => '1500px', 'completo' => 'none'];
$maxWidth = $maxWidths[$layoutWidth ?? 'completo'] ?? 'none';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Muebles & Estilo · Centro de control</title>
<link rel="stylesheet" href="/css/app.css">
</head>
<body class="bg-bg text-ink min-h-screen">

<div class="mx-auto px-4" style="max-width: <?= htmlspecialchars($maxWidth) ?>">

  <header class="sticky top-0 z-20 flex flex-wrap items-center gap-x-2.5 gap-y-3 px-0.5 py-3.5"
          style="background-image: linear-gradient(var(--bg) 72%, transparent)">
    <img src="/assets/logo.jpg" alt="Muebles & Estilo" width="46" height="46"
         class="rounded-xl object-cover bg-black border" style="border-color: var(--bd2)">

    <div class="flex-1 min-w-[150px] basis-[170px]">
      <div class="text-[15px] font-bold uppercase tracking-[.14em]">Muebles &amp; Estilo</div>
      <div class="text-[11px] uppercase tracking-[.12em] text-mut whitespace-nowrap">Centro de control · 4 locales</div>
    </div>

    <div class="ml-auto flex items-center gap-2">
      <div class="flex items-center gap-2 rounded-full px-[11px] py-[7px] border"
           style="background: var(--acc-soft); border-color: var(--acc-bd)">
        <span class="w-[7px] h-[7px] rounded-full animate-pulseDot" style="background: #ED0B4C"></span>
        <span id="clock" class="text-[11px] font-bold tabular-nums">--:--:--</span>
      </div>
      <button id="theme-toggle" type="button" aria-label="Cambiar tema"
              class="rounded-full w-9 h-9 flex items-center justify-center bg-chip border"
              style="border-color: var(--bd2)">
        <svg id="icon-sun" class="hidden" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="2" stroke-linecap="round">
          <circle cx="12" cy="12" r="4.5"></circle>
          <path d="M12 2.5v2.5M12 19v2.5M4.2 4.2l1.8 1.8M18 18l1.8 1.8M2.5 12H5M19 12h2.5M4.2 19.8l1.8-1.8M18 6l1.8-1.8"></path>
        </svg>
        <svg id="icon-moon" class="hidden" width="16" height="16" viewBox="0 0 24 24" fill="var(--ink)">
          <path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z"></path>
        </svg>
      </button>
    </div>
  </header>

  <div id="local-filter" class="flex gap-[7px] overflow-x-auto pb-2">
    <button class="chip-local shrink-0 rounded-xl px-[13px] py-[9px] text-left border" data-local="global">
      <div class="text-[13px] font-bold">Global</div>
      <div class="text-[11px] opacity-70 chip-amount">—</div>
    </button>
    <?php foreach ($locales as $local): ?>
    <button class="chip-local shrink-0 rounded-xl px-[13px] py-[9px] text-left border" data-local="<?= htmlspecialchars($local['id']) ?>">
      <div class="text-[13px] font-bold"><?= htmlspecialchars($local['nombre']) ?></div>
      <div class="text-[11px] opacity-70 chip-amount">—</div>
    </button>
    <?php endforeach; ?>
  </div>

  <div class="sticky z-[19] grid grid-cols-3 gap-1.5 p-[5px] bg-chip border rounded-[14px] my-3"
       style="top: 74px; border-color: var(--bd)">
    <button class="tab-btn rounded-[10px] py-2.5 px-1.5 text-[11px] font-bold uppercase tracking-[.1em] transition-all duration-[180ms]" data-tab="hoy">Hoy</button>
    <button class="tab-btn rounded-[10px] py-2.5 px-1.5 text-[11px] font-bold uppercase tracking-[.1em] transition-all duration-[180ms]" data-tab="mes">Mes</button>
    <button class="tab-btn rounded-[10px] py-2.5 px-1.5 text-[11px] font-bold uppercase tracking-[.1em] transition-all duration-[180ms]" data-tab="rotacion">Rotación</button>
  </div>

  <!-- ===================== VISTA HOY ===================== -->
  <section id="view-hoy" class="view">
    <div class="grid gap-2.5" style="grid-template-columns: repeat(auto-fit, minmax(150px,1fr))">
      <div class="kpi kpi-hero">
        <div class="kpi-label"><span class="dot" style="background:#ED0B4C"></span>Ventas hoy</div>
        <div class="kpi-value"><span id="kpi-hoy-ventas">—</span></div>
        <div class="kpi-delta" id="kpi-hoy-delta">± — vs ayer a esta hora</div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Tickets</div>
        <div class="kpi-value"><span id="kpi-hoy-tickets">—</span> <span class="unit">tickets</span></div>
        <div class="kpi-delta" id="kpi-hoy-tickets-delta">promedio —</div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Meta del día</div>
        <div class="kpi-value"><span id="kpi-hoy-meta">—</span> <span class="unit">%</span></div>
        <div class="kpi-delta" id="kpi-hoy-meta-delta">— faltan</div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span><span id="kpi-hoy-4-label">Cierre estimado</span></div>
        <div class="kpi-value"><span id="kpi-hoy-4-valor">—</span></div>
      </div>
    </div>

    <div class="grid gap-3 mt-3" style="grid-template-columns: repeat(auto-fit, minmax(300px,1fr))">
      <div class="panel">
        <h2 class="panel-h"><span class="dot" style="background:var(--acc-txt)"></span>Quién vende más hoy <span class="meta-label">en vivo</span></h2>
        <ol id="ranking-list" class="flex-1 flex flex-col justify-between text-[12.5px]">
          <li class="text-mut">Cargando…</li>
        </ol>
      </div>

      <div class="panel">
        <h2 class="panel-h">Horarios de más venta</h2>
        <!-- TODO: barras 10h-20h (README §Vista 1, punto 2) -->
        <div id="horas-bars" class="flex-1 flex items-end gap-1" style="min-height:132px"></div>
        <div class="insight" id="horas-insight">Cargando…</div>
      </div>

      <div class="panel">
        <h2 class="panel-h">Métodos de pago · hoy</h2>
        <!-- TODO: dona conic-gradient (README §Vista 1, punto 3) -->
        <ul id="pagos-hoy-list" class="flex-1 flex flex-col justify-between text-[12.5px]">
          <li class="text-mut">Cargando…</li>
        </ul>
      </div>

      <div class="panel">
        <h2 class="panel-h">Ventas entrando <span class="meta-label" id="feed-status">tiempo real</span></h2>
        <ul id="feed-list" class="flex-1 flex flex-col"></ul>
      </div>

      <div class="panel">
        <h2 class="panel-h">Categorías más vendidas</h2>
        <ul id="categorias-list" class="flex-1 flex flex-col justify-between text-[12.5px]">
          <li class="text-mut">Cargando…</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- ===================== VISTA MES ===================== -->
  <section id="view-mes" class="view hidden">
    <div class="grid gap-2.5" style="grid-template-columns: repeat(auto-fit, minmax(150px,1fr))">
      <div class="kpi kpi-hero">
        <div class="kpi-label"><span class="dot" style="background:#ED0B4C"></span>Ventas del mes</div>
        <div class="kpi-value"><span id="kpi-mes-ventas">—</span></div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Meta del mes</div>
        <div class="kpi-value"><span id="kpi-mes-meta">—</span> <span class="unit">%</span></div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Proyección cierre</div>
        <div class="kpi-value"><span id="kpi-mes-proyeccion">—</span></div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Promedio diario</div>
        <div class="kpi-value"><span id="kpi-mes-promedio">—</span></div>
      </div>
    </div>

    <div class="grid gap-3 mt-3" style="grid-template-columns: repeat(auto-fit, minmax(300px,1fr))">
      <div class="panel">
        <h2 class="panel-h">Real vs proyección</h2>
        <!-- TODO: SVG de serie (README §Vista 2, punto 1) -->
        <div class="insight" id="mes-insight">Cargando…</div>
      </div>
      <div class="panel">
        <h2 class="panel-h">Proyección de cierre por local</h2>
        <ul id="proyeccion-local-list" class="flex-1 flex flex-col justify-between text-[12.5px]">
          <li class="text-mut">Cargando…</li>
        </ul>
      </div>
      <div class="panel">
        <h2 class="panel-h">Métodos de pago · mes</h2>
        <ul id="pagos-mes-list" class="flex-1 flex flex-col justify-between text-[12.5px]">
          <li class="text-mut">Cargando…</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- ===================== VISTA ROTACIÓN ===================== -->
  <section id="view-rotacion" class="view hidden">
    <div class="grid gap-2.5" style="grid-template-columns: repeat(auto-fit, minmax(150px,1fr))">
      <div class="kpi kpi-hero">
        <div class="kpi-label"><span class="dot" style="background:#ED0B4C"></span>Unidades vendidas</div>
        <div class="kpi-value"><span id="kpi-rot-unidades">—</span> <span class="unit">u</span></div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Índice de rotación</div>
        <div class="kpi-value"><span id="kpi-rot-indice">—</span></div>
      </div>
      <div class="kpi">
        <div class="kpi-label"><span class="dot" style="background:var(--bar3)"></span>Días de inventario</div>
        <div class="kpi-value"><span id="kpi-rot-dias">—</span> <span class="unit">días</span></div>
      </div>
    </div>

    <div class="grid gap-3 mt-3" style="grid-template-columns: repeat(auto-fit, minmax(300px,1fr))">
      <div class="panel">
        <h2 class="panel-h">Lo que más sale <span class="meta-label">30 días</span></h2>
        <ol id="top-productos-list" class="flex-1 flex flex-col text-[13px]">
          <li class="text-mut">Cargando…</li>
        </ol>
      </div>
      <div class="panel">
        <h2 class="panel-h">Alertas de rotación</h2>
        <!-- TODO: 3 tarjetas quiebre/baja rotación/oportunidad (README §Vista 3, punto 2) -->
        <div id="alertas-rotacion" class="grid gap-2.5" style="grid-template-columns: repeat(auto-fit, minmax(250px,1fr))"></div>
      </div>
    </div>
  </section>

  <footer class="text-center py-6 text-[10.5px] uppercase tracking-[.06em]" style="color: var(--faint)">
    <div>Muebles &amp; Estilo · Centro de control</div>
    <div>Un estilo diferente para tu hogar</div>
  </footer>
</div>

<script>window.APP_CURRENCY = <?= json_encode($currency ?? '$ ') ?>;</script>
<script src="/js/dashboard.js"></script>
</body>
</html>
