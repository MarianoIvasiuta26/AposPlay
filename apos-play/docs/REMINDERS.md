# Sistema de Recordatorios de Juego

## Descripción

El sistema de recordatorios envía notificaciones por email a los usuarios que tienen reservas confirmadas, 24 horas y 1 hora antes del partido.

## Componentes

### 1. Job: `SendGameReminders`
- **Ubicación**: `app/Jobs/SendGameReminders.php`
- **Frecuencia**: Cada hora (configurado en `routes/console.php`)
- **Funcionalidad**:
  - Busca reservas con estado `CONFIRMED`
  - Filtra por fecha y hora exacta (24h y 1h antes)
  - Envía notificaciones por email
  - Evita duplicados usando la tabla de notificaciones
  - Registra logs de actividad

### 2. Notificación: `GameReminder`
- **Ubicación**: `app/Notifications/GameReminder.php`
- **Canal**: Email
- **Contenido**:
  - Nombre del usuario
  - Contexto (24 horas o 1 hora)
  - Detalles de la cancha
  - Fecha y hora formateadas
  - Duración de la reserva
  - Enlace a "Ver mis reservas"

### 3. Cron Job
- **Ubicación**: `routes/console.php`
- **Programación**: `Schedule::job(new SendGameReminders)->hourly();`
- Se ejecuta cada hora para buscar partidos que son en 24h o 1h

## Configuración

### 1. Ejecutar migración

```bash
php artisan migrate
```

Esto crea la tabla `notifications` para evitar duplicados.

### 2. Configurar Cron en el servidor

Agregar al crontab del servidor:

```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Configurar envío de emails

En `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="AposPlay"
```

## Comandos Disponibles

### Enviar recordatorios manualmente

```bash
php artisan reminders:send
```

Este comando es útil para probar el sistema.

### Ver logs

```bash
tail -f storage/logs/laravel.log
```

Busca logs con el prefijo `SendGameReminders`.

## Flujo de Ejecución

1. El cron job se ejecuta cada hora
2. El Job `SendGameReminders` se despacha
3. Busca reservas confirmadas para:
   - Exactamente 24 horas desde ahora
   - Exactamente 1 hora desde ahora
4. Para cada reserva:
   - Verifica que no se haya enviado un recordatorio similar en las últimas 2 horas
   - Envía la notificación `GameReminder`
   - Registra en logs

## Estados de Reserva

Solo se envían recordatorios para reservas con estado `CONFIRMED`:
- `PENDING` - No se envía
- `PENDING_PAYMENT` - No se envía
- `PAID` - No se envía
- `CONFIRMED` - **Se envía** ✅
- `CANCELLED` - No se envía

## Logs de Ejemplo

```
[2026-02-26 10:00:00] local.INFO: Starting SendGameReminders job
[2026-02-26 10:00:00] local.INFO: 24h window: 2026-02-27 10:00:00 to 2026-02-27 10:59:59
[2026-02-26 10:00:00] local.INFO: 1h window: 2026-02-26 11:00:00 to 2026-02-26 11:59:59
[2026-02-26 10:00:00] local.INFO: Reminder sent to user 123 for reservation 456 (24 horas)
[2026-02-26 10:00:00] local.INFO: Processed 1 reminders for 24 horas
[2026-02-26 10:00:00] local.INFO: SendGameReminders completed - 24h: 1, 1h: 0
```

## Solución de Problemas

### No se envían los emails

1. Verificar la configuración de MAIL en `.env`
2. Revisar los logs: `storage/logs/laravel.log`
3. Verificar la cola de trabajos: `php artisan queue:work`
4. Revisar `storage/logs/laravel.log` para errores

### Se envían emails duplicados

El sistema ya incluye protección contra duplicados mediante:
- Tabla `notifications` con unique constraint
- Verificación de notificaciones enviadas en las últimas 2 horas

### El cron job no se ejecuta

1. Verificar que el cron esté configurado en el servidor
2. Probar manualmente: `php artisan schedule:run`
3. Revisar logs del sistema

## Personalización

### Modificar el tiempo de los recordatorios

En `app/Jobs/SendGameReminders.php`:

```php
// Para 48 horas
$start48 = now()->addHours(48)->startOfHour();

// Para 30 minutos
$start30 = now()->addMinutes(30)->startOfMinute();
```

### Modificar el contenido del email

En `app/Notifications/GameReminder.php` modificar el método `toMail()`.