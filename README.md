# DELIZ · arquitectura y arranque local

## Ejecutar en esta laptop

Extrae el ZIP en una carpeta de trabajo y abre PowerShell en la carpeta `deliz`. Usa la cuenta MySQL/MariaDB que tiene acceso a tu base existente. El ZIP original no incluía un volcado SQL. El login espera las tablas existentes `usuarios` y `roles` y hashes compatibles con `password_verify`.

```powershell
$env:DELIZ_DB_HOST = '127.0.0.1'
$env:DELIZ_DB_PORT = '3306'
$env:DELIZ_DB_NAME = 'deliz'
$env:DELIZ_DB_USER = Read-Host 'Usuario de MySQL'
$delizClave = Read-Host 'Contraseña de MySQL' -AsSecureString
$env:DELIZ_DB_PASSWORD = [Net.NetworkCredential]::new('', $delizClave).Password
New-Item -ItemType Directory -Force .local-sessions | Out-Null
php -d "session.save_path=$PWD\.local-sessions" -S 127.0.0.1:8080 -t public
```

Abre http://127.0.0.1:8080/. El servidor integrado es para desarrollo local. Con Apache/XAMPP configura el `DocumentRoot` del sitio en la carpeta `public` y aplica las variables al proceso PHP. Para producción usa HTTPS, activa `DELIZ_SECURE_COOKIE=1` y configura el directorio privado de sesiones fuera de `public`.

## Diagnóstico y cambios aplicados

- El ZIP colocaba `index.php` en `public/assets`. Sus inclusiones y las rutas de CSS quedaban desalineadas. Ahora `public/index.php` centraliza rutas, método, acceso y errores.
- El controlador abría la conexión incluso al mostrar el formulario. Ahora el primer acceso al modelo abre PDO, por lo que el formulario público y los errores de enrutamiento no dependen de que la base esté disponible.
- La salida mediante GET podía forzarse desde otro sitio. El formulario usa POST y token CSRF. El mismo control protege el inicio de sesión y rechaza valores manipulados con tipo array.
- El menú sugería módulos inexistentes y los permisos vivían en la vista. Se habilitó la consulta real de usuarios. La ruta exige el rol 1 según el mapa original y las rutas protegidas consultan en la base el estado y rol vigentes.
- El conector tenía configuración incrustada y podía mostrar errores de PDO. Ahora las credenciales se leen del entorno, las consultas preparadas son nativas y los errores HTTP devuelven mensajes genéricos.
- Se conservaron las consultas con parámetros, `password_verify` y el escape de HTML. La vista de usuarios tampoco expone hashes.

## Límite cliente-servidor

El navegador presenta formularios, muestra estados y usa CSS y validación HTML para mejorar la interacción. Cuando cocina y caja necesiten actualizar partes de la pantalla, un módulo JS puede solicitar snapshots o enviar acciones mediante Fetch al mismo origen. Usa `textContent` para texto recibido y trata las respuestas HTTP antes de actualizar la interfaz. El navegador nunca recibe credenciales de BD ni decide precios, stock, transiciones o permisos.

El servidor valida método y datos, autentica, autoriza el recurso y ejecuta reglas de negocio. El flujo HTML actual es petición → router → controlador o acción de lectura → modelo PDO → vista. Las escrituras HTML usan POST y redirección 303. El flujo JSON previsto es petición → router → sesión, rol y CSRF → controlador → servicio transaccional → modelo PDO → respuesta JSON. Mantener ambos en el mismo origen permite evolucionar la interfaz sin introducir CORS. Una futura SPA podría reutilizar esos contratos cuando exista una necesidad de despliegue independiente.

## Rutas y responsabilidades

En la copia actual las rutas son `index.php?route=...`. Las rutas limpias y `/api/v1` de esta tabla son propuestas para una fase posterior. La política inicial se basa en los IDs que sugería el dashboard original (1 administrador, 2 cajero, 3 cocina). Confirma el catálogo real antes de ampliar la política.

| Módulo | HTML o formulario | JSON previsto | Permiso inicial |
|---|---|---|---|
| Auth | GET login, POST autenticar y POST logout | Sesión por cookie | Inicio público, operaciones protegidas según sesión |
| Dashboard | GET dashboard | GET /api/v1/dashboard/resumen, si requiere refresco | Usuarios autenticados, métricas según rol |
| Categorías | GET /categorias, formularios de alta y edición | GET/POST /api/v1/categorias, PATCH /api/v1/categorias/{id} | Admin escribe |
| Productos | GET /productos, formularios de catálogo | GET/POST /api/v1/productos, PATCH /api/v1/productos/{id} | Admin escribe, caja consulta catálogo activo |
| Inventario | GET /inventario | GET /api/v1/inventario, POST /api/v1/inventario/movimientos | Admin |
| Pedidos | GET /pedidos | GET/POST /api/v1/pedidos, POST /api/v1/pedidos/{id}/venta | Admin y caja |
| Cocina | GET /cocina | GET /api/v1/cocina/pedidos, PATCH /api/v1/cocina/pedidos/{id}/estado | Admin y cocina |
| Reportes | GET /reportes con período y filtros | GET /api/v1/reportes/ventas | Admin |
| Usuarios | GET usuarios implementado en modo lectura | POST /api/v1/usuarios, PATCH /api/v1/usuarios/{id} | Admin |

Define los permisos de lectura además de los de escritura. Por ejemplo, el tablero de cocina necesita productos y cantidades del pedido, mientras el listado de pedidos de caja puede limitarse a su turno cuando existan turnos. Limita y pagina las consultas.

## Contrato y consistencia de operaciones

JSON usa `Content-Type: application/json`. Para mutaciones de la API envía el token en `X-CSRF-Token` y conserva la cookie de sesión con Fetch del mismo origen. Extiende la validación CSRF del router cuando habilites la API. Una creación devuelve 201 y la representación guardada. Un error puede usar `{"error":{"code":"stock_insuficiente","message":"Existencias insuficientes"}}`. Reserva 401 para sesión ausente en API, 403 para permiso o CSRF, 404 para recurso ausente, 409 para conflicto y 422 para campos inválidos.

- **Cocina.** Propón `pendiente → en_preparacion → listo → entregado`. Cocina avanza hasta listo y caja registra entrega. El servidor comprueba rol y transición. La solicitud incluye el estado siguiente y la versión vista por el navegador. Un UPDATE condicionado por ID, estado y versión impide sobrescribir una actualización concurrente. Devuelve 409 si la versión cambió y el cliente vuelve a consultar. Registra actor, estado anterior, nuevo estado y fecha en la misma transacción. Modela el pago independientemente del estado de preparación.
- **Venta e inventario.** Propón descontar existencias una sola vez al confirmar la venta. Dentro de una transacción InnoDB, bloquea el pedido y los insumos afectados en orden estable, verifica cantidades y precios del servidor, registra venta, detalle y movimientos, y confirma todo junto. Ante stock insuficiente revierte la transacción. Define una restricción única para la venta por pedido y una clave de idempotencia para volver a entregar el resultado de un intento exitoso si se repite la solicitud. Si el negocio requiere preparar antes de cobrar, decide si hay que reservar stock al confirmar el pedido. Las anulaciones que restituyen stock generan movimientos compensatorios. Usa DECIMAL o enteros en unidades mínimas para importes.
- **Actualización de vistas.** Empieza con polling cada 5–10 segundos como valor inicial, una solicitud a la vez. Pausa con la pestaña oculta y aplica espera creciente ante fallos. Muestra una señal de desconexión y refresca inmediatamente después de una acción local. Mide tráfico y latencia antes de evaluar SSE.

## Seguridad y organización

- La sesión usa cookie HttpOnly, SameSite=Lax y Secure con HTTPS o configuración explícita. El ID se regenera al acceder. La política inicial vence tras 30 minutos de inactividad u 8 horas totales. Los datos de sesión quedan del lado servidor.
- Valida tipos, tamaño, rangos y enumeraciones en PHP. Para filtros y ordenamiento SQL usa listas permitidas de columnas. PDO parametriza valores y la cuenta de aplicación debe tener privilegios mínimos. Ejecuta migraciones con otra cuenta.
- Escapa datos al renderizar HTML con `htmlspecialchars` y usa APIs DOM seguras. Conserva la política CSP al agregar scripts. Antes de publicar, añade limitación de intentos de login por cuenta y origen, registros de auditoría sin claves ni tokens y revisa los encabezados detrás del proxy HTTPS.
- `public/assets/css/` mantiene los estilos por pantalla. Extrae `base.css` cuando las reglas se repitan. En `public/assets/js/` añade módulos ES `http.js`, `pedidos.js` y `cocina.js` cuando existan los endpoints. Centraliza en `http.js` el envío de JSON, CSRF y manejo de errores.
- `app/controllers/` adapta HTTP, `app/models/` agrupa PDO, `app/views/` renderiza datos y `config/database.php` configura la conexión. Extrae navegación y cabecera a `app/views/layouts/` al crear más pantallas. Añade `app/services/` para ventas y transiciones cuando haya varias escrituras relacionadas.

## Roadmap

1. **Base y seguridad.** Verifica el login con usuarios reales de los tres roles, contrasta IDs de roles con la BD, configura HTTPS y añade límites de intentos. Comprueba permisos llamando directamente a cada ruta y revisa expiración y revocación de sesiones.
2. **Módulos operativos.** Versiona el esquema de catálogo, inventario, pedidos y movimientos. Entrega catálogo, inventario, caja y cocina en ese orden. Verifica venta duplicada, stock insuficiente y dos actualizaciones concurrentes del mismo pedido.
3. **Reportes y actualización.** Define métricas y períodos, agrega índices según las consultas, pagina listados y habilita el polling de cocina y caja. Mide latencia y carga antes de adoptar SSE.

