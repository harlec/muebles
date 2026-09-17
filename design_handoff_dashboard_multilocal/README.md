# Handoff: Dashboard multilocal · Muebles & Estilo

## Overview
Panel de control de ventas en tiempo real para una mueblería con **4 locales** (Centro, Mall, Norte, Sur). Responde de un vistazo: cuánto se vendió hoy (global y por local), qué local vende más, en qué horas se vende, con qué método de pago se paga, cómo va el mes contra la meta, cuál es la proyección de cierre y qué producto/categoría rota más.

Mobile-first, monocromo (negro + carmín de marca), con variante clara y oscura.

## About the Design Files
Los archivos de este paquete son **referencias de diseño hechas en HTML** — un prototipo que muestra el look y el comportamiento buscados, **no código de producción para copiar tal cual**. La tarea es **recrear estos diseños en el entorno del codebase destino** (React, Vue, Next, SwiftUI, Flutter, etc.) usando sus patrones y librerías ya establecidas. Si todavía no existe un entorno, elegir el framework más apropiado (sugerencia: React + un motor de charts como Recharts/Visx) e implementarlo ahí.

`Dashboard Muebles y Estilo.dc.html` es un componente de un runtime propio (`support.js`): template HTML con huecos `{{ }}` + una clase de lógica. **Toda la lógica de negocio y de presentación está en la clase `Component` al final del archivo** (método `renderVals()`), y es la mejor fuente de verdad para fórmulas, textos y colores. Los datos son simulados (generador pseudoaleatorio con semilla) — deben reemplazarse por la API real.

## Fidelity
**High-fidelity.** Colores, tipografía, espaciado, estados y copys son finales. Se espera recrear la UI con fidelidad usando las librerías del codebase.

## Screens / Views
Una sola pantalla con **tres vistas conmutables** (tabs) y **dos filtros globales** que afectan a todo el contenido.

### Chrome global (siempre visible)

**Header** (sticky, `top: 0`, fondo `linear-gradient(<bg> 72%, transparent)`, z-index 20)
- Flex row, `gap: 12px 10px`, `flex-wrap: wrap`, padding `16px 2px 14px`.
- Logo: 46×46, `border-radius: 12px`, `object-fit: cover`, fondo `#000`, borde 1px `<bd2>`.
- Columna de texto (`flex: 1 1 170px; min-width: 150px`):
  - Título "MUEBLES & ESTILO" — 15px / 700 / `letter-spacing: .14em` / uppercase.
  - Subtítulo "CENTRO DE CONTROL · 4 LOCALES" — 11px / `letter-spacing: .12em` / uppercase / color `<mut>` / `white-space: nowrap`.
- Grupo derecho (`margin-left: auto`, flex, gap 8px):
  - Píldora "en vivo": punto 7px carmín con animación `pulseDot` (1.4s infinite; opacidad 1→.25, scale 1→.75) + reloj `HH:MM:SS` (11px/700, tabular). Borde 1px `<accBd>`, fondo `<accSoft>`, radio 99px, padding `7px 11px`.
  - Botón tema: texto "CLARO" u "OSCURO" (11px/700/uppercase/`.08em`), radio 99px, padding `8px 13px`, fondo `<chip>`, borde `<bd2>`.

**Filtro de local** (fila scroll-x, `gap: 7px`)
- Chips: `Global` + los 4 locales. Cada chip: nombre 13px/700 + subtexto con la venta del día (11px, opacidad .72), padding `9px 13px`, radio 12px.
- Activo: fondo `#ED0B4C`, texto `#fff`, borde `#ED0B4C`. Inactivo: fondo `<chip>`, texto `<ink>`, borde `<bd2>`.

**Tabs de vista** (sticky `top: 74px`, z-index 19)
- Grid 3 columnas, gap 6px, padding 5px, fondo `<chip>`, borde 1px `<bd>`, radio 14px.
- Botones: "HOY" / "MES" / "ROTACIÓN" — 11px/700/uppercase/`.1em`, radio 10px, padding `10px 6px`. Activo: fondo `#ED0B4C`, texto `#fff`. Inactivo: transparente, texto `<mut>`. Transición `all .18s`.

**Contenedor**: `max-width` según prop `layoutWidth` (compacto 1180px / amplio 1500px / completo 100%), `margin: 0 auto`, `padding: 0 16px`.

**Footer**: dos líneas micro (10.5px, uppercase, `.06em`, color `<faint>`): "DEMO · DATOS SIMULADOS EN VIVO" · "UN ESTILO DIFERENTE PARA TU HOGAR".

### Tarjeta KPI (patrón reusado en las 3 vistas)
Grid `repeat(auto-fit, minmax(150px,1fr))`, gap 10px. Cada tarjeta: padding `14px 14px 12px`, radio 16px, `overflow: hidden`, borde 1px, fondo degradado.
1. Fila de etiqueta: punto 6px (color = estado) + label 11px/700/uppercase/`.13em`.
2. Valor: 36px / **weight 500** / `letter-spacing: -.02em` / `font-variant-numeric: tabular-nums` + unidad 12px color `<mut>` (`%`, `tickets`, `días`, `u`).
3. Fila delta: texto 11px color `<mut>` + flecha `▲`/`▼` 11px/700 alineada a la derecha (verde si bien, carmín si mal, `<faint>` si neutro).
4. Sparkline SVG `viewBox="0 0 100 26"`, `preserveAspectRatio="none"`, alto 24px: área rellena + línea `stroke-width: 1.4` con `vector-effect="non-scaling-stroke"` (14 puntos).

La primera tarjeta de cada vista es **hero**: fondo `<heroBg>` (degradado carmín), borde `<accBd>`, label en color de acento, sparkline carmín.

### Vista 1 — HOY
KPIs: **Ventas hoy** (hero, delta "±N% vs ayer a esta hora"), **Tickets** (unidad "tickets", delta "promedio $X"), **Meta del día** (unidad "%", delta "$X faltan"), y la cuarta cambia según horario: **Cierre estimado** si la tienda está abierta (10–20h) o **Último día de venta** si está cerrada.

Grid de paneles: `repeat(auto-fit, minmax(300px,1fr))`, gap 12px, tarjetas que igualan altura (stretch). Cada panel: `display:flex; flex-direction:column`, padding 16px, radio 18px, fondo `<cardGrad>`, borde 1px `<bd>`.

Encabezado de panel: `h2` flex con punto 6px color `<accTxt>` + título 12px/700/uppercase/`.11em`; a la derecha, meta-label 10.5px uppercase color `<mut>`.

1. **QUIÉN VENDE MÁS HOY** (meta-label "EN VIVO")
   - Dona de participación: 104px, `conic-gradient` con 4 tramos (colores por posición: `#ED0B4C`, `rgba(237,11,76,.60)`, `<bar3>`, `<bar5>`), centro 70px del color de tarjeta con "TOTAL" (10px) + monto total del día (14px/500).
   - Leyenda al lado (`flex: 1; min-width: 140px`): cuadro 8px del color + nombre + `%` a la derecha.
   - Ranking detallado (lista que reparte altura con `justify-content: space-between`): posición `01`–`04`, nombre (carmín si es el local filtrado), "N tickets", monto a la derecha; barra 8px (`<track>` de fondo, relleno carmín para el líder y `<bar2>` para el resto, `transition: width .6s cubic-bezier(.2,.8,.2,1)`); pie con "meta día $X" y "N% de meta" (verde ≥80, `<mut>` ≥55, carmín debajo). **Clic en una fila filtra ese local** (y vuelve a Global si ya estaba activo).
2. **HORARIOS DE MÁS VENTA** (meta-label "pico HH:00 – HH:00" en acento)
   - 11 barras (10h→20h) que crecen con la tarjeta (`flex: 1; min-height: 132px`), gap 4px, radio `5px 5px 2px 2px`, `transition: height .5s`.
   - Hora pico: carmín. Horas pasadas: `<bar3>`. Horas futuras: `<ghost>` con borde `<ghostRing>` (etiqueta en `<ghostTxt>`).
   - Caja de insight: padding `11px 12px`, radio 12px, fondo `<inset>`, texto 11.5px `line-height: 1.45` color `<body>`.
3. **MÉTODOS DE PAGO · HOY**
   - Dona 118px `conic-gradient` de 4 tramos; centro 78px con "TICKETS" + conteo (20px/500).
   - Bloque "MEZCLA DEL DÍA" con frase generada.
   - Cuatro filas (reparten altura): cuadro de color 9px, nombre 12.5px/500, monto 11.5px/700, `%` 11px `<mut>`; barra 6px; pie "N tickets · ticket prom $X" (10px `<faint>`).
4. **VENTAS ENTRANDO** (meta-label "TIEMPO REAL" o "PAUSADO")
   - **5 filas máximo**, `justify-content: space-between`: hora `HH:MM` (11px/700 carmín, ancho 40px), producto (12px/600, truncado con ellipsis), "Local · Método" (11px `<mut>`), monto (12px/700 tabular). Separador inferior 1px `<bd4>`. Animación de entrada `fadeUp .35s` (opacidad 0→1, `translateY(6px)`→0).
5. **CATEGORÍAS MÁS VENDIDAS**
   - Selector propio de periodo: 3 píldoras "HOY / MES / AÑO" (10px/700/uppercase/`.08em`, padding `5px 11px`, radio 99px; activa con fondo `<accSoft>`, texto `<accTxt>`, borde `<accBd>`).
   - Filas por categoría (Salas, Colchones, Comedores, Dormitorio, Complementos): nombre 12.5px/700 (acento en la #1) + "N u · rot N.N×" + monto + `%`; barra 8px (1ª carmín, 2ª `rgba(237,11,76,.55)`, resto `<bar5>`), `transition: width .6s`.
   - Caja de insight al pie.

### Vista 2 — MES
KPIs: **Ventas del mes** (hero), **Meta del mes** (%), **Proyección cierre**, **Promedio diario**.

1. **SEPTIEMBRE · REAL VS PROYECCIÓN** — panel con retícula de fondo (`<gridBg>`: dos `repeating-linear-gradient` de 1px cada 34px sobre el degradado de tarjeta).
   - SVG `viewBox="0 0 640 210"`, ancho 100%: área `<area>`; línea de meta horizontal `stroke-dasharray="2 5"` color `<metaLine>`; serie real `stroke: <line>`, `stroke-width: 2.5`, `linejoin/linecap: round`; proyección `stroke: #ED0B4C`, `stroke-width: 2.5`, `stroke-dasharray="7 6"`; punto de hoy: círculo r=4.5 carmín con borde 2px del color de tarjeta; ticks en los días 1, 7, 14, 21, 30 (10px, `<mut>`).
   - Leyenda en el header: "— real" (`<ink>`), "--- proyección" (`<accTxt>`), "· · meta" (`<mut>`).
   - Insight con dos redacciones según si la proyección supera la meta o no.
2. **PROYECCIÓN DE CIERRE POR LOCAL** — por local: nombre, acumulado (`<mut>`), "→ proyectado" (700); barra apilada 8px: parte real `<ink>` + parte proyectada `rgba(237,11,76,.55)`; pie "±N% vs meta · en ruta / requiere empuje" (verde/carmín).
3. **MÉTODOS DE PAGO · MES** — nombre + monto + `%` + barra 8px del color del método; insight al pie.

### Vista 3 — ROTACIÓN
KPIs: **Unidades vendidas** (hero, unidad "u"), **Índice de rotación** ("2.3×"), **Días de inventario** (unidad "días").

1. **LO QUE MÁS SALE · <alcance>** (meta-label "30 DÍAS") — 10 filas: posición `01`–`10` (zero-padded), nombre 13px/700, "N u" a la derecha; segunda línea (sangría 28px): barra 6px (top-3 carmín, resto `<bar4>`), chip "rot N.N×" (fondo `<rotBg>` y texto `<rotFg>` si rot ≥ 2, si no `<track>`/`<mut>`), "N días stock", "▲ sube"/"▼ baja" (verde/`<mut>`).
2. **ALERTAS DE ROTACIÓN** — grid `repeat(auto-fit, minmax(250px,1fr))`, 3 tarjetas (padding 13px, radio 14px, fondo `<inset>`): etiqueta 10.5px uppercase `.1em` ("QUIEBRE CERCANO" en acento, "BAJA ROTACIÓN" y "OPORTUNIDAD" en `<mut>`) + texto 12.5px `<body2>`.

## Interactions & Behavior
- **Filtro de local**: chips y clic en filas del ranking → recalcula TODOS los números y textos de las tres vistas (alcance = 1 local o los 4).
- **Tabs**: intercambian la vista completa; los filtros se conservan.
- **Selector de periodo de categorías**: hoy = mensual ÷ 30 × avance del día, mes = mensual, año = mensual × 11.4.
- **Tema claro/oscuro**: botón en header (y prop `theme`); cambia toda la paleta.
- **Tiempo real**: reloj cada 1s; una venta simulada cada **9s** (si `liveUpdates`), que se antepone al feed y suma al total e `tickets` del local. Tope de seguridad: el simulador nunca agrega más del **12% de la meta diaria** por local (evita que los números se disparen con la demo abierta).
- **Horario de tienda**: 10:00–20:00. Fuera de horario, la 4ª KPI cambia de "Cierre estimado" a "Último día de venta" y "vs ayer" compara el día completo (dentro de horario, compara contra ayer **a la misma hora**, usando el avance del día).
- **Transiciones**: barras `width/height .5–.6s cubic-bezier(.2,.8,.2,1)`; chips/botones `all .18s`; filas del feed `fadeUp .35s`.
- **Responsive**: mobile-first. Header envuelve (controles a segunda fila); grids `auto-fit minmax(150px | 250px | 300px, 1fr)` pasan de 1 a N columnas; filtro de local con scroll horizontal. Sin anchos fijos salvo donas y sparklines.

## State Management
```
theme: 'dark' | 'light' | null   // null = usa la prop
tab: 'hoy' | 'mes' | 'prod'
local: 'global' | 'centro' | 'mall' | 'norte' | 'sur'
catPeriod: 'hoy' | 'mes' | 'anio'
extra: { [localId]: { amt, tk } }  // ventas llegadas en vivo
feed: Sale[]                       // últimas ventas (se muestran 5)
clock: 'HH:MM:SS'
```
Props/tweaks: `theme`, `layoutWidth` ('compacto'|'amplio'|'completo'), `currency` (prefijo, p. ej. `"S/ "`), `goalFactor` (0.7–1.4, multiplica todas las metas), `liveUpdates` (bool).

### Datos que necesita la API real
- **Por local**: id, nombre, meta diaria, meta mensual.
- **Ventas del día por hora** (10–20h): monto y nº de tickets.
- **Ventas del mes por día** (serie diaria) y días del mes.
- **Ventas del día anterior** (completo y acumulado por hora, para el "vs ayer a esta hora").
- **Mix de métodos de pago** por local y periodo: monto y tickets por método.
- **Productos** (30 días): nombre, categoría, unidades, precio promedio, índice de rotación, días de stock, tendencia.
- **Stream de ventas** (websocket/polling): hora, local, producto, método, monto.

### Fórmulas clave
- `ventasHoy = Σ horas hasta la hora actual + ventas en vivo`
- `cierreEstimado = Σ todas las horas del día + ventas en vivo`
- `metaPct = ventasHoy / (metaDiaria × goalFactor)`
- `promedioDiario = ventasMes / díasTranscurridos`; `proyecciónMes = promedioDiario × díasDelMes`
- `faltanteDiario = (metaMes − ventasMes) / díasRestantes`
- `ticketPromedio = monto / tickets` (global y por método)
- `avanceDelDía = clamp((hora − 10) / 11, 0.08, 1)`

## Design Tokens

### Marca
`#ED0B4C` (carmín del logo) — acento principal para rellenos. Texto de acento: `#FF8AAB` (oscuro) / `#C10A3E` (claro), para cumplir contraste ≥4.5:1.

### Tema oscuro
```
bg #08080A · card #0F0F11 · chip #131316 · inset #17171B · track #232328
cardGrad linear-gradient(165deg,#131317,#0C0C0E)
heroBg   linear-gradient(158deg,rgba(237,11,76,.24),rgba(237,11,76,.05) 55%,#0E0E11)
bd rgba(255,255,255,.11) · bd2 .14 · bd3 .16 · bd4 .09
ink #FBFBFC · body #D2D2DA · body2 #E8E8EE · mut #A8A8B2 · faint #92929C · ghostTxt #5E5E68 · rule #35353C
bar2 rgba(251,251,252,.58) · bar3 .42 · bar4 .44 · bar5 .36 · ghost .10 · ghostRing .18 · area .10
line #FBFBFC · metaLine #5E5E68 · accSoft rgba(237,11,76,.16) · accBd rgba(237,11,76,.45)
rotBg rgba(237,11,76,.22) · rotFg #FF9EB9 · ok #6FE09A
```

### Tema claro
```
bg #F4F2F0 · card #FFFFFF · chip #FFFFFF · inset #F2F0ED · track #E4E0DC
cardGrad linear-gradient(165deg,#FFFFFF,#FAF8F5)
heroBg   linear-gradient(158deg,rgba(237,11,76,.14),rgba(237,11,76,.03) 55%,#FFFFFF)
bd rgba(18,16,20,.13) · bd2 .16 · bd3 .18 · bd4 .10
ink #131318 · body #3C3C46 · body2 #25252D · mut #5C5C66 · faint #6C6C76 · ghostTxt #A4A4AE · rule #CECAC6
bar2 rgba(19,19,24,.46) · bar3 .32 · bar4 .34 · bar5 .26 · ghost .08 · ghostRing .14 · area .07
line #131318 · metaLine #A4A4AE · accSoft rgba(237,11,76,.09) · accBd rgba(237,11,76,.38)
rotBg rgba(237,11,76,.12) · rotFg #AE0736 · ok #12764A
```

### Colores de métodos de pago
Tarjeta crédito `#ED0B4C` · Crédito directo `#8E0630` · Efectivo `#55555C` · Transferencia `#FF7FA2`.

### Tipografía
**Roboto** (Google Fonts), pesos cargados 300/400/500/700/900. No usar 800 (se sintetiza y engruesa el trazo).
- Cifra KPI 36px/500/`-.02em`, tabular-nums
- Título de panel 12px/700/uppercase/`.11em`
- Etiqueta KPI 11px/700/uppercase/`.13em`
- Cuerpo 12.5px/400–500, insight 11.5px/`line-height 1.45`
- Micro 10–11px (mínimo 10px; no bajar más: en pantallas no-Retina se ve sucio)
- **No** declarar `-webkit-font-smoothing` ni `text-rendering`: producen halos de color en textos pequeños de colores saturados.

### Espaciado y formas
Espaciado: 2 · 4 · 5 · 6 · 7 · 8 · 10 · 12 · 14 · 16 · 18px. Radios: 99px (píldoras/donas) · 18px (paneles) · 16px (KPI) · 14px (chips/tabs) · 12px (chip de filtro, insight) · 10px (tab) · 5/3/1px (barras). Bordes: 1px. Sin sombras — la jerarquía viene del degradado y el borde.

### Formato de números
Miles con punto (`13.550`), moneda como prefijo configurable (`$ ` / `S/ `), porcentajes enteros, `font-variant-numeric: tabular-nums` en todas las cifras.

## Assets
- `logo.jpg` — logo "Muebles & Estilo" provisto por el cliente (JPG cuadrado con fondo negro). Se muestra 46×46 con radio 12px sobre fondo `#000`. **Pedir al cliente un PNG/SVG con transparencia** para que funcione limpio en el tema claro.
- Sin librería de iconos: los únicos glifos son `▲ ▼ ×` y puntos/cuadros CSS.
- Fuente: Google Fonts Roboto.

## Files
- `Dashboard Muebles y Estilo.dc.html` — el diseño completo (template + lógica en la clase `Component`). Fuente de verdad de fórmulas, copys y colores.
- `support.js` — runtime del prototipo (no se implementa; solo permite abrir el HTML en el navegador).
- `logo.jpg` — logo del cliente.

## Notas de implementación
- Los datos del prototipo son **simulados con semilla** (`rng()` en la clase): sirven para validar rangos y estados, no como cifras reales.
- Textos de insight son **generados** (plantillas con condicionales). Revisar sus ramas: hay casos donde dos superlativos podían caer en el mismo elemento y la frase se contradecía — ver `payTodayInsight` y `projInsight` para las redacciones ya corregidas.
- Accesibilidad: objetivos táctiles ≥44px en móvil (chips y tabs cumplen con su padding), contraste de texto ≥4.5:1 en ambos temas.
