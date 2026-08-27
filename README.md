# PM7 API — productivo

> Snapshot listo para desplegar (incluye `vendor/` ya instalado con `composer install --no-dev`). El código fuente de desarrollo vive en un repo privado aparte; este repo público solo existe para que el hosting (sin acceso SSH funcional) pueda clonarlo por HTTPS sin autenticación. No es donde se desarrolla — los cambios se generan ahí y se vuelven a publicar acá.

API en Laravel para el formulario de contacto de [pm7group.net](https://pm7group.net). Recibe el POST del formulario, verifica el token de reCAPTCHA v3 contra Google y envía un correo de notificación a ventas.

## Endpoint

`POST /api/contact`

```json
{
  "name": "Juan Pérez",
  "rut": "11.111.111-1",
  "email": "juan@example.com",
  "phone": "+56912345678",
  "company": "ACME SPA",
  "message": "Necesito una cotización...",
  "recaptcha_token": "..."
}
```

Respuestas:
- `201` — solicitud recibida, correo enviado.
- `422` — error de validación de campos, o el token de reCAPTCHA no pasó la verificación (score bajo, acción distinta a `contact_form`, o token inválido).

Límite de 5 solicitudes por minuto por IP (`RateLimiter::for('contact', ...)` en `AppServiceProvider`).

## Desarrollo local

Requiere PHP 8.2+ y Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Variables clave en `.env` (ver `.env.example` para la lista completa):

| Variable | Descripción |
| --- | --- |
| `FRONTEND_URL` | Origen(es) permitido(s) para CORS, separados por coma. Debe incluir la URL del frontend (Vercel). |
| `RECAPTCHA_SECRET_KEY` | Clave secreta de reCAPTCHA v3, del mismo sitio que `VITE_RECAPTCHA_SITE_KEY` en el frontend. Se obtiene en [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin). |
| `RECAPTCHA_MIN_SCORE` | Umbral mínimo de score (0–1) para aceptar el envío. Default `0.5`. |
| `CONTACT_TO_EMAIL` | Correo(s) que reciben la notificación de cada lead. Admite varios separados por coma. |
| `MAIL_*` | Credenciales SMTP para enviar el correo de notificación. |

## Despliegue en Webempresa

Webempresa no soporta Node.js, solo PHP (por eso este backend es Laravel y no una función serverless). Su panel es WePanel, no cPanel.

1. Subir el proyecto (sin `vendor/`, se instala en el servidor o se sube ya vendorizado si no hay acceso a Composer por SSH).
2. Apuntar el **document root del dominio/subdominio** (ej. `api.pm7group.net`) a la carpeta `public/` del proyecto, no a la raíz — es el requisito estándar de cualquier hosting Laravel.
3. Configurar las variables de `.env` en el servidor: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `FRONTEND_URL` (dominio real del sitio en Vercel), `RECAPTCHA_SECRET_KEY`, `CONTACT_TO_EMAIL`, y `MAIL_*` con las credenciales SMTP del correo `pm7group.net` ya administrado en Webempresa.
4. `php artisan key:generate --force` si no se generó ya, y `php artisan migrate --force` (crea las tablas internas de Laravel y `contact_submissions`, donde queda registrado cada lead con su número de seguimiento).
5. En el frontend (`PM7_Web`), configurar `VITE_API_URL` apuntando a la URL pública de esta API (ej. `https://api.pm7group.net`).
