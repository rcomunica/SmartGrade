# Guia de exposicion: SmartGrade

## 1. Proposito de este documento

Este documento sirve como material de apoyo para explicar el proyecto de grado SmartGrade: que problema resuelve, como esta organizado, como viajan los datos, que funciones se utilizan en las consultas `READ`, que devuelve cada una y cuales son los puntos tecnicos mas importantes para comentar ante los profesores.

La idea central para presentar el proyecto es:

> SmartGrade es una aplicacion web de gestion academica que permite a cada estudiante administrar sus materias, periodos, notas y metas, consultar su rendimiento y generar un reporte academico asistido por IA.

---

## 2. Resumen para decir al inicio

SmartGrade busca centralizar la informacion academica del estudiante en un solo lugar. El usuario crea una cuenta o inicia sesion y, desde el dashboard, puede:

- Registrar materias y el docente de cada materia.
- Crear periodos academicos y marcar uno como activo.
- Registrar notas con su porcentaje o peso.
- Ver promedios por periodo y por materia.
- Crear metas academicas asociadas a una materia.
- Editar y eliminar los registros creados.
- Generar un reporte de rendimiento mediante OpenAI.

La aplicacion esta construida principalmente con PHP procedural, MySQL y PDO. Las vistas generan HTML y utilizan Tailwind CSS junto con una hoja de estilos propia. La informacion se protege mediante sesiones y cada consulta filtra los registros por el usuario autenticado.

---

## 3. Tecnologias y papel de cada una

| Tecnologia   | Uso en el proyecto                                                             |
| ------------ | ------------------------------------------------------------------------------ |
| PHP          | Logica del servidor, validaciones, sesiones, consultas y generacion de vistas. |
| MySQL        | Persistencia de usuarios, materias, periodos, notas y metas.                   |
| PDO          | Conexion a MySQL y ejecucion de consultas preparadas.                          |
| HTML         | Estructura de las pantallas y formularios.                                     |
| Tailwind CSS | Clases utilitarias usadas en las vistas.                                       |
| CSS propio   | Componentes visuales, colores, tarjetas, botones y layout.                     |
| Vendor       | Gestion de dependencias PHP.                                                   |
| Parsedown    | Conversion de la respuesta Markdown de la IA a HTML.                           |
| OpenAI API   | Generacion del reporte narrativo del rendimiento academico.                    |

La dependencia externa visible en `composer.json` es `erusev/parsedown`.

---

## 4. Arquitectura de carpetas

### `views/`

Contiene las pantallas que ve el usuario:

- `login.php`: formulario de inicio de sesion.
- `register.php`: formulario de registro.
- `dashboard.php`: resumen del rendimiento y acceso al reporte de IA.
- `subjets/index.php`: gestion de materias.
- `terms/index.php`: gestion de periodos.
- `grades/index.php`: gestion y agrupacion de notas.
- `goals/index.php`: gestion de metas.
- `edit.php`: formularios para editar registros.
- `partials/`: componentes reutilizables como barra lateral y barra superior.

El nombre `subjets` aparece asi en el codigo y en la base de datos. Aunque en ingles correcto seria `subjects`, se debe conservar el nombre actual para no romper las referencias existentes.

### `logic/`

Contiene la logica de negocio y los endpoints que reciben formularios:

- `db.php`: crea la conexion PDO.
- `auth.php`: procesa el inicio de sesion.
- `register.php`: procesa el registro de usuarios.
- `logout.php`: cierra la sesion.
- `dashboard.php`: contiene funciones para los indicadores del dashboard.
- `connectAi.php`: prepara los datos y solicita el reporte a OpenAI.
- `subjets/`, `terms/`, `grades/` y `goals/`: contienen `create.php`, `read.php`, `update.php` y `delete.php`.

### `database/`

`database.sql` define las tablas, relaciones y datos de prueba.

### `vendor/`

Contiene las dependencias instaladas por Composer, entre ellas Parsedown.

---

## 5. Modelo de datos

La base de datos tiene estas entidades principales:

### `users`

Representa a los estudiantes registrados. Incluye nombre, apellido, correo, contrasena cifrada y fecha de creacion.

### `subjets`

Representa las materias. Cada materia pertenece a un usuario mediante `user_id` y tiene nombre, docente y fecha de registro.

### `terms`

Representa los periodos academicos. Cada periodo pertenece a un usuario y puede estar activo mediante `is_active`.

### `grades`

Representa las notas. Una nota pertenece a una materia, un periodo y un usuario. Guarda nombre, valor, porcentaje y fechas de creacion/actualizacion.

### `goals`

Representa las metas academicas. Una meta pertenece a un usuario y a una materia. Guarda nombre, descripcion, fecha objetivo y estado: `pending`, `in_progress` o `completed`.

### Relaciones importantes

- Un usuario puede tener muchas materias, periodos, notas y metas.
- Una materia puede tener muchas notas y metas.
- Un periodo puede tener muchas notas.
- Si se elimina un usuario, sus registros relacionados se eliminan por `ON DELETE CASCADE`.
- Si se elimina una materia, sus notas y metas relacionadas tambien pueden eliminarse por las relaciones en cascada.

---

## 6. Flujo general de la aplicacion

1. El usuario entra a una vista, por ejemplo `views/grades/index.php`.
2. La vista inicia o recupera la sesion.
3. Se verifica que exista `$_SESSION['user_id']`.
4. La vista incluye `logic/db.php` para obtener `$pdo`.
5. La vista incluye el `read.php` del modulo correspondiente.
6. Se llama a una funcion como `getUserGrades($pdo, $user_id)`.
7. La funcion prepara una consulta SQL con `PDO::prepare()`.
8. Se ejecutan los parametros con `execute()`.
9. Se devuelven filas como arreglos asociativos mediante `fetch()` o `fetchAll()`.
10. La vista recorre esos arreglos y los imprime con `htmlspecialchars()`.

Para crear, editar o eliminar, el flujo cambia ligeramente:

1. El formulario envia una peticion `POST` al endpoint de logica.
2. El endpoint valida el metodo, la sesion y los datos recibidos.
3. Consulta nuevamente que los ids pertenezcan al usuario actual.
4. Ejecuta `INSERT`, `UPDATE` o `DELETE` con una consulta preparada.
5. Guarda mensajes en la sesion.
6. Redirige a la vista usando el patron POST/Redirect/GET.

Este patron evita que una actualizacion de pagina vuelva a enviar el formulario y permite mostrar mensajes de exito o error despues de la redireccion.

---

## 7. Autenticacion y seguridad implementada

### Registro

`logic/register.php`:

1. Solo acepta solicitudes `POST`.
2. Lee nombre, apellido, correo y contrasenas.
3. Valida campos obligatorios, formato del correo, longitud minima de 8 caracteres y coincidencia de contrasenas.
4. Consulta si el correo ya existe.
5. Cifra la contrasena con `password_hash()`.
6. Inserta el usuario usando una consulta preparada.
7. Inicia automaticamente la sesion y redirige al dashboard.

### Inicio de sesion

`logic/auth.php`:

1. Solo acepta `POST`.
2. Valida que correo y contrasena no esten vacios.
3. Verifica el formato del correo.
4. Busca el usuario por correo.
5. Comprueba la contrasena con `password_verify()`.
6. Regenera el id de sesion con `session_regenerate_id(true)`.
7. Guarda en sesion el id, nombre y correo.
8. Redirige al dashboard.

El mensaje de error es generico: no indica si fallo el correo o la contrasena. Esto evita revelar informacion sobre las cuentas existentes.

### Proteccion por usuario

Cada pantalla privada verifica `$_SESSION['user_id']`. Ademas, las funciones de lectura reciben el `user_id` y lo incluyen en la consulta SQL. No se debe confiar solamente en el id enviado por el navegador.

### Escape de salida

Las vistas usan `htmlspecialchars()` al imprimir informacion proveniente de la base de datos. Esto reduce el riesgo de insertar HTML o JavaScript no deseado en la pagina.

---

## 8. Funciones de los archivos `READ`

Los archivos `read.php` no imprimen la pagina ni devuelven JSON por si mismos. Definen funciones de consulta. Sus retornos son arreglos PHP:

- `array`: lista de registros, posiblemente vacia.
- `?array`: un registro o `null` si no existe.
- `float`: un total numerico calculado.

Todas las funciones reciben una conexion PDO y, salvo el total de porcentajes, tambien reciben el id del usuario para aislar los datos.

## 8.1 `logic/subjets/read.php`

### `getUserSubjets(PDO $pdo, int $user_id): array`

**Proposito:** obtener todas las materias del usuario.

**Pasos:**

1. Prepara un `SELECT` sobre `subjets`.
2. Selecciona `id`, `name`, `teacher_name` y `created_at`.
3. Filtra por `user_id`.
4. Ordena por fecha de creacion descendente, mostrando primero las mas recientes.
5. Ejecuta la consulta.
6. Devuelve todas las filas con `fetchAll()`.

**Devuelve:** un arreglo de materias. Si no hay materias, devuelve `[]`.

**Uso principal:** `views/subjets/index.php`, donde el resultado se muestra en la tabla de materias.

### `getUserSubjetById(PDO $pdo, int $user_id, int $id): ?array`

**Proposito:** obtener una materia especifica y comprobar que pertenezca al usuario.

**Pasos:**

1. Busca por el `id` de la materia.
2. Agrega `user_id` como condicion de propiedad.
3. Limita el resultado a una fila.
4. Usa `fetch()`.
5. Devuelve el registro o `null` si no existe o no pertenece al usuario.

**Uso principal:** validacion al crear o editar notas y carga del formulario de edicion de materias.

---

## 8.2 `logic/terms/read.php`

### `getUserTerms(PDO $pdo, int $user_id): array`

**Proposito:** listar los periodos academicos del usuario.

**Pasos:**

1. Consulta la tabla `terms`.
2. Selecciona `id`, `name`, `is_active` y `created_at`.
3. Filtra por `user_id`.
4. Ordena por `created_at DESC`.
5. Ejecuta y obtiene todas las filas.

**Devuelve:** un arreglo de periodos. Cada elemento contiene tambien si esta activo.

**Uso principal:** `views/terms/index.php`.

### `getUserTermById(PDO $pdo, int $user_id, int $id): ?array`

**Proposito:** obtener un periodo especifico del usuario.

**Pasos:**

1. Filtra por id del periodo y por id del usuario.
2. Limita a un registro.
3. Ejecuta la consulta.
4. Devuelve el periodo o `null`.

**Uso principal:** validar que un periodo seleccionado al crear o editar una nota pertenece a la cuenta actual.

---

## 8.3 `logic/grades/read.php`

### `getUserSubjetsOptions(PDO $pdo, int $user_id): array`

**Proposito:** obtener solo `id` y `name` de las materias para llenar el selector del formulario de notas.

**Orden:** alfabetico por nombre.

**Devuelve:** lista de opciones o `[]`.

### `getUserTermsOptions(PDO $pdo, int $user_id): array`

**Proposito:** obtener `id` y `name` de los periodos para llenar el selector del formulario de notas.

**Orden:** por fecha de creacion descendente.

**Devuelve:** lista de opciones o `[]`.

### `getUserGrades(PDO $pdo, int $user_id): array`

**Proposito:** consultar todas las notas del usuario junto con el nombre de la materia y el nombre del periodo.

**Pasos:**

1. Consulta `grades` como tabla principal.
2. Une `subjets` para obtener `subjet_name`.
3. Une `terms` para obtener `term_name`.
4. Filtra para que materia y periodo pertenezcan al mismo usuario.
5. Calcula `avg_grade` con una funcion de ventana: promedio simple de las notas que comparten materia y periodo.
6. Ordena por nombre de materia ascendente y fecha de creacion descendente.
7. Devuelve todas las filas.

**Campos principales retornados:**

- `id`
- `subjet_id`
- `term_id`
- `name`
- `value`
- `avg_grade`
- `percentage`
- `created_at`
- `subjet_name`
- `term_name`

**Uso principal:** `views/grades/index.php` y `logic/OpenAi/openAi.php`.

**Importante para explicar:** la consulta trae cada nota individual. La vista despues agrupa las notas por materia y periodo y calcula el promedio ponderado con:

`promedio = suma(valor * porcentaje) / suma(porcentajes)`

### `getUserGradeById(PDO $pdo, int $user_id, int $grade_id): ?array`

**Proposito:** buscar una nota individual para editarla.

**Pasos:**

1. Busca la nota por `grade_id`.
2. Une materia y periodo.
3. Comprueba que ambos pertenezcan al usuario.
4. Devuelve la nota o `null`.

**Devuelve:** `id`, materia, periodo, nombre, valor, porcentaje y fecha de creacion.

### `getGradePercentageTotal(PDO $pdo, int $user_id, int $subjet_id, int $term_id, ?int $exclude_grade_id = null): float`

**Proposito:** calcular cuanto porcentaje ya esta ocupado para una materia en un periodo.

**Pasos:**

1. Suma `g.percentage` con `SUM()`.
2. Usa `COALESCE(..., 0)` para devolver cero si no hay notas.
3. Filtra por materia, periodo y propietario.
4. Si se esta editando, excluye la nota actual mediante `g.id <> :exclude_grade_id`.
5. Convierte el resultado a `float`.

**Devuelve:** el total acumulado de porcentajes, por ejemplo `75.0`.

**Regla de negocio:** al crear o editar una nota, la suma de porcentajes no debe superar el 100%.

---

## 8.4 `logic/goals/read.php`

### `getUserGoals(PDO $pdo, int $user_id): array`

**Proposito:** listar las metas del usuario incluyendo el nombre de la materia.

**Pasos:**

1. Consulta `goals`.
2. Une `subjets` para mostrar `subject_name`.
3. Filtra por usuario en metas y materias.
4. Ordena primero las metas con fecha, luego por fecha objetivo ascendente y finalmente por fecha de creacion descendente.
5. Devuelve todas las metas.

**Devuelve:** lista con id, materia, nombre, descripcion, fecha objetivo, estado, fecha de creacion y nombre de materia.

### `getUserGoalById(PDO $pdo, int $user_id, int $id): ?array`

**Proposito:** obtener una meta individual para editarla.

**Devuelve:** una meta o `null`.

**Seguridad:** exige que tanto la meta como la materia relacionada pertenezcan al usuario.

### `getUserGoalSubjects(PDO $pdo, int $user_id): array`

**Proposito:** obtener las materias disponibles para asociar una nueva meta.

**Pasos:** consulta `id` y `name` en `subjets`, filtra por usuario y ordena alfabeticamente.

**Devuelve:** lista de materias o `[]`.

### `getUserGoalBySubjectId(PDO $pdo, int $user_id, int $subject_id): array`

**Proposito:** obtener las metas asociadas a una materia especifica.

**Uso principal:** preparar la informacion que se envia al reporte de IA.

**Pasos:**

1. Filtra por `subject_id`.
2. Filtra por `user_id` tanto en la meta como en la materia.
3. Ordena por fecha objetivo y fecha de creacion.
4. Devuelve todas las metas o `[]`.

---

## 9. Dashboard y calculos

`logic/dashboard.php` define cuatro funciones adicionales:

### `get_avg_grade(PDO $pdo)`

Calcula el promedio general usando solamente el periodo activo. Primero calcula un promedio ponderado por materia y despues obtiene el promedio de esos promedios de materia. Devuelve un numero, normalmente decimal, o `null` cuando no existen datos.

### `get_lowest_subject(PDO $pdo)`

Agrupa las notas por materia, calcula el promedio simple de `g.value`, ordena ascendentemente y devuelve la materia con menor promedio. Devuelve un arreglo con `avg_grade` y `name`, o `null`.

### `get_high_subject(PDO $pdo)`

Hace el mismo proceso que la anterior, pero ordena descendentemente y devuelve la materia con mayor promedio.

### `get_actual_term(PDO $pdo)`

Busca el primer periodo del usuario cuyo `is_active` vale `1`. Devuelve `id` y `name`, o `null`.

En el dashboard, si no existe un resultado, se presenta `—` o `0.00` como valor visual por defecto.

---

## 10. Reporte con inteligencia artificial

El flujo de IA es:

1. El usuario pulsa **Generar reporte con AI** en el dashboard.
2. `logic/connectAi.php` verifica la sesion.
3. Llama a `getUserGrades()` para recuperar las notas.
4. Llama a `formatGradesJson()`.
5. `formatGradesJson()` obtiene el periodo activo y el promedio general.
6. Agrupa las notas por materia.
7. Consulta las metas de cada materia una sola vez mediante `getUserGoalBySubjectId()`.
8. Calcula el porcentaje pendiente: `100 - porcentaje acumulado`.
9. Construye un JSON con estudiante, periodo, promedio general, materias, notas y metas.
10. `callOpenAi()` lee las instrucciones de `description.md`.
11. Envia el JSON y las instrucciones al endpoint de OpenAI.
12. Extrae `choices[0].message.content` de la respuesta.
13. Parsedown convierte la respuesta Markdown a HTML.
14. El HTML se guarda en `$_SESSION['ai_result']`.
15. Se redirige al dashboard y se muestra el reporte.

### Que se debe explicar

La IA no calcula directamente desde cero los datos academicos: el sistema primero consulta y organiza la informacion de la base de datos. La IA recibe ese contexto estructurado y genera una interpretacion o recomendacion en lenguaje natural.

### Riesgos que deben reconocerse

- La clave de API no debe estar escrita en el repositorio ni en archivos publicos. Debe moverse a variables de entorno y revocarse si ya fue expuesta.
- La respuesta de la API debe validarse antes de acceder a `choices[0]`.
- Se debe manejar el caso de timeout, error HTTP o respuesta vacia.
- El HTML generado por Parsedown debe sanitizarse o limitarse si el contenido puede incluir etiquetas no deseadas.

---

## 11. Guion sugerido para la exposicion

### Paso 1: problema y objetivo

Explicar que los estudiantes suelen tener notas, periodos y objetivos dispersos. SmartGrade centraliza esos datos y los transforma en informacion util para hacer seguimiento academico.

### Paso 2: ingreso al sistema

Mostrar el registro y el login. Explicar que la contrasena se almacena cifrada, no en texto plano, y que la sesion identifica al usuario durante la navegacion.

### Paso 3: configurar el contexto academico

Crear o mostrar una materia y un periodo. Aclarar que el periodo puede marcarse como activo para que el dashboard calcule el promedio correspondiente.

### Paso 4: registrar notas

Mostrar una nota con valor y porcentaje. Explicar que el porcentaje representa el peso de esa actividad y que el sistema evita que la suma de pesos supere el 100% para una materia y periodo.

### Paso 5: consultar rendimiento

Abrir la pantalla de notas y mostrar la agrupacion por materia y periodo. Explicar la formula del promedio ponderado y la diferencia entre promedio por periodo, promedio por materia y promedio general.

### Paso 6: registrar metas

Crear una meta asociada a una materia, indicando que el sistema permite darle seguimiento por estado y fecha objetivo.

### Paso 7: dashboard

Mostrar promedio general, materia con menor desempeño, materia con mayor desempeño y periodo activo. Relacionar cada indicador con su funcion de consulta.

### Paso 8: reporte de IA

Mostrar como el sistema toma notas, promedios y metas, los organiza en JSON y solicita a la IA un reporte comprensible para el estudiante.

### Paso 9: arquitectura y seguridad

Explicar la separacion entre vistas, logica y base de datos; el uso de PDO y consultas preparadas; la validacion de pertenencia por `user_id`; y el escape de salida HTML.

### Paso 10: cierre

Cerrar con el valor del sistema: no solo almacena notas, sino que convierte los registros en seguimiento, indicadores y recomendaciones para mejorar el rendimiento.

---

## 12. Preguntas probables de los profesores

### ¿Por que se utiliza PDO?

Porque permite conectarse a MySQL, usar consultas preparadas y separar los valores de entrada de la sentencia SQL, reduciendo el riesgo de inyeccion SQL.

### ¿Como se evita que un estudiante vea datos de otro?

La sesion guarda el id del usuario autenticado y las consultas agregan condiciones como `WHERE user_id = :user_id`. En relaciones, tambien se valida que la materia o el periodo asociado pertenezca a ese usuario.

### ¿Como se calcula una nota final?

Se multiplica cada nota por su porcentaje, se suman esos resultados y se divide por la suma de porcentajes. La implementacion en PHP usa la misma idea para mostrar los promedios en la vista.

### ¿Que ocurre si no existen notas?

Las funciones que listan datos devuelven un arreglo vacio. Las vistas muestran mensajes como “Aun no tienes notas registradas” y el dashboard usa valores visuales por defecto.

### ¿Que pasa si el usuario intenta entrar directamente a una URL privada?

La pagina verifica `$_SESSION['user_id']`; si no existe, redirige al login.

### ¿Por que se usa `fetchAll()` y `fetch()`?

`fetchAll()` se usa cuando se necesita una lista; `fetch()` cuando solo se espera un registro, como una materia por id o una nota individual.

### ¿La IA reemplaza el calculo del sistema?

No. El backend calcula y organiza los datos academicos. La IA interpreta ese contexto y redacta un reporte.

---

## 14. Frase de cierre

> SmartGrade integra autenticacion, persistencia de datos, reglas de negocio y analisis academico en un flujo completo: el estudiante registra su realidad academica, el sistema la organiza y calcula indicadores, y finalmente la IA ayuda a convertir esos datos en una orientacion comprensible para mejorar.
