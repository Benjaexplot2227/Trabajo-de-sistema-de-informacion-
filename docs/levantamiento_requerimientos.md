
# TecnoStock: levantamiento de requerimientos

## 1. Situación actual

TecnoStock es un emprendimiento que necesita llevar un mejor control de sus productos. Actualmente utiliza planillas para registrar la información del inventario. Con este método pueden aparecer códigos repetidos, errores en las cantidades y diferencias entre el stock anotado y el que realmente está disponible.

Para mejorar esta situación se propone desarrollar un sistema de inventario que reúna la información de los productos y permita registrar sus entradas y salidas.

## 2. Objetivo general

Desarrollar una aplicación web local para registrar y consultar productos, controlar las existencias y mantener un registro de los movimientos realizados.

## 3. Objetivos específicos

- Mantener un catálogo con los datos de cada producto.
- Facilitar la búsqueda de productos por nombre, código o categoría.
- Registrar las entradas y salidas de unidades.
- Mantener actualizado el stock después de cada movimiento.
- Identificar los productos que estén por debajo de su stock mínimo.
- Conservar los movimientos realizados para poder revisarlos posteriormente.

## 4. Usuario

El sistema será utilizado por un encargado de inventario. Para ingresar deberá contar con una cuenta registrada. Esta persona podrá administrar el catálogo y registrar los movimientos de los productos.

## 5. Alcance

El proyecto considera las funciones necesarias para llevar el inventario:

- Inicio y cierre de sesión.
- Registro, consulta, búsqueda y modificación de productos.
- Desactivación de productos que ya no se comercialicen.
- Clasificación de productos mediante categorías.
- Registro de entradas, salidas y ajustes de stock.
- Consulta de existencias y alertas de stock bajo.
- Consulta del historial de movimientos.

La aplicación se ejecutará de manera local. No se consideran ventas, facturación, sucursales ni conexión con servicios externos, porque no son parte del problema planteado.

## 6. Requerimientos funcionales

| Código | Requerimiento |
|---|---|
| RF-01 | El sistema permitirá iniciar y cerrar sesión. |
| RF-02 | Permitirá registrar productos con código, nombre, categoría, descripción, precio, stock actual y stock mínimo. |
| RF-03 | Permitirá consultar un listado de productos. |
| RF-04 | Permitirá buscar productos por nombre, código o categoría. |
| RF-05 | Permitirá modificar los datos de un producto y ajustar sus cantidades. |
| RF-06 | Permitirá desactivar productos que ya no se comercialicen. |
| RF-07 | Permitirá registrar entradas y salidas de inventario. |
| RF-08 | Actualizará el stock después de guardar un movimiento válido. |
| RF-09 | Mostrará una alerta cuando el stock actual sea menor que el stock mínimo. |
| RF-10 | Validará los datos antes de guardarlos. |

## 7. Requerimientos no funcionales

| Código | Requerimiento |
|---|---|
| RNF-01 | La aplicación funcionará localmente mediante Apache. |
| RNF-02 | Se utilizará PHP 8 para procesar las operaciones del sistema. |
| RNF-03 | Se utilizará MySQL o MariaDB para guardar la información. |
| RNF-04 | PHP se conectará a la base de datos mediante PDO. |
| RNF-05 | La interfaz se desarrollará con HTML, Bootstrap y JavaScript. |
| RNF-06 | Las pantallas y mensajes deberán ser claros y fáciles de entender. |
| RNF-07 | Los datos deberán validarse antes de guardarse en la base de datos. |

## 8. Reglas de negocio

1. El código de cada producto debe ser único.
2. El precio, el stock actual y el stock mínimo no pueden ser negativos.
3. Cada producto debe estar asociado a una categoría.
4. La cantidad de un movimiento debe ser mayor que cero.
5. No se permitirá registrar una salida mayor que el stock disponible.
6. Cada movimiento debe guardar la fecha, el tipo, la cantidad, el producto y el usuario responsable.
7. Una entrada aumenta el stock y una salida lo disminuye.
8. Los productos que ya no se comercialicen se desactivarán; no se borrarán físicamente si tienen movimientos registrados.
9. La alerta de stock bajo aparecerá solamente cuando el stock actual sea menor que el stock mínimo.
10. Si se cambia manualmente la cantidad de un producto, el cambio también deberá quedar registrado como un ajuste.

## 9. Herramientas y tecnologías

| Tecnología o herramienta | Uso en el proyecto |
|---|---|
| PHP 8 | Programar las operaciones del sistema. |
| MySQL o MariaDB | Guardar los usuarios, categorías, productos y movimientos. |
| PDO | Conectar PHP con la base de datos y ejecutar consultas. |
| HTML | Crear la estructura de las páginas. |
| Bootstrap | Dar formato a los formularios, tablas y menús. |
| JavaScript | Apoyar las validaciones y algunas acciones de la interfaz. |
| Apache | Servir la aplicación en el computador. |
| XAMPP | Ejecutar Apache, PHP y MySQL/MariaDB en el entorno local. |
| Visual Studio Code | Editar los archivos del proyecto. |

## 10. Resultado esperado

Al terminar, el encargado podrá registrar y consultar productos, realizar movimientos y revisar las existencias desde una sola aplicación. También podrá identificar los productos que necesiten reposición y consultar los movimientos anteriores.
