# codeigniter-productos-crud

## Nombre del proyecto

`codeigniter-productos-crud` es un CRUD para agregar, editar y eliminar productos, conectado a una base de datos MySQL ejecutada localmente mediante Laragon. Es un proyecto de aprendizaje, usado para familiarizarse con el flujo y la estructura de CodeIgniter 3 antes de iniciar el proyecto integrador del módulo.

## Stack utilizado

CodeIgniter 3, PHP 8.3, MySQL, Composer, `vlucas/phpdotenv` y Bootstrap 5.

## Requisitos

- Laragon (incluye Apache, MySQL y PHP) — alternativa válida: XAMPP
- Composer
- Git
- PHP 8.3
- MySQL

## Instalación

1. Clonar el repositorio:
   ```bash
   git clone <url>
   ```
2. Instalar dependencias con Composer:
   ```bash
   composer install --no-dev
   ```

## Configuración

1. Copiar el archivo de ejemplo:
   ```bash
   cp .env.example .env
   ```

2. Editar `.env` con los datos de tu base de datos local:
   ```env
   DB_HOSTNAME=localhost
   DB_USERNAME=root
   DB_PASSWORD=<contraseña>
   DB_DATABASE=productos_crud
   ```

   - `DB_HOSTNAME`: dirección del servidor de base de datos (en desarrollo local, siempre `localhost`).
   - `DB_USERNAME`: usuario de MySQL (por defecto en Laragon, `root`).
   - `DB_PASSWORD`: contraseña de ese usuario en tu instalación local. Nota: por un conflicto de MySQL 8.4 con el plugin de autenticación `mysql_native_password`, puede ser necesario asignarle una contraseña explícita al usuario `root` en vez de dejarla vacía (ver sección "Problemas encontrados").
   - `DB_DATABASE`: nombre de la base de datos que usará la aplicación.

## Base de datos

1. Abrir el panel **"Base de Datos"** desde el menú principal de Laragon — esto abre HeidiSQL, ya conectado a tu MySQL local.
2. En HeidiSQL: **Archivo → Cargar SQL**, y seleccionar el archivo `database/schema.sql` del proyecto.
3. Ejecutar el script completo con el botón de ejecutar (o la tecla **F9**).

El script (`database/schema.sql`) contiene:

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

## Ejecución

1. Desde la raíz del proyecto, levantar el servidor de desarrollo de PHP:
   ```bash
   php -S localhost:8000
   ```

2. Abrir en el navegador:
   ```
   http://localhost:8000/index.php/productos
   ```

## Problemas encontrados

### Problema 1: Conflicto de Composer con PHP 8.4

**Síntoma**: al correr `composer install`, apareció el error:
```
doctrine/instantiator 2.1.0 requires php ^8.4 -> your php version (8.3.30) does not satisfy that requirement.
```

**Causa**: una dependencia de **desarrollo** (`doctrine/instantiator`, usada internamente por `phpunit` para pruebas automatizadas del framework) exigía PHP 8.4, mientras que la versión instalada localmente era PHP 8.3.30. Esta dependencia no es necesaria para que la aplicación funcione — solo se usaría si se quisieran correr pruebas automatizadas del propio CodeIgniter.

**Solución**: instalar las dependencias omitiendo las de desarrollo:
```bash
composer install --no-dev
```

---

### Problema 2: Error de conexión a la base de datos (`mysql_native_password`)

**Síntoma**: al abrir la aplicación, aparecía:
```
mysqli::real_connect(): (HY000/1524): Plugin 'mysql_native_password' is not loaded
A Database Error Occurred — Unable to connect to your database server
```

**Causa**: este problema tuvo en realidad **dos capas**, descubiertas de forma iterativa:

1. MySQL 8.4 (la versión instalada vía Laragon) eliminó el plugin de autenticación antiguo `mysql_native_password`, y el usuario `root` creado por Laragon todavía estaba configurado para usarlo.
2. Después de corregir el plugin del usuario, el error persistía exactamente igual — lo cual reveló la causa real y más profunda: la librería `vlucas/phpdotenv` (instalada vía Composer) en su versión moderna **ya no llama a `putenv()`** por defecto, solo carga las variables en `$_ENV`/`$_SERVER`. Como `application/config/database.php` leía la configuración únicamente con `getenv()`, siempre recibía valores vacíos — la aplicación intentaba conectarse con un usuario vacío (`''@'localhost'`), lo cual disparaba el mismo mensaje de error de plugin.

Este segundo punto se diagnosticó creando un script de prueba aislado (`test_db.php`) que confirmó, paso a paso, que `Dotenv` sí leía el archivo `.env` correctamente (`$dotenv->load()` devolvía el array con los valores correctos), pero `getenv()` seguía devolviendo vacío.

**Solución**:
1. Actualizar el plugin de autenticación del usuario `root` en MySQL:
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'tu_contraseña';
   FLUSH PRIVILEGES;
   ```
2. Modificar `application/config/database.php` para usar `$_ENV` como respaldo cuando `getenv()` no devuelve nada:
   ```php
   'hostname' => getenv('DB_HOSTNAME') ?: ($_ENV['DB_HOSTNAME'] ?? 'localhost'),
   'username' => getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? ''),
   'password' => getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? ''),
   'database' => getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? ''),
   ```

---

### Problema 3: Los enlaces de navegación llevaban a una URL incorrecta

**Síntoma**: al hacer clic en el botón "Nuevo producto", el navegador iba a `http://localhost/productos/crear` (sin el puerto `:8000` ni `index.php`) en vez de `http://localhost:8000/index.php/productos/crear`, resultando en un error 404 de Apache.

**Causa**: dos problemas combinados en la configuración de URLs de CodeIgniter 3:

1. `$config['base_url']` estaba vacío, así que CodeIgniter intentaba autodetectarlo usando `$_SERVER['SERVER_ADDR']` (la IP del servidor), un método que **nunca incluye el puerto** de la petición actual.
2. Las vistas usaban la función `base_url('productos/crear')` para generar el enlace, pero `base_url()` **no agrega automáticamente** `index.php` a la URL — a diferencia de `site_url()`, que sí lo hace.

**Solución**:
1. Definir explícitamente la URL base en `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost:8000/';
   ```
2. Reemplazar las llamadas a `base_url()` por `site_url()` en los enlaces de navegación de las vistas (`templates/header.php`, `productos/index.php`, `productos/crear.php`, `productos/editar.php`).

## Buenas prácticas investigadas

Fuentes: [Guía de seguridad oficial de CodeIgniter 3](https://codeigniter.com/userguide3/general/security.html) y [Guía de estilo oficial de CodeIgniter 3](https://codeigniter.com/userguide3/general/styleguide.html).

1. **Nombres de variables con sentido, en snake_case.** La guía de estilo de CI3 recomienda variables en minúsculas, separadas por guion bajo (`$group_id`, no `$groupid`), evitando nombres cortos o sin significado salvo en iteradores de bucles. Facilita que cualquiera entienda qué guarda cada variable sin tener que rastrear el código.

2. **Separación de responsabilidades (MVC): el modelo es el único que interactúa con la base de datos.** Los controladores deben ser "flacos" — reciben la petición, delegan la lógica de datos al modelo, y deciden qué vista mostrar, sin tener SQL ni lógica de negocio compleja dentro. Esto hace que el código sea más fácil de mantener y probar, porque cada capa tiene una única responsabilidad.

3. **Guardar los datos sensibles en variables de entorno (`.env`), nunca en el código versionado.** Aunque no es una práctica nativa de CI3 (se agregó a este proyecto con `vlucas/phpdotenv`), es un estándar de la industria: mantiene las credenciales fuera del repositorio de Git, y permite que cada entorno (desarrollo, pruebas, producción) tenga su propia configuración sin tocar el código fuente.

4. **Validar y escapar toda entrada del usuario antes de usarla.** La guía de seguridad de CI3 recomienda un enfoque de tres pasos ante cualquier dato externo (formularios, cookies, URL): filtrarlo, validarlo y escaparlo antes de enviarlo a la base de datos. En este proyecto se aplica con la librería `form_validation` en los controladores y con `html_escape()` al mostrar datos en las vistas.

5. **Ocultar errores detallados en producción.** CI3 permite controlar esto con la constante `ENVIRONMENT` en `index.php`: en `'development'` se muestran los errores completos, pero en `'production'` deben ocultarse, ya que un mensaje de error de PHP puede revelar rutas del servidor, nombres de tablas u otra información sensible.

## Reflexión técnica

**1. ¿Qué fue lo que más te costó entender del framework?**

Su estructura: al ver tantas carpetas (`controllers`, `models`, `views`, `config`, `system`, etc.) se ve complicado establecer la conexión entre todo y que fluya de forma ordenada. Requiere bastante entendimiento, porque si una parte no funciona correctamente puede afectar a las demás.

**2. ¿Qué parte de la estructura del proyecto te pareció más importante?**

El patrón MVC en sí — es lo que le da vida al proyecto. Me pareció fundamental la regla de que solo el modelo debe interactuar con la base de datos, mientras que el controlador actúa como puente entre el modelo y las vistas.

**3. Explica con tus propias palabras cómo funciona una petición desde que el usuario realiza una acción hasta que obtiene una respuesta.**

La petición arranca cuando el usuario hace clic en algo o entra a una URL (por ejemplo, `/productos/editar/3`). CodeIgniter interpreta esa URL como ruta y decide a qué controlador y método llamar. El controlador es quien realmente "manda": si hay datos enviados por el usuario, primero los valida; si necesita datos de la base de datos, se los pide al modelo, que es el único que habla directamente con la BD. Una vez el controlador tiene todo lo que necesita, se lo entrega a una vista, que arma el HTML final que el usuario ve en pantalla. La vista nunca llama a nada por su cuenta — solo recibe datos ya listos del controlador.

**4. Menciona al menos 3 buenas prácticas que encontraste y explica por qué son importantes.**

- **Nombres de variables con sentido, en snake_case**: facilita que cualquiera entienda qué guarda cada variable sin tener que rastrear el código.
- **Separación de responsabilidades (MVC)**: el modelo es el único que interactúa con la base de datos; esto hace que el código sea más fácil de mantener y probar.
- **Guardar datos sensibles en variables de entorno**: credenciales como usuario y contraseña de la base de datos se guardan en `.env`, nunca en el código versionado en Git.

**5. Menciona al menos un problema técnico que encontraste y explica cómo lo solucionaste.**

El "Error de conexión a la base de datos": los valores del `.env` no estaban siendo leídos por PHP (`getenv()` regresaba vacío). Se solucionó en dos pasos: primero corrigiendo el plugin de autenticación del usuario `root` en MySQL, y luego modificando `database.php` para que usara `$_ENV` como respaldo cuando `getenv()` no devuelve nada.

**6. ¿Qué aprendiste durante esta actividad que consideras que te será útil para el proyecto del módulo?**

Esta actividad sirve como base para el proyecto: quedó muy claro qué hacer a la hora de modificar o agregar un nuevo CRUD — por ejemplo, con el ejercicio de `tarifas`, usé como base lo que ya existía en `productos` y lo adapté. Me quedo con el orden lógico para construir un CRUD nuevo: primero la base de datos (la tabla), luego el modelo, después el controlador, y por último las vistas — ese fue el orden que seguí para adaptar el ejercicio de `tarifas`.