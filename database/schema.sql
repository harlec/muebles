-- Esquema Dashboard Muebles & Estilo
-- Basado en design_handoff_dashboard_multilocal/README.md ("Datos que necesita la API real")

CREATE DATABASE IF NOT EXISTS muebles_dashboard
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE muebles_dashboard;

CREATE TABLE locales (
  id            VARCHAR(20)     NOT NULL PRIMARY KEY,   -- centro | mall | norte | sur
  nombre        VARCHAR(60)     NOT NULL,
  meta_diaria   DECIMAL(10,2)   NOT NULL,
  meta_mensual  DECIMAL(12,2)   NOT NULL,
  activo        TINYINT(1)      NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id     INT UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(60)     NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE productos (
  id               INT UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY,
  categoria_id     INT UNSIGNED    NOT NULL,
  nombre           VARCHAR(120)    NOT NULL,
  precio_promedio  DECIMAL(10,2)   NOT NULL,
  activo           TINYINT(1)      NOT NULL DEFAULT 1,
  CONSTRAINT fk_productos_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- Cada fila es una venta individual (un ticket). El monto y la hora ya
-- permiten derivar: ventas por hora, por día, por mes, mix de pago,
-- feed en vivo y rotación de producto (agregando por producto en N días).
CREATE TABLE ventas (
  id             BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  local_id       VARCHAR(20)      NOT NULL,
  producto_id    INT UNSIGNED     NOT NULL,
  metodo_pago    ENUM('tarjeta_credito','credito_directo','efectivo','transferencia') NOT NULL,
  monto          DECIMAL(10,2)    NOT NULL,
  fecha          DATE             NOT NULL,   -- fecha de la venta (zona horaria del negocio)
  hora           TINYINT UNSIGNED NOT NULL,   -- 10..20, hora de apertura de tienda
  creado_en      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ventas_local
    FOREIGN KEY (local_id) REFERENCES locales(id),
  CONSTRAINT fk_ventas_producto
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- Consultas frecuentes: ventas de hoy/mes por local, feed en vivo (orden por creado_en)
CREATE INDEX idx_ventas_local_fecha ON ventas (local_id, fecha);
CREATE INDEX idx_ventas_fecha_hora ON ventas (fecha, hora);
CREATE INDEX idx_ventas_creado_en ON ventas (creado_en);
CREATE INDEX idx_ventas_producto_fecha ON ventas (producto_id, fecha);
