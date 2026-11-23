
# TecnoStock: modelo y diccionario de datos

## 1. Descripción

La base de datos de TecnoStock guardará la información necesaria para administrar el inventario. Se utilizarán cuatro tablas principales: `usuarios`, `categorias`, `productos` y `movimientos`.

Las tablas estarán relacionadas para evitar que se registren movimientos de productos o usuarios que no existan.

## 2. Tabla usuarios

Guarda los datos del encargado que puede ingresar al sistema.

| Campo | Tipo de dato | Clave o condición | Descripción |
|---|---|---|---|
| id_usuario | INT | PK, autoincremental | Identificador del usuario. |
| nombre | VARCHAR(100) | NOT NULL | Nombre del encargado. |
| correo | VARCHAR(150) | UNIQUE, NOT NULL | Correo utilizado para iniciar sesión. |
| clave_hash | VARCHAR(255) | NOT NULL | Contraseña almacenada mediante hash. |
| estado | TINYINT(1) | NOT NULL, valor inicial 1 | Indica si la cuenta está habilitada. |

## 3. Tabla categorias

Guarda los tipos de productos que se pueden utilizar para clasificar el inventario.

| Campo | Tipo de dato | Clave o condición | Descripción |
|---|---|---|---|
| id_categoria | INT | PK, autoincremental | Identificador de la categoría. |
| nombre | VARCHAR(80) | UNIQUE, NOT NULL | Nombre de la categoría. |
| descripcion | VARCHAR(255) | Puede quedar vacío | Descripción de la categoría. |
| estado | TINYINT(1) | NOT NULL, valor inicial 1 | Indica si la categoría está activa. |

## 4. Tabla productos

Guarda la información de cada producto registrado.

| Campo | Tipo de dato | Clave o condición | Descripción |
|---|---|---|---|
| id_producto | INT | PK, autoincremental | Identificador del producto. |
| id_categoria | INT | FK, NOT NULL | Categoría a la que pertenece. |
| codigo | VARCHAR(40) | UNIQUE, NOT NULL | Código único del producto. |
| nombre | VARCHAR(120) | NOT NULL | Nombre del producto. |
| descripcion | VARCHAR(500) | Puede quedar vacío | Descripción del producto. |
| precio | DECIMAL(10,2) | NOT NULL, no negativo | Precio del producto. |
| stock_actual | INT | NOT NULL, no negativo | Cantidad disponible. |
| stock_minimo | INT | NOT NULL, no negativo | Cantidad mínima para generar una alerta. |
| estado | TINYINT(1) | NOT NULL, valor inicial 1 | Indica si el producto está activo. |

## 5. Tabla movimientos

Guarda las entradas, salidas y ajustes que se realizan sobre los productos.

| Campo | Tipo de dato | Clave o condición | Descripción |
|---|---|---|---|
| id_movimiento | INT | PK, autoincremental | Identificador del movimiento. |
| id_producto | INT | FK, NOT NULL | Producto relacionado. |
| id_usuario | INT | FK, NOT NULL | Usuario que realizó el movimiento. |
| tipo | VARCHAR / ENUM | NOT NULL | Tipo de movimiento. |
| cantidad | INT | NOT NULL, mayor que cero | Unidades involucradas. |
| fecha_hora | DATETIME | NOT NULL | Fecha y hora del registro. |
| observacion | VARCHAR(255) | Puede quedar vacío | Comentario sobre el movimiento. |

Los tipos de movimiento serán:

- `ENTRADA`: aumenta el stock.
- `SALIDA`: disminuye el stock.
- `AJUSTE_ENTRADA`: registra un aumento manual del stock.
- `AJUSTE_SALIDA`: registra una disminución manual del stock.

## 6. Relaciones entre las tablas

| Relación | Tipo | Explicación |
|---|---|---|
| usuarios - movimientos | Uno a muchos (1:N) | Un usuario puede registrar varios movimientos. |
| categorias - productos | Uno a muchos (1:N) | Una categoría puede tener varios productos. |
| productos - movimientos | Uno a muchos (1:N) | Un producto puede tener varios movimientos. |

## 7. Reglas de integridad

1. Cada tabla tendrá una clave primaria para identificar sus registros.
2. El correo del usuario, el nombre de la categoría y el código del producto no se podrán repetir.
3. Las claves foráneas mantendrán relacionadas las tablas.
4. El precio, el stock actual y el stock mínimo no podrán ser negativos.
5. La cantidad de un movimiento deberá ser mayor que cero.
6. No se podrá registrar una salida superior al stock disponible.
7. Los productos con movimientos se desactivarán en vez de eliminarlos físicamente.
8. Cada movimiento deberá guardar el usuario responsable.
9. La actualización del stock y el registro del movimiento deberán completarse juntos. Si ocurre un error, se deberá cancelar la operación.

## 8. Uso de la información

La tabla `productos` mantendrá el stock actual para que pueda consultarse rápidamente. La tabla `movimientos` permitirá revisar cómo fue cambiando esa cantidad a lo largo del tiempo.

La alerta se calculará comparando el stock actual con el stock mínimo:

- Si el stock actual es menor que el mínimo, se mostrará una alerta.
- Si ambos valores son iguales, no se mostrará alerta.
- Si el stock actual es mayor que el mínimo, el producto estará sobre el mínimo.

