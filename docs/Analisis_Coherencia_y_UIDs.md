# Análisis de Coherencia: Documento PDF v1.0 vs Código Implementado

**Fecha:** 23/03/2026
**Documento analizado:** `Especificacion_de_Requerimientos.docx.pdf` (v1.0 — 15/06/2025)
**Código:** Repositorio AposPlay (`apos-play/`)

---

## 1. Resumen Ejecutivo

El documento PDF v1.0 presenta **excelente coherencia** con la implementación en código. **Todos los casos de uso documentados están implementados**, incluyendo notificaciones por email, recordatorios programados vía cron, reembolsos vía API de MercadoPago, y el flujo completo de reprogramación de reservas. Se detectaron únicamente **errores de numeración en el PDF** y **UIDs faltantes** para pantallas que fueron agregadas en versiones posteriores.

---

## 2. Errores de Numeración en el PDF v1.0

### 2.1 UC duplicados (bug del documento)

| UC duplicado | Primera aparición | Segunda aparición | Corrección sugerida |
|---|---|---|---|
| **UC-14** | Notificación asignación cupón (p.31) | Ver reportes de ocupación (p.43) | La segunda debería ser **UC-25** |
| **UC-16** | Reembolsar pago (p.33) | Gestionar promociones (p.46) | La segunda debería ser **UC-27** |

### 2.2 Diferencias de numeración con v3.md

La v3.md realizó una renumeración completa. Mapeo de equivalencias:

| Funcionalidad | PDF v1.0 | v3.md | Notas |
|---|---|---|---|
| Gestionar Usuarios | UC-01 (A-D) | UC-01 (A-D) | Coherente |
| Ver disponibilidad | UC-02 | UC-02 | Coherente |
| Reservar cancha | UC-03 | UC-03 | Coherente |
| Cancelar reserva | UC-04 | UC-04 | Coherente |
| Ver mis reservas | UC-05 | UC-05 | Coherente |
| Modificar reserva | UC-06 | UC-06 | Coherente |
| Pagar reserva online | UC-07 | UC-07 | Coherente |
| Bloqueos de horario | *(no tiene UC propio)* | UC-08 | **Falta en PDF** |
| Canjear puntos | UC-08 | UC-19 | Renumerado |
| Acumular puntos | UC-09 | UC-18 | Renumerado |
| Aplicar cupón | UC-10 | UC-10 | Coherente |
| Ver historial puntos | UC-11 | *(dentro de UC-18)* | Fusionado en v3 |
| Notificar cancelación | UC-12 | UC-12 | Coherente |
| Recordatorio de juego | UC-13 | UC-13 | Coherente |
| Notificación cupón | UC-14 | UC-14 | Coherente |
| Explorar canchas | UC-15 | UC-15 | Coherente |
| Reembolsar pago | UC-16 | UC-16 | Coherente |
| Confirmar asistencia | UC-17 | UC-17 | Coherente |
| Crear cuenta Owner | UC-18 | UC-21A | Renumerado |
| Ver todos los Owners | UC-19 | UC-21B | Renumerado |
| Desactivar Owner | UC-20 | UC-21C | Renumerado |
| Reactivar Owner | UC-21 | UC-21D | Renumerado |
| Panel por Rol | UC-22 | UC-22 | Coherente |
| Procesar pago MP | UC-23 | UC-23 | Coherente |
| Pago fallido | UC-24 | UC-24 | Coherente |
| Ver reportes ocupación | UC-14 *(error)* | UC-25 | Corregido en v3 |
| Exportar ingresos | UC-26 | UC-26 | Coherente |
| Gestionar promociones | UC-16 *(error)* | UC-20 | Corregido y renumerado en v3 |
| Gestionar cupones | UC-27 (A-E) | UC-27 (A-E) | Coherente |
| Gestionar Staff | UC-28 (A-C) | UC-28 (A-C) | Coherente |
| Gestionar complejos | UC-29 (A-D) | UC-29 (A-D) | Coherente |
| Gestionar cancha | UC-30 (A-F) | UC-30 (A-F) | Coherente |
| Ver auditoría | *(no existe)* | UC-31 | **Agregado en v3** |

---

## 3. Coherencia Documento ↔ Código

### 3.1 Casos de Uso IMPLEMENTADOS correctamente

| UC (PDF) | Descripción | Ruta en código | Estado |
|---|---|---|---|
| UC-01A | Iniciar sesión | `routes/auth.php` (Breeze) | ✅ Implementado |
| UC-01B | Registrar cuenta | `routes/auth.php` (Breeze) | ✅ Implementado |
| UC-01C | Recuperar contraseña | `routes/auth.php` (Breeze) | ✅ Implementado |
| UC-01D | Gestionar perfil | `settings/profile`, `settings/password`, `settings/appearance` | ✅ Implementado |
| UC-02 | Ver disponibilidad | `CourtAvailability` Livewire component | ✅ Implementado |
| UC-03 | Reservar cancha | Modal en `CourtAvailability` | ✅ Implementado |
| UC-04 | Cancelar reserva | `MyReservations::cancel()` | ✅ Implementado (regla 24h) |
| UC-05 | Ver mis reservas | `MyReservations` component | ✅ Implementado |
| UC-07 | Pagar reserva | `MercadoPagoController::createPreference()` | ✅ Implementado |
| UC-08/19 | Canjear puntos | `CourtAvailability` modal (checkbox `$usePoints`) | ✅ Implementado |
| UC-09/18 | Acumular puntos | `ReservationObserver` + `LoyaltyService::earnPoints()` | ✅ Implementado |
| UC-10 | Aplicar cupón | `CourtAvailability` modal (campo cupón) | ✅ Implementado |
| UC-11 | Ver historial puntos | `LoyaltyBalance` component (`/mis-puntos`) | ✅ Implementado |
| UC-16 | Reembolsar pago | `DailyReservations` (admin) / `Staff\Reservations` | ✅ Implementado (simulado) |
| UC-18 | Crear cuenta Owner | `Admin\Owners\Form` (`/admin/owners/crear`) | ✅ Implementado |
| UC-19 | Ver todos los Owners | `Admin\Owners\Index` (`/admin/owners`) | ✅ Implementado |
| UC-20/21 | Desactivar/Reactivar Owner | Toggle en `Admin\Owners\Index` | ✅ Implementado |
| UC-22 | Panel por Rol | Sidebar condicional + RoleMiddleware | ✅ Implementado |
| UC-23 | Procesar pago MP | `MercadoPagoController` | ✅ Implementado |
| UC-24 | Pago fallido | `MercadoPagoController::failure()` | ✅ Implementado |
| UC-25 | Reportes de ocupación | `Admin\OccupancyReport` (`/admin/reporte-ocupacion`) | ✅ Implementado |
| UC-26 | Exportar ingresos | `Admin\IncomeExport` (`/admin/exportar-ingresos`) | ✅ Implementado |
| UC-27A-E | Gestionar cupones | `Admin\Coupons` (`/admin/cupones`) | ✅ Implementado |
| UC-28A-C | Gestionar Staff | `Owner\Staff\Index/Form` (`/owner/staff`) | ✅ Implementado |
| UC-29A-D | Gestionar complejos | `Owner\Complexes\Index/Form` (`/owner/complejos`) | ✅ Implementado |
| UC-30A-F | Gestionar cancha | `Canchas` + `CourtSchedules` (`/canchas`) | ✅ Implementado |
| Promociones | Gestionar promociones | `Admin\Promotions\Index/Form` (`/admin/promociones`) | ✅ Implementado |
| Bloqueos | Bloqueos de horario | `Admin\CourtBlocks\Index/Form` (`/admin/bloqueos`) | ✅ Implementado |
| UC-31 | Ver auditoría | `Admin\AuditLog\Index` (`/admin/auditoria`) | ✅ Implementado |

### 3.2 Casos de Uso adicionales verificados como IMPLEMENTADOS

| UC (PDF) | Descripción | Implementación | Detalle |
|---|---|---|---|
| UC-06 | Modificar reserva | ✅ Completo | Modal "Reprogramar Reserva" en `MyReservations` con selector de fecha, horario y duración. Lógica en `ReservationService::reschedule()` con validación de 4h de anticipación, verificación de bloqueos y disponibilidad. |
| UC-12 | Notificar cancelación al staff | ✅ Completo | `ReservationObserver::notifyStaffOfCancellation()` envía `CancellationNotification` (email + database) a todo el staff del complejo. Tabla `notifications` creada via migración. |
| UC-13 | Enviar recordatorio de juego | ✅ Completo | `SendGameReminders` Job ejecutado cada hora vía `routes/console.php`. Envía `GameReminder` notification 24h y 1h antes. Anti-duplicados incluido. |
| UC-14 | Notificación asignación cupón | ✅ Completo | `Coupons::confirmSave()` envía `CouponAssigned` notification (email + database) a cada usuario asignado al cupón. |
| UC-15 | Explorar canchas (público) | ✅ Completo | Sección `#canchas` en `welcome.blade.php` lista todas las canchas con nombre, tipo, dirección, precio y botón "Reservar". Accesible sin autenticación. |
| UC-16 | Reembolsar pago | ✅ Completo | `RefundService::refundViaMercadoPago()` usa `PaymentRefundClient::refund()` de la API real de MercadoPago. Reembolso total (>8h) y parcial (2-8h). |
| UC-17 | Confirmar asistencia | ✅ Completo | `Staff\Reservations::confirmReservation()` cambia estado a `CONFIRMED`. Visible en tabla "Reservas del Día" con botón "Confirmar" para reservas en estado `PAID`. |

### 3.3 Implementaciones en código SIN UC en el PDF v1.0

| Feature | Ruta | Observación |
|---|---|---|
| Auditoría (UC-31) | `/admin/auditoria` | Agregado en v3.md, no existe en PDF v1.0 |
| Bloqueos de horario | `/admin/bloqueos` | Implementado pero sin UC dedicado en PDF (mencionado en UC-30 indirectamente) |
| Reservas del Día (Staff) | `/staff/reservas` | Implementado como vista staff, en PDF está dentro de UC-17 |

---

## 4. Coherencia de Actores

| Actor PDF | Rol en código (`UserRole` enum) | Coherente |
|---|---|---|
| ACT-01 Usuario | `UserRole::USER` | ✅ |
| ACT-02 Administrador (superadmin) | `UserRole::SUPERADMIN` | ✅ |
| ACT-03 Staff | `UserRole::STAFF` | ✅ |
| ACT-04 Sistema de pagos | `MercadoPagoController` | ✅ |
| ACT-05 Job Scheduler | `SendGameReminders` Job + `routes/console.php` scheduler | ✅ |
| ACT-06 Owner | `UserRole::OWNER` | ✅ |

---

## 5. Coherencia de IRQs (Requisitos de Información)

| IRQ PDF | Datos específicos | Modelo en código | Coherente |
|---|---|---|---|
| IRQ-01 Info de canchas | nombre, tipo, precio, dirección, horarios | `Court`, `CourtAddress`, `Schedule` | ✅ |
| IRQ-02 Info de reservas | fecha, hora, cancha, usuario, estado, monto | `Reservation` | ✅ |
| IRQ-03 Info de usuarios | nombre, email, password, role | `User` | ✅ |
| IRQ-04 Info de pagos | payment_id, payment_status, monto, fecha | `Reservation` (campos de pago) | ✅ |
| IRQ-05 Info de métricas | ocupación, ingresos, por cancha/período | `OccupancyReport`, `IncomeExport` | ✅ |
| IRQ-06 Info de complejos | nombre, dirección, owner, estado, staff | `Complex`, `complex_staff` pivot | ✅ |
| IRQ-07 Info de auditoría | *(solo en v3.md)* | `AuditLog` model | ✅ (v3) |

---

## 6. Coherencia de NFRs

| NFR PDF | Descripción | Estado en código |
|---|---|---|
| NFR-01 Seguridad | CSRF, bcrypt, middleware de roles | ✅ Laravel CSRF + bcrypt + `RoleMiddleware` |
| NFR-02 Rendimiento | Usuarios concurrentes | ✅ Laravel Sail + MySQL |
| NFR-03 Usabilidad | Diseño responsivo (Tailwind + Flux UI) | ✅ Tailwind CSS v4 + Flux UI |
| NFR-04 Auditoría | Borrado lógico + timestamps + logs | ✅ Soft deletes en todos los modelos + `AuditLog` |

---

## 7. Coherencia de UIDs (Identificadores de Interfaz)

### 7.1 UIDs del PDF vs pantallas reales

| UID PDF | Nombre | Pantalla real | Coherente |
|---|---|---|---|
| UID-01 | Pantalla de Bienvenida / Login | `/login`, `/register` | ✅ |
| UID-02 | Dashboard del Jugador | `/dashboard` (role: user) | ✅ |
| UID-03 | Buscador Avanzado | `/court-availability` (filtros fecha/tipo) | ✅ |
| UID-04 | Detalle del Complejo | *(no hay ruta dedicada)* | ⚠️ No hay pantalla "detalle complejo" pública |
| UID-05 | Calendario de Selección de Turnos | Grilla horaria en `CourtAvailability` | ✅ |
| UID-06 | Resumen de Reserva y Pago | Modal "Confirmar Reserva" en `CourtAvailability` | ✅ |
| UID-07 | Mis Reservas | `/mis-reservas` | ✅ |
| UID-08 | Panel de Control del Dueño | `/dashboard` (role: owner) | ✅ |
| UID-09 | Gestión de Canchas | `/canchas` | ✅ |
| UID-10 | Configuración de Horarios | `/canchas/{court}/horarios` | ✅ |
| UID-11 | Gestión de Bloqueos | `/admin/bloqueos` | ✅ |
| UID-12 | Módulo de Fidelización | `/admin/promociones` + `/mis-puntos` | ✅ |
| UID-13 | Perfil de Usuario | `/settings/profile` | ✅ |

### 7.2 Pantallas implementadas SIN UID en el PDF

| Pantalla | Ruta | UID sugerido |
|---|---|---|
| Dashboard Superadmin | `/dashboard` (role: superadmin) | **UID-14** |
| Dashboard Staff | `/dashboard` (role: staff) | **UID-15** |
| Gestionar Owners | `/admin/owners` | **UID-16** |
| Crear Owner | `/admin/owners/crear` | **UID-17** |
| Gestionar Staff | `/owner/staff` | **UID-18** |
| Crear Staff | `/owner/staff/crear` | **UID-19** |
| Gestionar Complejos | `/owner/complejos` | **UID-20** |
| Crear/Editar Complejo | `/owner/complejos/crear` | **UID-21** |
| Cupones y Descuentos | `/admin/cupones` | **UID-22** |
| Reporte de Ocupación | `/admin/reporte-ocupacion` | **UID-23** |
| Exportar Ingresos | `/admin/exportar-ingresos` | **UID-24** |
| Reservas del Día (Staff) | `/staff/reservas` | **UID-25** |
| Todas las Reservas (Admin) | `/admin/reservas-del-dia` | **UID-26** |
| Auditoría | `/admin/auditoria` | **UID-27** |
| Promociones | `/admin/promociones` | **UID-28** |
| Crear/Editar Promoción | `/admin/promociones/crear` | **UID-29** |
| Crear Bloqueo | `/admin/bloqueos/crear` | **UID-30** |
| Apariencia (tema) | `/settings/appearance` | **UID-31** |
| Cambiar Contraseña | `/settings/password` | **UID-32** |

---

## 8. Diagramas UID (Interacción de Usuario)

A continuación se presentan los diagramas de interacción de usuario para los flujos principales del sistema.

---

### UID-01: Pantalla de Bienvenida / Login

```
┌─────────────────────────────────────────────────┐
│              PANTALLA DE BIENVENIDA             │
│                                                 │
│  ┌─────────────┐  ┌──────────────┐              │
│  │ Iniciar     │  │  Registrarse │              │
│  │ Sesión      │  │              │              │
│  └──────┬──────┘  └──────┬───────┘              │
│         │                │                      │
│         ▼                ▼                      │
│  ┌─────────────┐  ┌──────────────┐              │
│  │ UC-01A      │  │ UC-01B       │              │
│  │ Login Form  │  │ Register Form│              │
│  │ ┌─────────┐ │  │ ┌──────────┐ │              │
│  │ │ Email   │ │  │ │ Nombre   │ │              │
│  │ │ Password│ │  │ │ Email    │ │              │
│  │ └─────────┘ │  │ │ Password │ │              │
│  │             │  │ └──────────┘ │              │
│  │ [¿Olvidaste │  │              │              │
│  │  tu clave?] │  │ [Registrarse]│              │
│  │     │       │  └──────┬───────┘              │
│  │     │       │         │                      │
│  │ [Iniciar   ]│         │                      │
│  └──────┬──────┘         │                      │
│         │                │                      │
│         ▼                ▼                      │
│      ┌──────────────────────┐                   │
│      │    /dashboard        │                   │
│      │  (según rol del user)│                   │
│      └──────────────────────┘                   │
│                                                 │
│  ┌──────────────┐                               │
│  │ UC-01C       │                               │
│  │ Recuperar    │◄── [¿Olvidaste tu clave?]     │
│  │ Contraseña   │                               │
│  │ ┌──────────┐ │                               │
│  │ │ Email    │ │                               │
│  │ └──────────┘ │                               │
│  │ [Enviar     ]│──► Email con enlace           │
│  └──────────────┘    ──► Reset Password Form    │
└─────────────────────────────────────────────────┘
```

---

### UID-02: Dashboard del Jugador (User)

```
┌─────────────────────────────────────────────────────────┐
│  SIDEBAR (user)          │   DASHBOARD USER              │
│                          │                               │
│  ● Inicio ◄─────────────│  ┌───────────┐ ┌───────────┐  │
│  ● Mis Reservas          │  │ Mis       │ │ Mis       │  │
│  ● Mis Puntos            │  │ Reservas  │ │ Puntos    │  │
│  ● Canchas               │  │ [→ ver]   │ │ [→ ver]   │  │
│                          │  └───────────┘ └───────────┘  │
│  ─── Configuración ──── │  ┌───────────┐                │
│  ● Configuración         │  │ Canchas   │                │
│                          │  │ [→ ver]   │                │
│                          │  └───────────┘                │
│                          │                               │
│  USER DROPDOWN:          │  Bienvenido, {nombre}         │
│  ┌────────────────┐      │  Rol: Usuario                 │
│  │ {nombre}       │      │                               │
│  │ {email}        │      │                               │
│  │ ─────────────  │      │                               │
│  │ Configuración  │      │                               │
│  │ ─────────────  │      │                               │
│  │ Cerrar sesión  │      │                               │
│  └────────────────┘      │                               │
└─────────────────────────────────────────────────────────┘
```

---

### UID-03/05/06: Flujo de Reserva (Disponibilidad → Reserva → Pago)

```
┌─────────────────────────────────────────────────────────────┐
│  CANCHAS (CourtAvailability) — UC-02, UC-03                 │
│                                                             │
│  ┌─── FILTROS ──────────────────────────┐                   │
│  │ Fecha: [selector]  Tipo: [Todas ▼]   │                   │
│  └──────────────────────────────────────┘                   │
│                                                             │
│  ┌─── CANCHA: "Cancha 1" ──────────────────────────────┐    │
│  │ Dirección: Av. San Martín 123 | Precio: $5000/hora  │    │
│  │                                                     │    │
│  │ Horarios Disponibles:                               │    │
│  │ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐       │    │
│  │ │ 08:00│ │ 09:00│ │ 10:00│ │ 11:00│ │ 12:00│       │    │
│  │ │ 🟢  │ │ 🔴  │ │ 🟢  │ │ 🟢  │ │ ⬜  │       │    │
│  │ └──┬───┘ └──────┘ └──────┘ └──────┘ └──────┘       │    │
│  └────┼────────────────────────────────────────────────┘    │
│       │ click en slot verde                                 │
│       ▼                                                     │
│  ┌─── MODAL: Confirmar Reserva ─────────────────────┐       │
│  │                                                   │       │
│  │ Cancha: Cancha 1                                  │       │
│  │ Fecha: 23/03/2026  Hora: 08:00                    │       │
│  │ Duración: [1h ▼] [2h] [3h]                       │       │
│  │ Precio por hora: $5,000                           │       │
│  │                                                   │       │
│  │ ┌─ Cupón de descuento ─────────────────────┐      │       │
│  │ │ ¿Tenés un cupón de descuento?            │      │       │
│  │ │ [código cupón    ] [Aplicar]  ← UC-10    │      │       │
│  │ └─────────────────────────────────────────┘      │       │
│  │                                                   │       │
│  │ ☐ Usar 50 puntos (30% descuento)  ← UC-08       │       │
│  │   Saldo disponible: 75 puntos                     │       │
│  │                                                   │       │
│  │ Total: $5,000  (o $3,500 con descuento)           │       │
│  │                                                   │       │
│  │ [Cancelar]          [Confirmar Reserva]           │       │
│  └───────────────────────────┬───────────────────────┘       │
│                              │                               │
│                              ▼                               │
│                    Reserva creada → "Pendiente de Pago"      │
│                    Redirige a /mis-reservas                   │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-07: Mis Reservas (UC-05, UC-04, UC-07)

```
┌─────────────────────────────────────────────────────────────┐
│  MIS RESERVAS                                               │
│                                                             │
│  ┌─── Reserva #1 ──────────────────────────────────────┐    │
│  │ Cancha: Cancha 1 | Fecha: 24/03 08:00-09:00        │    │
│  │ Precio: $5,000   | Estado: [Pendiente de Pago] 🟡   │    │
│  │                                                     │    │
│  │ [Pagar Reserva]  [Cancelar]                         │    │
│  └──────┬────────────────┬─────────────────────────────┘    │
│         │                │                                  │
│         ▼                ▼                                  │
│   UC-07: Pagar      UC-04: Cancelar                        │
│   → MercadoPago     → Confirmar cancelación                │
│   → Redirige a MP   → Solo si >24h                         │
│   → Callback:       → Revierte puntos                      │
│     success/failure  → Libera horario                      │
│                                                             │
│  ┌─── Reserva #2 ──────────────────────────────────────┐    │
│  │ Cancha: Cancha 2 | Fecha: 22/03 18:00-19:00        │    │
│  │ Precio: $5,000   | Estado: [Confirmada] 🟢          │    │
│  │                                                     │    │
│  │ [Modificar]  [Cancelar]                             │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌─── Reserva #3 ──────────────────────────────────────┐    │
│  │ Cancha: Cancha 1 | Fecha: 20/03 10:00-11:00        │    │
│  │ Precio: $3,500   | Estado: [Cancelada] 🔴           │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-08: Dashboard Owner

```
┌─────────────────────────────────────────────────────────────┐
│  SIDEBAR (owner)              │   DASHBOARD OWNER            │
│                               │                              │
│  ● Inicio                     │  ┌──────────┐ ┌──────────┐  │
│                               │  │Reservas  │ │Cupones y │  │
│  ─── Administración ────     │  │del Día   │ │Descuentos│  │
│  ● Reservas del Día           │  │[→ ver]   │ │[→ ver]   │  │
│  ● Cupones y Descuentos      │  └──────────┘ └──────────┘  │
│  ● Reporte de Ocupación      │  ┌──────────┐ ┌──────────┐  │
│  ● Exportar Ingresos         │  │Reporte   │ │Exportar  │  │
│  ● Promociones               │  │Ocupación │ │Ingresos  │  │
│  ● Bloqueos de Horario       │  │[→ ver]   │ │[→ ver]   │  │
│  ● Auditoría                 │  └──────────┘ └──────────┘  │
│                               │  ┌──────────┐ ┌──────────┐  │
│  ─── Mi Complejo ────        │  │Promociones│ │Bloqueos  │  │
│  ● Complejos                  │  │[→ ver]   │ │[→ ver]   │  │
│  ● Mis Canchas                │  └──────────┘ └──────────┘  │
│  ● Staff                      │  ┌──────────┐ ┌──────────┐  │
│                               │  │Auditoría │ │Complejos │  │
│                               │  │[→ ver]   │ │[→ ver]   │  │
│                               │  └──────────┘ └──────────┘  │
│                               │  ┌──────────┐ ┌──────────┐  │
│                               │  │Canchas   │ │Staff     │  │
│                               │  │[→ ver]   │ │[→ ver]   │  │
│                               │  └──────────┘ └──────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-14: Dashboard Superadmin

```
┌─────────────────────────────────────────────────────────────┐
│  SIDEBAR (superadmin)         │   DASHBOARD SUPERADMIN       │
│                               │                              │
│  ● Inicio                     │  (Incluye TODO lo del owner  │
│                               │   + sección Superadmin)      │
│  ─── Superadmin ────         │                              │
│  ● Gestionar Owners           │  ┌──────────┐ ┌──────────┐  │
│  ● Todas las Reservas         │  │Gestionar │ │Todas las │  │
│                               │  │Owners    │ │Reservas  │  │
│  ─── Administración ────     │  │[→ ver]   │ │[→ ver]   │  │
│  ● Reservas del Día           │  └──────────┘ └──────────┘  │
│  ● Cupones y Descuentos      │                              │
│  ● Reporte de Ocupación      │  + todas las tarjetas del    │
│  ● Exportar Ingresos         │    owner dashboard            │
│  ● Promociones               │                              │
│  ● Bloqueos de Horario       │                              │
│  ● Auditoría                 │                              │
│                               │                              │
│  ─── Mi Complejo ────        │                              │
│  ● Complejos                  │                              │
│  ● Mis Canchas                │                              │
│  ● Staff                      │                              │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-16: Gestionar Owners (UC-18, UC-19, UC-20, UC-21)

```
┌─────────────────────────────────────────────────────────────┐
│  GESTIONAR OWNERS (superadmin only)                         │
│                                                             │
│  [Buscar por nombre o email...] 🔍       [Crear Owner]     │
│                                              │              │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Nombre    │ Email           │ Complejos │ Estado │Acc│    │
│  ├───────────┼─────────────────┼──────────┼────────┼───│    │
│  │ Juan P.   │ juan@mail.com   │ Club A   │🟢Activo│[D]│    │
│  │ María L.  │ maria@mail.com  │ Club B,C │🟢Activo│[D]│    │
│  │ Pedro S.  │ pedro@mail.com  │ —        │🔴Inact.│[R]│    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  [D] = Desactivar (UC-20)                                  │
│  [R] = Reactivar (UC-21)                                   │
│                                                             │
│  ┌─── Crear Owner (UC-18) ────────────────────┐            │
│  │ [← Volver]                                  │            │
│  │ Nombre:   [______________]                  │            │
│  │ Email:    [______________]                  │            │
│  │ Password: [______________]                  │            │
│  │                                             │            │
│  │ ─── Complejo (opcional) ───                 │            │
│  │ Nombre complejo:    [______________]        │            │
│  │ Dirección complejo: [______________]        │            │
│  │                                             │            │
│  │              [Crear Owner]                  │            │
│  └─────────────────────────────────────────────┘            │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-18: Gestionar Staff (UC-28, UC-28A-C)

```
┌─────────────────────────────────────────────────────────────┐
│  GESTIONAR STAFF (owner)                                    │
│                                                             │
│  [Buscar por nombre/email...] 🔍         [Crear Staff]     │
│                                              │              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Nombre  │ Email          │ Complejos Asignados │ Acc │   │
│  ├─────────┼────────────────┼─────────────────────┼─────│   │
│  │ Ana R.  │ ana@mail.com   │ [Club A] [Club B ×] │     │   │
│  │ Luis M. │ luis@mail.com  │ Sin complejos       │     │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  [×] = Revocar acceso a complejo (UC-28C)                  │
│                                                             │
│  ┌─── Crear Staff (UC-28A) ──────────────────────┐         │
│  │ [← Volver]                                     │         │
│  │ Nombre:   [______________]                     │         │
│  │ Email:    [______________]                     │         │
│  │ Password: [______________]                     │         │
│  │                                                │         │
│  │ ─── Asignar a complejos ───                    │         │
│  │ ☐ Club A (Av. San Martín 123)                  │         │
│  │ ☑ Club B (Calle 9 de Julio 456)                │         │
│  │                                                │         │
│  │              [Crear Staff]                     │         │
│  └────────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-20: Gestionar Complejos (UC-29, UC-29A-D)

```
┌─────────────────────────────────────────────────────────────┐
│  MIS COMPLEJOS (owner)                                      │
│                                                             │
│  [Crear Complejo]                                           │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Nombre   │ Dirección       │ Canchas │Staff│Estado│Acc│  │
│  ├──────────┼─────────────────┼─────────┼─────┼──────┼───│  │
│  │ Club A   │ Av. San Martín  │    3    │  2  │🟢Act │E/D│  │
│  │ Club B   │ Calle 9 Julio   │    1    │  0  │🟢Act │E/D│  │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  E = Editar (UC-29B)                                       │
│  D = Desactivar/Eliminar (UC-29C)                          │
│                                                             │
│  ┌─── Crear/Editar Complejo ─────────────────────┐         │
│  │ [← Volver]                                     │         │
│  │ Nombre:    [______________]                    │         │
│  │ Dirección: [______________]                    │         │
│  │                                                │         │
│  │              [Crear Complejo]                  │         │
│  └────────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-09: Gestión de Canchas (UC-30A-F)

```
┌─────────────────────────────────────────────────────────────┐
│  MIS CANCHAS (owner)                                        │
│                                                             │
│  [Crear cancha]                                             │
│                                                             │
│  ┌─── Tarjeta Cancha ─────────────────────────┐             │
│  │ 🏟️ Cancha 1                                │             │
│  │ Tipo: [Futbol]  | Jugadores: 10            │             │
│  │ Dirección: Av. San Martín 123              │             │
│  │                                            │             │
│  │ [Editar] [Ver horarios]                    │             │
│  └────────────────────────────────────────────┘             │
│                                                             │
│  ┌─── Modal: Editar Cancha (UC-30B) ─────────────────┐     │
│  │ Información Básica:                                │     │
│  │ Nombre: [________]  Precio: [________]             │     │
│  │ Tipo: [Fútbol ▼]    Jugadores: [________]          │     │
│  │                                                    │     │
│  │ Dirección:                                         │     │
│  │ Calle: [________] Número: [____]                   │     │
│  │ Ciudad: [________] Provincia: [________]            │     │
│  │ CP: [________]     País: [________]                 │     │
│  │                                                    │     │
│  │ [Cancelar]  [¿Está seguro?] → [Actualizar cancha] │     │
│  └────────────────────────────────────────────────────┘     │
│                                                             │
│  ┌─── Modal: Configurar Horarios (UC-30F) ───────────┐     │
│  │ Horarios de Atención — [Nombre cancha]             │     │
│  │                                                    │     │
│  │ Día       │ Estado │ Turno Mañana  │ Turno Tarde   │     │
│  │ Lunes     │ ✅ On  │ 08:00 - 12:00│ 14:00 - 22:00│     │
│  │ Martes    │ ✅ On  │ 08:00 - 12:00│ 14:00 - 22:00│     │
│  │ Miércoles │ ❌ Off │ Cerrado      │ Cerrado      │     │
│  │ ...       │        │              │              │     │
│  │                                                    │     │
│  │ [Cancelar]          [Guardar configuración]        │     │
│  └────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-22: Cupones y Descuentos (UC-27A-E)

```
┌─────────────────────────────────────────────────────────────┐
│  CUPONES Y DESCUENTOS (admin/owner)                         │
│                                                             │
│  [Buscar por código o descripción...] 🔍   [Crear cupón]   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Código │ Descripción │ Descuento│Clientes│Usos│Estado│   │
│  ├────────┼─────────────┼──────────┼────────┼────┼──────│   │
│  │ ABC123 │ 20% off     │ 20%     │ 3      │2/10│🟢Act │   │
│  │ XYZ789 │ $1000 desc  │ $1,000  │ 1      │5/5 │🔴Inac│   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  Acciones: [Editar] [Desactivar/Activar] [Eliminar]        │
│                                                             │
│  ┌─── Modal: Crear Cupón (UC-27A) ──────────────────┐      │
│  │ Tipo: [Porcentaje ▼]  Valor: [________]          │      │
│  │ Descripción: [________________________]          │      │
│  │ Válido desde: [fecha]  Válido hasta: [fecha]     │      │
│  │ Máximo de usos: [________] (opcional)            │      │
│  │                                                   │      │
│  │ ─── Clientes que reciben el cupón ───            │      │
│  │ ☐ Seleccionar todos (5)                          │      │
│  │ ☑ Juan Pérez                                     │      │
│  │ ☑ María López                                    │      │
│  │ ☐ Pedro Sánchez                                  │      │
│  │ [2 cliente(s) seleccionado(s)]                   │      │
│  │                                                   │      │
│  │ [Cancelar]              [Guardar]                │      │
│  └───────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-23: Reporte de Ocupación (UC-25)

```
┌─────────────────────────────────────────────────────────────┐
│  REPORTE DE OCUPACIÓN                                       │
│                                                             │
│  ┌─── Filtros ──────────────────────────────────────────┐   │
│  │ [Hoy] [Esta semana] [Este mes]                       │   │
│  │ Desde: [fecha]  Hasta: [fecha]                       │   │
│  │ Cancha: [Todas las canchas ▼]                        │   │
│  │ Agrupar por: [Cancha ▼]                              │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐                 │
│  │ Total     │ │ Ingresos  │ │ Período   │                 │
│  │ reservas  │ │ totales   │ │           │                 │
│  │    42     │ │ $210,000  │ │ Mar 2026  │                 │
│  └───────────┘ └───────────┘ └───────────┘                 │
│                                                             │
│  ┌─── Tabla de resultados ──────────────────────────────┐   │
│  │ Cancha    │ Reservas │ Horas │ Ocupación │ Ingresos  │   │
│  ├───────────┼──────────┼───────┼───────────┼───────────│   │
│  │ Cancha 1  │    25    │  30h  │   75%     │ $150,000  │   │
│  │ Cancha 2  │    17    │  20h  │   50%     │ $85,000   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-24: Exportar Ingresos (UC-26)

```
┌─────────────────────────────────────────────────────────────┐
│  EXPORTAR INGRESOS                                          │
│                                                             │
│  ┌─── Período ──────────────────────────────────────────┐   │
│  │ ○ Por mes:   Mes: [Marzo ▼]  Año: [2026 ▼]          │   │
│  │ ● Rango de fechas: Desde: [01/03] Hasta: [23/03]    │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐   │
│  │ Registros │ │ Ingresos  │ │Reembolsos │ │ Ingreso   │   │
│  │    42     │ │ brutos    │ │           │ │ neto      │   │
│  │           │ │ $210,000  │ │ $15,000   │ │ $195,000  │   │
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘   │
│                                                             │
│  Se exportarán **42** registros del período seleccionado.   │
│                                                             │
│  [Exportar CSV]  [Exportar PDF]                             │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-25: Reservas del Día — Staff (UC-17)

```
┌─────────────────────────────────────────────────────────────┐
│  RESERVAS DEL DÍA (staff view)                              │
│                                                             │
│  Fecha: [23/03/2026 ▼]                                     │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Hora  │ Cancha   │ Usuario    │ Estado  │Pago│ Acc.  │   │
│  ├───────┼──────────┼────────────┼─────────┼────┼───────│   │
│  │ 08:00 │ Cancha 1 │ Juan P.    │Confirmada│Paid│[Conf]│   │
│  │ 09:00 │ Cancha 1 │ María L.   │Pend.Pago│ — │      │   │
│  │ 10:00 │ Cancha 2 │ Pedro S.   │Cancelada │Refund│    │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  [Conf] = Confirmar asistencia (UC-17)                     │
│  [Reembolsar] = Solo para canceladas con pago (UC-16)      │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-27: Auditoría (UC-31)

```
┌─────────────────────────────────────────────────────────────┐
│  AUDITORÍA (superadmin/owner)                               │
│                                                             │
│  ┌─── Filtros ──────────────────────────────────────────┐   │
│  │ Usuario: [Todos ▼]  Acción: [Todas ▼]               │   │
│  │ Modelo: [Todos ▼]                                    │   │
│  │ Desde: [fecha]  Hasta: [fecha]                       │   │
│  │                          [Limpiar] [Exportar PDF]    │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Fecha       │ Usuario   │ Acción  │ Modelo  │ Desc  │   │
│  ├─────────────┼───────────┼─────────┼─────────┼───────│   │
│  │ 23/03 14:30 │ Juan P.   │🟢Creado │ Reserva │ ...   │   │
│  │ 23/03 14:25 │ Admin     │🔵Actual │ Cancha  │ ...   │   │
│  │ 23/03 14:20 │ María L.  │🟣Login  │ Usuario │ ...   │   │
│  │ 23/03 14:00 │ Juan P.   │🔴Elimin │ Reserva │ ...   │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  Paginación: [1] [2] [3] ... [10]                          │
│                                                             │
│  Nota: Owner solo ve logs de sí mismo, su staff y          │
│  clientes que reservaron en sus canchas.                    │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-12: Mis Puntos (UC-09/UC-11)

```
┌─────────────────────────────────────────────────────────────┐
│  MIS PUNTOS                                                 │
│                                                             │
│  ┌─────────────────────────────────┐                        │
│  │      Saldo actual               │                        │
│  │         75 puntos               │                        │
│  └─────────────────────────────────┘                        │
│                                                             │
│  ┌─── Últimas transacciones ───────────────────────────┐    │
│  │ Tipo      │ Puntos │ Descripción          │ Fecha   │    │
│  ├───────────┼────────┼──────────────────────┼─────────│    │
│  │ 🟢Ganados │ +5     │ Reserva #42 pagada   │ 23/03   │    │
│  │ 🔵Canjeados│ -50   │ Descuento en Res #41 │ 22/03   │    │
│  │ 🟡Revertidos│+50   │ Cancelación Res #40  │ 21/03   │    │
│  │ 🟢Ganados │ +5     │ Reserva #39 pagada   │ 20/03   │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                             │
│  (Últimas 10 transacciones, orden descendente)              │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-28: Promociones (UC-16/UC-27 del PDF)

```
┌─────────────────────────────────────────────────────────────┐
│  PROMOCIONES (admin/owner/staff)                            │
│                                                             │
│  [Buscar por nombre...] 🔍            [Crear promoción]    │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Nombre    │ Tipo       │Descuento│Vigencia   │Estado │   │
│  ├───────────┼────────────┼─────────┼───────────┼───────│   │
│  │ Promo 2x1 │[Combo]     │ 50%     │Mar 1-31   │🟢Activa│  │
│  │ Verano    │[Pts Extra] │ —       │Ene-Feb    │🔴Expir│   │
│  │ Black Fri │[Cupón]     │ 30%     │Nov 24-30  │🔵Progr│   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  Acciones: [Editar] [Desactivar/Activar] [Eliminar]        │
│                                                             │
│  ┌─── Crear/Editar Promoción ────────────────────────┐     │
│  │ Nombre: [______________]                           │     │
│  │ Tipo: [Combo ▼]                                    │     │
│  │ Valor descuento: [________]                        │     │
│  │ Puntos bonus: [________] (solo tipo Pts Extra)     │     │
│  │ Fecha inicio: [fecha]  Fecha fin: [fecha]          │     │
│  │                                                    │     │
│  │ [Cancelar]              [Guardar]                  │     │
│  └────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

---

### UID-11: Gestión de Bloqueos

```
┌─────────────────────────────────────────────────────────────┐
│  BLOQUEOS DE HORARIO (admin/owner)                          │
│                                                             │
│  [Crear bloqueo]                                            │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Cancha   │ Fecha inicio │ Fecha fin │ Motivo │ Acc.  │   │
│  ├──────────┼──────────────┼───────────┼────────┼───────│   │
│  │ Cancha 1 │ 25/03 08:00  │ 25/03 12:00│Mantenim│[Elim]│   │
│  │ Cancha 2 │ 28/03 00:00  │ 29/03 23:59│Evento  │[Elim]│   │
│  └──────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─── Crear Bloqueo ────────────────────────────────┐      │
│  │ Cancha: [Cancha 1 ▼]                             │      │
│  │ Fecha inicio: [fecha + hora]                      │      │
│  │ Fecha fin: [fecha + hora]                         │      │
│  │ Motivo: [________________________]                │      │
│  │                                                   │      │
│  │ [Cancelar]              [Crear bloqueo]           │      │
│  └───────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

---

## 9. Conclusiones y Recomendaciones

### 9.1 Fortalezas
- La cobertura funcional es **muy alta**: 30+ UCs documentados y la gran mayoría implementados
- Los flujos de reserva, pago y fidelización están bien alineados entre documento y código
- El sistema de roles (4 roles) está correctamente implementado con middleware y sidebar condicional
- La auditoría (UC-31) fue agregada correctamente en v3.md y el código

### 9.2 Problemas detectados

1. **Errores de numeración en PDF v1.0**: UC-14 y UC-16 duplicados (corregidos en v3.md)
2. **UIDs incompletos**: El catálogo del PDF tiene 13 UIDs, pero el sistema tiene ~32 pantallas distintas

### 9.3 Recomendaciones
1. Corregir los UC duplicados en el PDF (UC-14 → UC-25, UC-16 → UC-27)
2. Actualizar el catálogo de UIDs para cubrir las ~32 pantallas reales
3. Actualizar CLAUDE.md para reflejar que el reembolso ya usa la API real de MercadoPago (`PaymentRefundClient`)
