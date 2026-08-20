# Productos CRUD — CodeIgniter 3

CRUD de productos (crear, listar, editar, eliminar) hecho con CodeIgniter 3
+ Query Builder + vistas PHP nativas, usando MySQL/MariaDB como base de
datos y Bootstrap 5 (vía CDN) para el estilo. Es el mismo CRUD y la misma
tabla `productos` que la versión de Laravel (`../laravel-productos-crud`)
— sirve para comparar cómo resuelve cada framework exactamente lo mismo.

## Requisitos

- PHP 8.1 o superior (probado también en 8.5)
- Composer
- MySQL o MariaDB corriendo localmente (o accesible por red)

## 1. Instalar dependencias

Si clonaste el proyecto sin la carpeta `vendor/`, instala las dependencias
con Composer (incluye el propio núcleo de CodeIgniter vía
`codeigniter/framework` y `vlucas/phpdotenv` para las variables de
entorno):

```bash
composer install
```

## 2. Configurar las variables de entorno (`.env`)

A diferencia de la instalación estándar de CodeIgniter 3 (que trae los
datos de la BD escritos directamente en `application/config/database.php`),
este proyecto se modificó para leer la configuración desde un archivo
`.env` en la raíz — el mismo enfoque que usa Laravel. La carga ocurre en
`index.php` (busca `.env` y lo carga con `vlucas/phpdotenv` antes de que
arranque el framework).

Copia la plantilla y edítala con los datos de **tu** MySQL/MariaDB local:

```bash
cp .env.example .env
```

```env
DB_HOSTNAME=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=productos_crud
```

`application/config/database.php` ya está modificado para leer estas
variables con `getenv()`; si no existe `.env`, cae en los valores por
defecto (`localhost` / usuario y contraseña vacíos).

## 3. Crear la base de datos y la tabla

Importa el script incluido, que crea la base de datos y la tabla
`productos` (misma estructura que la versión Laravel):

```bash
mysql -u root -p < database/schema.sql
```

O ejecuta manualmente:

```sql
CREATE DATABASE IF NOT EXISTS productos_crud;
USE productos_crud;

CREATE TABLE IF NOT EXISTS productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);
```

## 4. Levantar el servidor

```bash
php -S localhost:8000
```

Abre
[http://localhost:8000/index.php/productos](http://localhost:8000/index.php/productos)
en el navegador.

> CodeIgniter 3 no reescribe la URL para quitar `index.php` a menos que
> configures un `.htaccess` con mod_rewrite (como harías en un hosting
> real con Apache). Para este demo local con el servidor embebido de PHP,
> `index.php` en la URL es lo esperado.

## Estructura relevante

- `application/models/Producto_model.php` — Query Builder (`insert`,
  `update`, `delete`, `get`).
- `application/controllers/Productos.php` — controlador con las acciones
  del CRUD (`index`, `crear`, `editar`, `eliminar`).
- `application/views/productos/` — vistas (`index`, `crear`, `editar`).
- `application/views/templates/` — `header`/`footer` compartidos con
  Bootstrap 5 vía CDN.
- `application/config/routes.php` — `productos` como controlador por
  defecto.
- `database/schema.sql` — script de creación de la base de datos y tabla.

## Notas

- El código no usa tipado estricto en los métodos del modelo/controlador
  a propósito — es tipado ligero, como se ve típicamente en proyectos
  CodeIgniter 3.
- Los formularios usan los helpers de formulario de CI3 (`form_open`,
  `form_input`, `form_label`) y la librería `form_validation`, en vez de
  HTML plano — es el equivalente conceptual del `FormRequest` de Laravel.
- Se activó `$config['csrf_protection'] = TRUE` en
  `application/config/config.php` para que los formularios incluyan token
  CSRF automáticamente (vía `form_open()`), igual que `@csrf` en Blade.
- CodeIgniter 3 es un framework legado que predata PHP 8.2: en PHP 8.2+
  vas a ver avisos de "deprecated" por el uso de propiedades dinámicas en
  su núcleo. Ya se ajustó `index.php` para ocultar ese ruido puntual en el
  entorno `development` (`error_reporting(E_ALL & ~E_DEPRECATED)`) — el
  resto de errores sigue mostrándose con normalidad.
- Si ves "A Database Error Occurred — Unable to connect to your database
  server", es porque MySQL/MariaDB no está corriendo o los datos del
  `.env` no coinciden con tu instalación local — no es un bug del CRUD.
- Al correr `composer install` puede aparecer un error inofensivo de un
  script (`sed: invalid command code v`) al instalar una dependencia de
  testing (`vfsstream`) — es un script pensado para Linux/GNU `sed` que
  falla en macOS (BSD `sed`); no afecta a la aplicación en sí.

## 🧪 Ejercicio de práctica — antes de arrancar el proyecto

**Modalidad**: por equipo — para los equipos a los que les tocó **CodeIgniter 3**
en el proyecto integrador "Oficina del Agua".
**Entrega**: sábado 22 de agosto (antes de pasar a la Semana 2 — desarrollo del
proyecto).

### Objetivo

Esta carpeta es el ejemplo, no el ejercicio. La prueba de concepto es que tu
equipo tome esta misma estructura como plantilla y construya, desde cero, un
CRUD completo (crear, listar, editar, eliminar) para una **entidad distinta**
de `productos` — para practicar el framework con las manos antes de que el
esfuerzo real se vaya al proyecto.

### Qué tabla elegir

Elige una entidad simple relacionada con "Oficina del Agua" — no hace falta
que sea una de las tablas finales del proyecto ni que el modelo de datos sea
definitivo, solo que sirva para practicar el flujo completo. Por ejemplo:
`clientes`, `contadores` o `tarifas`.

### Pasos (siguiendo el mismo patrón que `productos`)

1. Diseña la tabla nueva y agrégala a un script SQL propio (por ejemplo
   `database/schema_<entidad>.sql`).
2. **Modelo**: crea `application/models/<Entidad>_model.php` usando Query
   Builder (`insert`, `update`, `delete`, `get`) — copia
   `Producto_model.php` como punto de partida y adapta los campos.
3. **Controlador**: crea `application/controllers/<Entidad>.php` con las
   mismas acciones que `Productos.php` (`index`, `crear`, `editar`,
   `eliminar`).
4. **Vistas**: crea `application/views/<entidad>/` con `index`, `crear` y
   `editar`, reutilizando `application/views/templates/header` y `footer`
   para mantener el mismo look con Bootstrap 5.
5. **Ruta**: accede por `index.php/<entidad>`, o agrégala en
   `application/config/routes.php` si quieres que sea la ruta por defecto.
6. Prueba las 4 operaciones en el navegador contra tu MySQL/MariaDB local
   antes de dar por terminado.

### Requisitos técnicos

- MySQL/MariaDB real (no arrays en memoria ni datos hardcodeados).
- Reutiliza los helpers de formulario de CI3 (`form_open`, `form_input`) y
  la librería `form_validation` — igual que en el ejemplo, nada de HTML de
  formulario sin validar en el servidor.
- Al menos una validación real de servidor (campo requerido, tipo numérico,
  longitud, etc.).
- Mantén `$config['csrf_protection']` activo — los formularios deben seguir
  incluyendo el token vía `form_open()`.

### Entregables

- La carpeta del CRUD funcionando (o el commit/rama correspondiente si ya
  vive en el repositorio del equipo).
- Captura de pantalla de las 4 operaciones (listar, crear, editar, eliminar)
  funcionando contra MySQL/MariaDB.
