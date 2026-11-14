CREATE DATABASE IF NOT EXISTS tecnostock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tecnostock;

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  clave_hash VARCHAR(255) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  descripcion VARCHAR(255) NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
  id_producto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(40) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  descripcion VARCHAR(500) NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock_actual INT UNSIGNED NOT NULL DEFAULT 0,
  stock_minimo INT UNSIGNED NOT NULL DEFAULT 0,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  id_categoria INT UNSIGNED NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_productos_precio CHECK (precio >= 0),
  CONSTRAINT chk_productos_stock CHECK (stock_actual >= 0),
  CONSTRAINT chk_productos_minimo CHECK (stock_minimo >= 0),
  CONSTRAINT fk_productos_categoria FOREIGN KEY (id_categoria)
    REFERENCES categorias (id_categoria)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_productos_nombre (nombre),
  INDEX idx_productos_estado_stock (estado, stock_actual, stock_minimo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimientos (
  id_movimiento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo ENUM('ENTRADA', 'SALIDA', 'AJUSTE_ENTRADA', 'AJUSTE_SALIDA') NOT NULL,
  cantidad INT UNSIGNED NOT NULL,
  observacion VARCHAR(255) NULL,
  id_producto INT UNSIGNED NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  CONSTRAINT chk_movimientos_cantidad CHECK (cantidad > 0),
  CONSTRAINT fk_movimientos_producto FOREIGN KEY (id_producto)
    REFERENCES productos (id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_movimientos_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_movimientos_fecha (fecha_hora),
  INDEX idx_movimientos_producto (id_producto)
) ENGINE=InnoDB;

INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
  ('Accesorios', 'Accesorios tecnológicos generales'),
  ('Audio', 'Audífonos, parlantes y accesorios de audio'),
  ('Cables', 'Cables y adaptadores'),
  ('Computación', 'Periféricos y accesorios de computación');

