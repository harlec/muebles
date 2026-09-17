-- Datos base para desarrollo local. Los montos de metas replican los del
-- prototipo (Component.LOCALES en el HTML de diseño).

USE muebles_dashboard;

INSERT INTO locales (id, nombre, meta_diaria, meta_mensual) VALUES
  ('centro', 'Centro', 3800.00, 98000.00),
  ('mall',   'Mall',   3400.00, 88000.00),
  ('norte',  'Norte',  3000.00, 76000.00),
  ('sur',    'Sur',    2400.00, 60000.00);

INSERT INTO categorias (nombre) VALUES
  ('Salas'), ('Colchones'), ('Comedores'), ('Dormitorio'), ('Complementos');

INSERT INTO productos (categoria_id, nombre, precio_promedio) VALUES
  ((SELECT id FROM categorias WHERE nombre='Salas'),        'Sofá 3 cuerpos Milano',       1450.00),
  ((SELECT id FROM categorias WHERE nombre='Salas'),        'Sofá esquinero Roma',         1890.00),
  ((SELECT id FROM categorias WHERE nombre='Colchones'),    'Colchón Queen Confort Plus',   980.00),
  ((SELECT id FROM categorias WHERE nombre='Colchones'),    'Colchón King Premium',        1320.00),
  ((SELECT id FROM categorias WHERE nombre='Comedores'),    'Comedor 6 sillas Nordic',     1150.00),
  ((SELECT id FROM categorias WHERE nombre='Dormitorio'),   'Placard 6 puertas',            890.00),
  ((SELECT id FROM categorias WHERE nombre='Dormitorio'),   'Cama box Queen',               760.00),
  ((SELECT id FROM categorias WHERE nombre='Complementos'), 'Mesa de luz par',              210.00),
  ((SELECT id FROM categorias WHERE nombre='Complementos'), 'Espejo decorativo',            140.00);

-- Sin ventas de ejemplo: cargar con un script real de POS o generar
-- manualmente filas de prueba en `ventas` para validar los endpoints.
